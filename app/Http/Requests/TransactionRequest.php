<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    protected $forceJsonResponse = true;

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
            "total_pay" => "required|int",
            "fifty_cop" => "int",
            "hundred_cop" => "int",
            "two_hundred_cop" => "int",
            "five_hundred_cop" => "int",
            "one_thousand_cop" => "int",
            "two_thousand_cop" => "int",
            "five_thousand_cop" => "int",
            "ten_thousand_cop" => "int",
            "twenty_thousand_cop" => "int",
            "fifty_thousand_cop" => "int",
            "one_hundred_thousand_cop" => "int",
        ];
    }

    public function messages()
    {
        return [
            "total_pay.required" => "total_pay es un campo obligatorio",
            "total_pay.int" => "total_pay es debe ser un campo entero",
            "fifty_cop.int" => "el campo fifty_cop debe ser entero",
            "hundred_cop.int" => "el campo hundred_cop debe ser entero",
            "two_hundred_cop.int" => "el campo two_hundred_cop debe ser entero",
            "five_hundred_cop.int" => "el campo five_hundred_cop debe ser entero",
            "one_thousand_cop.int" => "el campo one_thousand_cop debe ser entero",
            "two_thousand_cop.int" => "el campo two_thousand_cop debe ser entero",
            "five_thousand_cop.int" => "el campo five_thousand_cop debe ser entero",
            "ten_thousand_cop.int" => "el campo ten_thousand_cop debe ser entero",
            "twenty_thousand_cop.int" => "el campo twenty_thousand_cop debe ser entero",
            "fifty_thousand_cop.int" => "el campo fifty_thousand_cop debe ser entero",
            "one_hundred_thousand_cop.int" => "el campo one_hundred_thousand_cop debe ser entero",
        ];
    }
}
