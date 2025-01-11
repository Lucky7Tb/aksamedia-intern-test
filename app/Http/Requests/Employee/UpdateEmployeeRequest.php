<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => ['file', 'image', 'mimes:png,jpg,jpeg', 'mimetypes:image/jpeg,image/jpg,image/png'],
            'name' => ['string'],
            'phone' => ['numeric', 'max_digits:15'],
            'division' => ['string', 'uuid', 'exists:divisions,id'],
            'position' => ['string'],
        ];
    }
}
