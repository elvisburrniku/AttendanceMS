<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRec extends FormRequest
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
            'first_name' => 'required|string|min:3|max:64|alpha_dash',
            'last_name' => 'required|string|min:3|max:64|alpha_dash',
            'department' => 'required',
            'position' => 'required',
            'area' => 'required',
        ];
    }
}
