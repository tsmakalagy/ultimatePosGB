<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilRequest extends FormRequest
{
/*
public function messages(){
    return[
        'other_details.required' =>"ce champ est required"
    ];
}*/

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
            
               
               'shipping_address'=>'required',
               'body'     => 'required',

            //'tel'     => 'required|exists:Shippers,tel',
              //  'tel'     => ['required',unique('shippers','tel')],
              
        ];
    }
}
