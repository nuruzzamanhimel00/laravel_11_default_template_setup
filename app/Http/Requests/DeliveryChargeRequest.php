<?php

namespace App\Http\Requests;

use App\Models\InvestorInfo;
use App\Models\InvestorPayment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $deliveryChargeId = $this->route('delivery_charge');
        // dd($deliveryChargeId);
        // dd(request()->all());
        return [
            'unique_key' => [
                'required',
                'uuid',
                Rule::unique('delivery_charges', 'unique_key')->ignore($deliveryChargeId),
            ],
            'title'      => ['required', 'string', 'max:255'],
            'cost'       => ['required', 'numeric', 'min:0'],
            'status'     => ['required', 'in:active,inactive'],
        ];
    }
}
