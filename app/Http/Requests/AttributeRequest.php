<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
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
            'name'           => ['required', 'string', 'max:100', Rule::unique('product_attributes')->ignore($this->attribute)],
            'status'                => 'required',
            'item_data' => 'required|array',
            'item_data.*.value' => 'required|string',
            'item_data.*.color' => 'nullable|string',
            'item_data.*.image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
            'item_data.*.old_image' => ['nullable'],

        ];

        return $rules;
    }
}
