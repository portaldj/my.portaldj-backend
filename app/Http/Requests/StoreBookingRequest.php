<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
            'dj_user_id' => ['required', 'integer', Rule::exists('users', 'id'), Rule::notIn([auth()->id()])],
            'event_details' => 'required|string|max:255',
            'requested_date' => 'required|date|after:now',
        ];
    }
}
