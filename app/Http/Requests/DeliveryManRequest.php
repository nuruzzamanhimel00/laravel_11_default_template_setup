<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryManRequest extends FormRequest
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
            'email'                 => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->delivery_man)],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'phone'                 => ['nullable', 'max:25', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'avatar'                => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
            // 'role'                  => 'required',
            'type'                  => 'required',
            'status'                => 'required',
            'delivery_men.vehicle_type'                => 'nullable',
            'delivery_men.vehicle_number'                => 'nullable',
            // 'delivery_men.license_number'                => 'nullable',
            // 'delivery_men.identity_number'                => 'nullable',
            'delivery_men.nid_no'                => 'nullable',
            'delivery_men.nid_front'                => 'nullable|max:10240',

            'delivery_men.nid_back'                => 'nullable|max:10240',
            'username'              => ['required', 'string', 'max:100', Rule::unique('users')->ignore($this->delivery_man)],

        ];
        // Conditionally add the unique rule for phone if its length is greater than 4
        if ($this->phone && strlen($this->phone) > 5) {
            $rules['phone'][] = Rule::unique('users')->ignore($this->delivery_man);
        }
        if ($this->delivery_man) {
            $rules['password']              = ['nullable', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['nullable', 'string', 'min:8'];

            if(auth()->user()->id == $this->delivery_man){
                // $rules['role']                  = ['nullable'];
                $rules['type']                  = ['nullable'];
            }
        }

        return $rules;
    }
}
