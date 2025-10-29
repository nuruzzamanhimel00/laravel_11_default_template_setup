<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
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
            'first_name'            => ['required', 'string', 'max:100'],
            'last_name'             => ['nullable', 'string', 'max:100'],
            'email'                 => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->supplier)],
            'phone'                 => ['nullable', 'max:25', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'company'             => ['nullable', 'string', 'max:255'],
            'designation'             => ['nullable', 'string', 'max:500'],
            'address'             => ['nullable', 'string','max:255'],
            'country'             => ['nullable', 'string','max:255'],
            'city'             => ['nullable', 'string','max:255'],
            'zipcode'             => ['nullable', 'string','max:20'],
            'short_address'             => ['nullable', 'string','max:255'],
            'type'                => 'required',
            'status'                => 'required',
            'avatar'                => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],

        ];
        // Conditionally add the unique rule for phone if its length is greater than 4
        if ($this->phone && strlen($this->phone) > 5) {
            $rules['phone'][] = Rule::unique('users')->ignore($this->supplier);
        }

        return $rules;
    }
}
