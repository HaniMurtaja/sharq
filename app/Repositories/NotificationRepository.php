<?php
    namespace App\Repositories;

    use App\Http\Resources\Api\NotificationCollection;
    use App\Http\Resources\Api\NotificationResource;
    use App\Models\UserNotification;
    use App\Traits\HandleResponse;
    use Illuminate\Http\Request;
    use Validator;
    use Auth;


    class NotificationRepository
    {
        use HandleResponse;

        public function __construct()
        {

        }

        public function get_data(Request $request)
        {
            $limit = $request->limit ? $request->limit : 10;
            $notifications = UserNotification::where('user_id', '=', auth()->user()->id)->orderByDesc("id")->paginate($limit);
            return $this->send_response(TRUE, 200, __("auth.Successful", [], $request->header('lang')), new NotificationCollection($notifications));
        }

        public function read_all(Request $request)
        {
            UserNotification::where('user_id', '=', auth()->user()->id)->where('is_read', 0)->update(['is_read' => 1]);
            return $this->send_response(TRUE, 200, __("auth.Successful", [], $request->header('lang')), NULL);
        }

        public function read_one(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:user_notifications',
            ]);
            if($validator->fails())
            {
                return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
            }
            $user_notification = UserNotification::where('id', $request->id)->where('user_id', '=', auth()->user()->id)->first();
            if(!$user_notification)
            {
                return $this->send_response(FALSE, 400, __("auth.NoDataFound", [], $request->header('lang')), NULL);
            }
            $user_notification->is_read = 1;
            $user_notification->save();
            return $this->send_response(TRUE, 200, __("auth.Successful", [], $request->header('lang')), new NotificationResource($user_notification));
        }

        public function save($id, $title, $body, $redirect_id = NULL, $click = NULL, $icon = NULL)
        {
            // dd(99);
//            $notification = new UserNotification();
//            $notification->user_id = $id;
//            $notification->title = $title;
//            $notification->body = $body;
//            $notification->redirect_id = $redirect_id;
//            $notification->click_action = $click;
//            $icon = $icon ? $icon : 'icons/general.png';
//            $notification->icon = $click ? 'icons/' . $click . '.png' : $icon;
//            $notification->save();
        }
    }
