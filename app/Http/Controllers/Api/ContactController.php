<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormSubmitted;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController
{
    public function store(ContactFormRequest $request): JsonResponse
    {
        $contactSubmission = $request->contactSubmission();

        Mail::to(config('contact.recipient.address'))
            ->send(new ContactFormSubmitted($contactSubmission));

        return response()->json([
            'message' => 'Thanks, your message has been sent.',
        ]);
    }
}