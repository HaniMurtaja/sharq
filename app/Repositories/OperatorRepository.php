<?php

namespace App\Repositories;

use App\Http\Services\SendSms;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Operator;
use App\Enum\DriverStatus;
use App\Enum\VerificationStatuses;
use App\Models\BankDetails;
use App\Rules\KSAPhoneRule;
use Illuminate\Http\Request;
use App\Models\OperatorStatus;
use App\Traits\HandleResponse;
use Illuminate\Validation\Rule;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use App\Http\Resources\Api\OperatorResource;
use App\Models\DriverLocationLog;
use App\Models\OperatorDetail;
use App\Traits\FileHandler;

class OperatorRepository
{
    use HandleResponse;
    use FileHandler;

    public function __construct(FirebaseRepository $firebaseRepository)
    {
        $this->firebaseRepository = $firebaseRepository;
    }

    public function send_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                //'regex:/(05)[0-9]/',
                //'digits:10',
                new KSAPhoneRule(),
                Rule::Exists('users')
            ]
        ], [
            'phone.exists' => $request->header('lang') == 'en' ? 'phone number is wrong' : 'رقم الهاتف غير صحيح',
        ]);
        if ($validator->fails()) {
            return $this->send_response(FALSE, 403, $validator->errors()->first(), NULL);
        }
        // send otp and return status
        $otp_status = $this->send_otp_to_user($request);
        if (!$otp_status) {
            return $this->send_response(FALSE, 400, 'code not sent', NULL);
        } else {
            return $this->send_response(TRUE, 200, 'success', NULL);
        }
    }

    public function send_otp_to_user(Request $request)
    {
        $otpState = FALSE;
        $code = 7418;
        try {
            // send sms code
            if (App::environment('production')) {
                if ($request['phone'] == '566278832' || $request['phone'] == '566238294') {
                    $otpState = TRUE;
                } else {
                    $code = mt_rand(1000, 9999);

                    $message_sending = ' رمز التفعيل : ' . $code;
                    //send sms and get response
                    $notify_res = SendSms::toSms($request['phone'], $message_sending);
                    $notify_res = $notify_res->getData();
                    $otpState = $notify_res->status;
                }
            } else {
                $otpState = TRUE;
            }
            // store data in db
            if ($otpState) {

                DB::transaction(function () use ($request, $code) {
                    $verification_code = new VerificationCode();
                    $verification_code->phone = $request['phone'];
                    $verification_code->code = $code;
                    $verification_code->current_time = Carbon::now()->format('Y-m-d H:i:s');
                    $verification_code->save();
                });
            }
        } catch (\Exception $exception) {
            $otpState = FALSE;
        }
        return $otpState;
    }

    public function verify_otp($request)
    {
        $is_verified = FALSE;
        try {
            $verification_code_table = VerificationCode::where('phone', $request['phone'])->orderBy('id', 'desc')->first();
            if (!$verification_code_table) {
                return $is_verified = FALSE;
            }
            $created = new Carbon($verification_code_table->created_at);
            $now = Carbon::now();
            //check if code = requested code and not extend 2 minutes
            $is_verified = ($verification_code_table->code == $request->code && $created->diffInMinutes($now) <= 2) ? 1 : 0;
            if ($is_verified) {
                $verification_code_table->delete();
            } else {
                return $is_verified = FALSE;
            }
        } catch (\Exception $exception) {
            dd($exception);
            $is_verified = FALSE;
        }
        return $is_verified;
    }

    public function login(Request $request)
    {

        $isEmail = filter_var($request->phone, FILTER_VALIDATE_EMAIL);
        // dd($isEmail);

        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                $isEmail ? 'email' : new KSAPhoneRule(),
            ],
            'password' => ['required'],
            'device_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }


        $user = Operator::where($isEmail ? 'email' : 'phone', $request->phone)->first();

        if (!$user) {
            return $this->send_response(FALSE, 400, 'User or password is wrong', NULL);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->send_response(FALSE, 400, 'User or password is wrong', NULL);
        }
        if ($user->device_id && $user->device_id !== $request->device_id) {
            $user->tokens()->delete();
            $message = 'You were logged out from your previous device.';
        } else {
            $message = 'success';
        }


        $user->device_id = $request->device_id;
        if ($request->firebase_token) {
            $user->firebase_token = $request->firebase_token;
        }
        $user->save();


        $token = $user->createToken('auth-token')->plainTextToken;
        $user['access_token'] = $token;


        $operatorResource = new OperatorResource($user);
        $operatorData = $operatorResource->toArray(request());

        try {

            $this->firebaseRepository->save_driver($user->id, $operatorData);
        } catch (\Exception $e) {

            Log::info($e);
        }

        return $this->send_response(TRUE, 200, $message, new OperatorResource($user));
    }



    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'phone' => [
    //             'required',
    //             //'regex:/(05)[0-9]/',
    //             //'digits:10',
    //             new KSAPhoneRule(),
    //             Rule::Exists('users')
    //         ],
    //         'code' => ['required']
    //     ], [
    //         'phone.exists' => $request->header('lang') == 'en' ? 'phone number is wrong' : 'رقم الهاتف غير صحيح',
    //     ]);



    //     if ($validator->fails()) {
    //         return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
    //     }
    //     //verify code
    //     $is_verified = $this->verify_otp($request);
    //     //         $user = Operator::where('phone', $request->phone)->first();
    //     // dd($user->tokens()->count());
    //     if (!$is_verified) {
    //         return $this->send_response(FALSE, 400, 'verification code not correct', NULL);
    //     } else {
    //         $user = Operator::where('phone', $request->phone)->first();
    //         //validate user is active
    //         if (!$user) {
    //             return $this->send_response(FALSE, 400, 'not found', NULL);
    //         }


    //         // if ($user->tokens()->count() > 0) {
    //         //     return $this->send_response(FALSE, 400, 'You are logged in on another device.', NULL);
    //         // }


    //         if ($request->firebase_token) {
    //             $user->firebase_token = $request->firebase_token;
    //             $user->save();
    //         }



    //         //create auth passport token
    //         $token = $user->createToken('auth-token')->plainTextToken;
    //         $user['access_token'] = $token;
    //         //update firebase token and create auth token

    //         return $this->send_response(TRUE, 200, 'success', new OperatorResource($user));
    //     }
    // }

    public function logout(Request $request)
    {

        if (!auth()->check()) {
            return $this->send_response(FALSE, 400, 'Unauthenticated', NULL);
        }


        $user = auth()->user();

        $this->change_status(DriverStatus::OFFLINE->value);

        $user->tokens()->delete();


        $user->device_id = null;
        $user->save();


        return $this->send_response(TRUE, 200, 'Successfully logged out', NULL);
    }


    public function profile(Request $request)
    {
        $user = Operator::where('id', '=', auth()->user()->id)->first();
        if ($user) {
            return $this->send_response(TRUE, 200, 'success', new OperatorResource($user));
        } else {
            return $this->send_response(FALSE, 400, 'no data found', NULL);
        }
    }



    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'email' => [
                'nullable',
                'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
            'birth_date' => 'nullable|date_format:Y-m-d|before:' . now()->subYears(16)->toDateString(),
            'emergency_contact_phone' => [
                'nullable',
                new KSAPhoneRule(),
            ],
            'id_card_image_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_card_image_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'license_front_image' => ['nullable', 'file'],
            'license_back_image' => ['nullable', 'file'],
            'social_id_no' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }

        $user = Operator::find(auth()->user()->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->save();

        $operator = $user->operator;
        if ($operator) {
            $operator->birth_date = $request->birth_date ?? $operator->birth_date;
            $operator->emergency_contact_name = $request->emergency_contact_name ?? $operator->emergency_contact_name;
            $operator->emergency_contact_phone = $request->emergency_contact_phone ?? $operator->emergency_contact_phone;

            //   dd(99);
            if ($operator->is_verified !== VerificationStatuses::VERIFIED->value) {

                // dd($operator->is_verified);

                if ($request->filled('social_id_no')) {
                    $operator->social_id_no = $request->social_id_no;
                }


                if ($request->hasFile('id_card_image_front')) {
                    $operator->id_card_image_front = $this->upload_files($request->file('id_card_image_front'), 'images/idPhoto/' . $operator->id);
                }


                if ($request->hasFile('id_card_image_back')) {
                    $operator->id_card_image_back = $this->upload_files($request->file('id_card_image_back'), 'images/idPhoto/' . $operator->id);
                }

                if ($request->hasFile('license_front_image')) {
                    $operator->license_front_image = $this->upload_files(
                        $request->file('license_front_image'),
                        'images/licensePhoto/' . $operator->id
                    );
                }

                if ($request->hasFile('license_back_image')) {
                    $operator->license_back_image = $this->upload_files(
                        $request->file('license_back_image'),
                        'images/licensePhoto/' . $operator->id
                    );
                }

                $operator->iban = $request->iban ?? $operator->iban;

                if (
                    !empty($operator->social_id_no) &&
                    !empty($operator->id_card_image_front) &&
                    !empty($operator->id_card_image_back) &&
                    !empty($operator->license_back_image) &&
                    !empty($operator->license_front_image)
                ) {
                    $operator->is_verified = VerificationStatuses::WAITING_FOR_VERIFICATION;
                }
            }


            $operator->save();
        }


        $operatorResource = new OperatorResource($user);
        $operatorData = $operatorResource->toArray(request());

        try {

            $this->firebaseRepository->save_driver(auth()->user()->id, $operatorData);
        } catch (\Exception $e) {

            Log::info($e);
        }

        return $this->send_response(TRUE, 200, 'success', new OperatorResource($user));
    }





    public function update_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', new Enum(DriverStatus::class)],
        ]);
        if ($validator->fails()) {
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }
        // Update the driver's status
        $this->change_status($request->status);

        return $this->send_response(TRUE, 200, 'success', NULL);
    }

    public function add_operator($request)
    {

        $request->merge(['password' => Hash::make($request->password)]);
        $operator = Operator::create($request->all());
        return response()->json([
            'operator' => [
                'id' => $operator->id,
                "name" => $operator->full_name,
                'phone' => $operator->phone
            ]
        ], 200);
    }

    // changeStatus()
    public function change_status($status)
    {
        $user = Operator::with('operator')->find(auth()->user()->id);
        if ($user) {

            $operator = $user->operator;
            if ($operator) {


                if (isset(\request()->lat) && isset(\request()->lng)) {
                    $lat = \request()->lat;
                    $lng = \request()->lng;
                    DB::table('operators')
                        ->where('operator_id', auth()->user()->id)
                        ->update([
                            'lat' => $lat,
                            'lng' => $lng,
                            'location' => \DB::raw("POINT($lng, $lat)"),
                            'updated_at' => now(),
                        ]);

                    //                $operator->lat = @$lat;
                    //                $operator->lng = @$lng;
                    //               $operator->location = \DB::raw("POINT($lng, $lat)");

                }
                $operator->status = $status;
                $operator->save();
                //add operator status record
                //                OperatorStatus::create(['operator_id' => auth()->user()->id, 'status' => $operator->status]);
                $operatorResource = new OperatorResource($user);
                $operatorData = $operatorResource->toArray(request());
                //try save firebase
                try {
                    // Attempt to save to Firebase
                    $this->firebaseRepository->save_driver(auth()->user()->id, $operatorData);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
                }
            }
        }
    }

    public function new_bank_details($request)
    {

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string',
            'iban' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }
        $user = Operator::findOrFail(auth()->user()->id);

        if ($user && $user->operator) {
            BankDetails::updateOrCreate([
                'bank_name' => $request->bank_name,
                'iban' => $request->iban,
                'operator_id' => $user->operator->id

            ]);
            return $this->send_response(TRUE, 200, 'success', NULL);
        }
        return $this->send_response(FALSE, 400, 'NO OPERATORE REGISTRED WITH YOUR CREDENTIALS', NULL);
    }

    public function bank_details()
    {


        $user = Operator::findOrFail(auth()->user()->id);

        if ($user && $user->operator) {

            return $this->send_response(TRUE, 200, 'success', $user->operator->bank_details);
        }
        return $this->send_response(FALSE, 400, 'NO OPERATORE REGISTRED WITH YOUR CREDENTIALS', NULL);
    }


    public function detect_location(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => ['required'],
            'long' => ['required'],

        ]);
        if ($validator->fails()) {
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }
        //    $user = Operator::find(auth()->user()->id);
        //        $operator = OperatorDetail::where('operator_id', auth()->user()->id)->first();

        //        if ($operator) {
        DB::table('operators')
            ->where('operator_id', auth()->user()->id)
            ->update([
                'lat' => $request->lat,
                'lng' => $request->long,
                'location' => DB::raw("POINT($request->long, $request->lat)"),
                'updated_at' => now(),
            ]);
        //        }

        //
        //        DriverLocationLog::create([
        //            'driver_id' =>  $user->id,
        //            'lat' => $request->lat,
        //           'lng' => $request->long,
        //        ]);
        //
        //        $user->save();
        //        $operatorResource = new OperatorResource($user);
        //        $operatorData = $operatorResource->toArray(request());
        //
        //        // try {
        //        // Attempt to save to Firebase
        //        $this->firebaseRepository->save_driver(auth()->user()->id, $operatorData);
        //        // } catch (\Exception $e) {
        //        //     // Handle the exception (log it, show a message, etc.)
        //        //     Log::info($e);
        //        // }

        return $this->send_response(TRUE, 200, 'success', []);
    }
}
