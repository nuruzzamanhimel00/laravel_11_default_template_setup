<?php

namespace App\Http\Requests;

use App\Models\InvestorInfo;
use App\Models\InvestorPayment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
        $rules = [
            'name'            => ['required', 'string', 'max:100',  Rule::unique('brands')->ignore($this->brand)],
            'status'                => 'required',
            'image' => ['nullable','mimes:jpeg,jpg,png', 'max:10240'],

        ];
        return $rules;
    }
}
