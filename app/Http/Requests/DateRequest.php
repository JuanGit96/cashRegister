<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DateRequest extends FormRequest
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
            "day" => "required|integer|min:1",
            "month" => "required|integer|min:1",
            "year" => "required|integer|min:2000",
            "hours" => "required|integer|max:23",
            "minutes" => "required|integer|max:59",
            "seconds" => "required|integer|max:59"
        ];
    }

    public function messages()
    {
        return [
            "day.integer" => "El campo day debe ser entero",
            "month.integer" => "El campo month debe ser entero",
            "year.integer" => "el campo year debe ser entero",
            "hours.integer" => "el campo hours debe ser entero",
            "minutes.integer" => "el campo minutes debe ser entero",
            "seconds.integer" => "el campo seconds debe ser entero",
            "day.required" => "El campo day es obligatorio",
            "month.required" => "El campo month es obligatorio",
            "year.required" => "el campo year es obligatorio",
            "hours.required" => "el campo hours es obligatorio",
            "minutes.required" => "el campo minutes es obligatorio",
            "seconds.required" => "el campo seconds es obligatorio",
            "day.min" => "El campo day debe ser minimo de 1",
            "month.min" => "El campo month debe ser minimo de 1",
            "year.min" => "el campo year debe ser minimo de 2000",
            "hours.max" => "el campo hours debe ser maximo de 23",
            "minutes.max" => "el campo minutes debe ser maximo de 59",
            "seconds.max" => "el campo seconds debe ser maximo de 59",
        ];
    }
}
