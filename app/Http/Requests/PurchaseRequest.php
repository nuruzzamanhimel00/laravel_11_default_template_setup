<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
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
            'supplier_id'    => ['required', 'exists:users,id'], // Ensure supplier_id exists in users table
            'warehouse_id'    => ['required', 'exists:warehouses,id'], // Ensure brand_id exists in brands table
            'company'         => ['nullable','max:100'],
            'date'         => ['required','date'],
            'address'         => ['nullable','max:255'],
            'country'         => ['nullable','max:100'],
            'city'         => ['nullable','max:100'],
            'zipcode'         => ['nullable','max:20'],
            'short_address'         => ['nullable','max:255'],
            'notes'         => ['nullable','max:500'],


            'purchase_items'         => ['required'],
            'purchase_items.*.product_id'         => ['required', 'exists:products,id'],
            'purchase_items.*.warehouse_id'         => ['required', 'exists:warehouses,id'],
            'purchase_items.*.quantity'         => ['required', 'numeric'],
            'purchase_items.*.price'         => ['required', 'numeric'],
            'purchase_items.*.sale_price'         => ['required', 'numeric'],
            'purchase_items.*.restaurant_sale_price'         => ['required', 'numeric'],
            'purchase_items.*.notes'         => ['nullable'],
            'purchase_items.*.sub_total'         => ['required', 'numeric'],
            'purchase_items.*.data'         => ['required'],

        ];



        return $rules;
    }
}
