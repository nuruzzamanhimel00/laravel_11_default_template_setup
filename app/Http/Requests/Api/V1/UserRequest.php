<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $request = request();
        $user_id = $this->user_id ?? auth()->id();

        $isUpdate = $request->method() === 'PUT' || $request->method() === 'PATCH';

        $rules = [
            'first_name'            => ['required', 'string', 'max:100'],
            'last_name'             => ['nullable', 'string', 'max:100'],
            'email'                 => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user_id)
            ],
            'password'              => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8'],
            'phone'                 => ['nullable', 'max:25', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'avatar'                => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
            'type'                  => ['required'],
            'status'                => ['nullable'],
        ];

        // Add unique rule for phone if length is > 5
        if (!empty($this->phone) && strlen($this->phone) > 5) {
            $rules['phone'][] = Rule::unique('users')->ignore($user_id);
        }

        // Adjust rules if the authenticated user is updating their own profile
        if (auth()->id() == $user_id) {
            $rules['type'] = ['nullable'];
        }

        return $rules;
    }
}
