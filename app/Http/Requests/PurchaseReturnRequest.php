<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseReturnRequest extends FormRequest
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
            'return_date'         => ['required','date'],
            'note'         => ['required'],
            'purchase_return'      => 'required',
            'purchase_return.*.product_id'         => ['required', 'exists:products,id'],
            'purchase_return.*.purchase_item_id'         => ['required', 'exists:purchase_items,id'],
            'purchase_return.*.product_variant_id'         => ['nullable', 'exists:product_variants,id'],
            'purchase_return.*.warehouse_id'         => ['required', 'exists:warehouses,id'],
            'purchase_return.*.return_quantity'         => ['nullable','numeric'],
            'purchase_return.*.return_price'         => ['required', 'numeric','regex:/^\d+(\.\d{1,2})?$/'],
            'purchase_return.*.return_sub_total'         => ['nullable', 'numeric','regex:/^\d+(\.\d{1,2})?$/'],
        ];



        return $rules;
    }
}
