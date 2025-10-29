<?php

namespace App\Http\Requests;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
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
            'target_type' => ['required', 'string', Rule::in([User::TYPE_RESTAURANT, User::TYPE_REGULAR_USER])],
            'title' => ['required', 'string', 'max:250'],
            'message' => ['required', 'string', 'max:65535'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['required'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
            'applied_for' => ['required', 'string', Rule::in(Promotion::APPLICABLE_TYPES)],
            'applied_for_ids' => ['required', 'array'],
            'offer_type' => ['required', 'string', Rule::in(Promotion::OFFER_TYPES)],
            'offer_value' => ['required', 'min:1'],
            'in_homepage' => ['nullable'],
        ];

        // Dynamically validate that each ID exists in the corresponding table
        if ($this->applied_for == Promotion::APPLICABLE_PRODUCTS) {
            $rules['applied_for_ids.*'] = ['required','integer', 'exists:products,id'];
        } elseif ($this->applied_for == Promotion::APPLICABLE_CATEGORIES) {
            $rules['applied_for_ids.*'] = ['required','integer', 'exists:categories,id'];
        }

        return $rules;
    }

}
