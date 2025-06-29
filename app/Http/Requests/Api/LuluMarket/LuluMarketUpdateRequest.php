<?php
namespace App\Http\Requests\Api\LuluMarket;

use Illuminate\Foundation\Http\FormRequest;

class LuluMarketUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'task_id'                          => 'required|string',
            'event_type'                       => 'required|string',
            'job_number'                       => 'nullable|string',
            'client_id'                        => 'nullable|string',
            'created_at'                       => 'nullable|date',
            'shopper_web_url'                  => 'nullable|string',
            'job_comment'                      => 'nullable|string',
            'round_trip_info'                  => 'nullable|array',
            'round_trip_info.enabled'          => 'nullable|boolean',
            'round_trip_info.reason'           => 'nullable|string',
            'fleet_info'                       => 'nullable|array',
            'fleet_info.id'                    => 'nullable|string',
            'fleet_info.name'                  => 'nullable|string',
            'recipient'                        => 'nullable|array',
            'recipient.name'                   => 'nullable|string',
            'recipient.email'                  => 'nullable|string',
            'recipient.phone_number'           => 'nullable|string',
            'origin'                           => 'nullable|array',
            'origin.name'                      => 'nullable|string',
            'origin.address'                   => 'nullable|string',
            'origin.address_two'               => 'nullable|string',
            'origin.description'               => 'nullable|string',
            'origin.country'                   => 'nullable|string',
            'origin.city'                      => 'nullable|string',
            'origin.state'                     => 'nullable|string',
            'origin.zip_code'                  => 'nullable|string',
            'origin.latitude'                  => 'nullable|numeric',
            'origin.longitude'                 => 'nullable|numeric',
            'origin.store_reference'           => 'nullable|string',
            'destination'                      => 'nullable|array',
            'destination.name'                 => 'nullable|string',
            'destination.address'              => 'nullable|string',
            'destination.address_two'          => 'nullable|string',
            'destination.description'          => 'nullable|string',
            'destination.country'              => 'nullable|string',
            'destination.city'                 => 'nullable|string',
            'destination.state'                => 'nullable|string',
            'destination.zip_code'             => 'nullable|string',
            'destination.latitude'             => 'nullable|numeric',
            'destination.longitude'            => 'nullable|numeric',
            'payment_info'                     => 'nullable|array',
            'payment_info.currency_code'       => 'nullable|string',
            'payment_info.prices'              => 'nullable|array',
            'payment_info.prices.shipping_fee' => 'nullable|numeric',
            'payment_info.prices.order_value'  => 'nullable|numeric',
            'payment_info.payment'             => 'nullable|array',
            'payment_info.payment.method'      => 'nullable|string',
            'slot_info'                        => 'nullable|array',
            'slot_info.from'                   => 'nullable|date',
            'slot_info.to'                     => 'nullable|date',
            'slot_info.reason'                 => 'nullable|string',
            'packages_quantity'                => 'nullable|numeric',
        ];
    }
}
