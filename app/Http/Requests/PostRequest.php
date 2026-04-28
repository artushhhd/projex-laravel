<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all authenticated users to create posts
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|max:255',
            'description' => 'required|string|min:10',
        ];
    }

    /**
     * Custom error messages (Optional)
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a title for your post.',
            'description.min' => 'The description is too short. Tell us more!',
        ];
    }
}