<?php

namespace App\Http\Requests\Product;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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

        $rules = [
            'name'             => ['string', 'required', Rule::unique('products', 'name')->ignore($this->product)->withoutTrashed()],
            'sku'              => ['string', 'nullable', Rule::unique('products', 'sku')->ignore($this->product)->withoutTrashed()],
            'barcode'          => 'string|nullable',
            'category_id'      => 'required|exists:categories,id',
            'brand_id'         => 'nullable|exists:brands,id',
            'product_unit_id'  => 'nullable|exists:product_units,id',
            'unit_value'       => 'numeric|nullable',
            'taxes.has_tax'    => 'boolean|nullable',
            'taxes.tax_amount' => 'numeric|nullable',
            'barcode_image'    => 'string|nullable',
            'description'      => 'string|nullable',
            'notes'            => 'string|nullable',
            'image'            => 'file|nullable',
            'details_image'    => 'file|nullable',
            'status'           => 'required|in:active,inactive,deleted',
            'is_split_sale'    => 'boolean|nullable',
            'available_for'    => 'required|in:Both,Customer,Restaurant,DeliveryMan,All',
            'product_tags'     => 'nullable|sometimes|array',
            'low_stock_alert'  => 'required|integer',
        ];
        return $rules;
    }
}
