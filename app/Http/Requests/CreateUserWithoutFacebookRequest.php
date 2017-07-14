<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateUserWithoutFacebookRequest extends Request
{
    protected $rules = [
        'facebook_id' => 'null',
        'name'        => 'required|string|min:4',
        'email'       => 'required|unique:users|string',
        'gender'      => 'required|string',
        'password'    => 'required|string|min:6'
    ];

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
        return $this->rules;
    }
}
