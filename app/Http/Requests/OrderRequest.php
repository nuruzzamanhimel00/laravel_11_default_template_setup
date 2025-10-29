<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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



    public function rules()
    {
        // dd(request()->all());
        $isWalkInCustomer = request()->walk_in_customer === 'true';
        $userId = request()->order_for_id ?? null;

        return [
            'is_split_sale'                 => ['required'],
            'billing_info'                 => ['required'],
            'shipping_info'                => ['required'],
            'order_for'                    => ['required'],
            'order_for_id'                => $isWalkInCustomer ? ['nullable'] : ['required', 'exists:users,id'],

            'customer'                    => $isWalkInCustomer ? ['required'] : ['nullable'],
            'customer.name'              => $isWalkInCustomer ? ['required'] : ['nullable'],
            'customer.phone'             => $isWalkInCustomer
                                            ? ['required', Rule::unique('users', 'phone')->ignore($userId)]
                                            : ['nullable', Rule::unique('users', 'phone')->ignore($userId)],
            'customer.email'             => $isWalkInCustomer
                                            ? ['required', Rule::unique('users', 'email')->ignore($userId)]
                                            : ['nullable', Rule::unique('users', 'email')->ignore($userId)],

            'walk_in_customer'            => ['nullable'],
            'date'                        => ['required', 'date'],

            'total_paid'                  => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_info.account_no'     => ['nullable', 'string'],
            'payment_info.transaction_no' => ['nullable', 'string'],
            'payment_info.date'           => ['nullable', 'date'],
            'payment_info.notes'          => ['nullable'],

            'sub_total'                   => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'global_discount'             => ['nullable'],
            'global_discount_type'        => ['nullable'],
            'tax_amount'                  => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'discount_amount'             => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'total'                       => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_type'                => ['required'],

            'sale_items'                  => ['required', 'array'],
            'sale_items.*.product_id'     => ['required', 'exists:products,id'],
            'sale_items.*.variant_id'     => ['nullable', 'exists:product_variants,id'],
            'sale_items.*.warehouse_stock_id'     => ['nullable', 'exists:warehouse_stocks,id'],
            'sale_items.*.product_name'   => ['required'],
            'sale_items.*.product_sku'    => ['nullable'],
            'sale_items.*.product_barcode'=> ['nullable'],
            'sale_items.*.price'          => ['required', 'numeric'],
            'sale_items.*.quantity'       => ['required', 'numeric'],
            'sale_items.*.discount'       => ['nullable', 'numeric'],
            'sale_items.*.discount_type'  => ['nullable'],
            'sale_items.*.sub_total'      => ['required', 'numeric'],
            'sale_items.*.data'  => ['nullable'],
        ];
    }

}
