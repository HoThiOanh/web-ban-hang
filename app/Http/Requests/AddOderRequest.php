<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'first_name'=> 'nullable|required',
            'last_name'=> 'nullable|required',
            'country'=> 'nullable|required',
            'street_address'=> 'nullable|required',
            'town_city'=> 'nullable|required',
            'phone'=> 'nullable|required|numeric',
            'email'=> 'nullable|required|email',
        ];
    }


}
