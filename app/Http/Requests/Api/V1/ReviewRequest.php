<?php

namespace App\Http\Requests\Api\V1;

use App\Models\InvestorInfo;
use App\Models\InvestorPayment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
        return [
            'rating'      => ['required', 'numeric', 'min:1', 'max:5'],
            'message'     => ['required', 'string'],
            // 'user_id'     => ['required', 'exists:users,id'],
            'product_id'  => ['required', 'exists:products,id'],

            // for multiple images
            'images'      => ['nullable', 'array'],
            'images.*.image'    => ['nullable','image', 'mimes:jpeg,jpg,png', 'max:10240'], // max 10MB per image
        ];
    }

}
