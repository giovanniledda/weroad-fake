<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use function fake;
use function now;
use function rand;

class StoreTravelRequest extends FormRequest
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
            'name' => 'required|unique:travels|max:140',
            'description' => 'required',
            'days' => 'required|numeric',
        ];
    }
}
