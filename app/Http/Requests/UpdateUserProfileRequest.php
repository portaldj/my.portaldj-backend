<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')
            ],

            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            'profile_types' => 'nullable|array',
            'profile_types.*' => [
                'integer',
                Rule::exists('profile_types', 'id')
            ],

            'skills' => 'nullable|array',
            'skills.*' => [
                'integer',
                Rule::exists('skills', 'id')
            ],

            'music_genres' => 'nullable|array',
            'music_genres.*' => [
                'integer',
                Rule::exists('music_genres', 'id')
            ],
        ];
    }
}
