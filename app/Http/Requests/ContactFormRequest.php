<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array{name: string, email: string, contactNumber: ?string, service: ?string, message: string}
     */
    public function contactSubmission(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contactNumber' => $validated['contactNumber'] ?: null,
            'service' => $validated['service'] ?: null,
            'message' => $validated['message'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'contactNumber' => ['nullable', 'string', 'max:50'],
            'service' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}