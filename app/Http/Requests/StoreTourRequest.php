<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:tours|max:140',
            'startingDate' => 'required|date|after:today',
            'endingDate' => 'sometimes|date|after:startingDate',
            'price' => 'required|numeric|gt:0',
        ];
    }
}
