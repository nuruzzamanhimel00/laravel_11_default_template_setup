<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdministrationRequest extends FormRequest
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
            'first_name'            => ['required', 'string', 'max:100'],
            'last_name'             => ['nullable', 'string', 'max:100'],
            'email'                 => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->administration)],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'phone'                 => ['nullable', 'max:25', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'avatar'                => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
            'role'                  => 'required',
            'type'                  => 'required',
            'status'                => 'required'
        ];
        // Conditionally add the unique rule for phone if its length is greater than 4
        if ($this->phone && strlen($this->phone) > 5) {
            $rules['phone'][] = Rule::unique('users')->ignore($this->administration);
        }
        if ($this->administration) {
            $rules['password']              = ['nullable', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['nullable', 'string', 'min:8'];

            if(auth()->user()->id == $this->administration){
                $rules['role']                  = ['nullable'];
                $rules['type']                  = ['nullable'];
            }
        }

        // dd($rules);
        return $rules;
    }
}
