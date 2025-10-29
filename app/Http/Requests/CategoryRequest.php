<?php

namespace App\Http\Requests;

use App\Models\InvestorInfo;
use App\Models\InvestorPayment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        return [
            'parent_id'   => ['nullable'],
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($this->category)],
            'description' => ['nullable', 'max:500'],
            'status'      => 'required',
            'image'       => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ];
    }
}
