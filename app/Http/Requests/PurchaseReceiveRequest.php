<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseReceiveRequest extends FormRequest
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
        // dd(request()->all());

        $rules = [
            'receive_date'         => ['required','date'],
            'purchase_receive'      => 'required',
            'purchase_receive.*.product_id'         => ['required', 'exists:products,id'],
            'purchase_receive.*.purchase_item_id'         => ['required', 'exists:purchase_items,id'],
            'purchase_receive.*.product_variant_id'         => ['nullable', 'exists:product_variants,id'],
            'purchase_receive.*.receive_quantity'         => ['nullable','numeric'],
            'purchase_receive.*.receive_price'         => ['required', 'numeric', 'gt:0'],
            'purchase_receive.*.receive_sale_price'         => ['required', 'numeric', 'gt:0'],
            'purchase_receive.*.receive_restaurant_sale_price'         => ['required', 'numeric', 'gt:0'],
            'purchase_receive.*.receive_sub_total'         => ['nullable', 'numeric','gt:-1'],
        ];



        return $rules;
    }
}
