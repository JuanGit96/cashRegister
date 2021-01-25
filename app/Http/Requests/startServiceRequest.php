<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class startServiceRequest extends FormRequest
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
            "fifty_cop" => "integer|min:1",
            "hundred_cop" => "integer|min:1",
            "two_hundred_cop" => "integer|min:1",
            "five_hundred_cop" => "integer|min:1",
            "one_thousand_cop" => "integer|min:1",
            "two_thousand_cop" => "integer|min:1",
            "five_thousand_cop" => "integer|min:1",
            "ten_thousand_cop" => "integer|min:1",
            "twenty_thousand_cop" => "integer|min:1",
            "fifty_thousand_cop" => "integer|min:1",
            "one_hundred_thousand_cop" => "integer|min:1",
        ];
    }

    public function messages()
    {
        return [
            "fifty_cop.integer" => "el campo fifty_cop debe ser entero",
            "hundred_cop.integer" => "el campo hundred_cop debe ser entero",
            "two_hundred_cop.integer" => "el campo two_hundred_cop debe ser entero",
            "five_hundred_cop.integer" => "el campo five_hundred_cop debe ser entero",
            "one_thousand_cop.integer" => "el campo one_thousand_cop debe ser entero",
            "two_thousand_cop.integer" => "el campo two_thousand_cop debe ser entero",
            "five_thousand_cop.integer" => "el campo five_thousand_cop debe ser entero",
            "ten_thousand_cop.integer" => "el campo ten_thousand_cop debe ser entero",
            "twenty_thousand_cop.integer" => "el campo twenty_thousand_cop debe ser entero",
            "fifty_thousand_cop.integer" => "el campo fifty_thousand_cop debe ser entero",
            "one_hundred_thousand_cop.integer" => "el campo one_hundred_thousand_cop debe ser entero",
        ];
    }
}
