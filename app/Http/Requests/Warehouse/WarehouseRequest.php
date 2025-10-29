<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
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
            'name'         => 'required|string|max:255',
            'code'         => 'nullable|string|max:50|unique:warehouses,code,' . $this->warehouse,
            'address'      => 'nullable|string|max:500',
            'phone'        => 'nullable|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
            'full_phone'   => 'nullable|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
            'company_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'contact'      => 'nullable|string|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'is_default'   => 'boolean',
            'status'       => 'nullable|string|max:50',
        ];
    }
}
