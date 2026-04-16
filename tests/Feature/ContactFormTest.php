<?php

namespace Tests\Feature;

use App\Mail\ContactFormSubmitted;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    public function test_it_sends_a_contact_form_email(): void
    {
        Mail::fake();

        config()->set('contact.recipient.address', 'bookings@example.com');

        $response = $this->postJson('/api/contact', [
            'name' => 'Jordan Example',
            'email' => 'jordan@example.com',
            'contactNumber' => '07123 456789',
            'service' => 'Wedding - Gold Package',
            'message' => 'We are planning a ceremony in September.',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Thanks, your message has been sent.',
            ]);

        Mail::assertSent(ContactFormSubmitted::class, function (ContactFormSubmitted $mail) {
            return $mail->hasTo('bookings@example.com')
                && $mail->submission['name'] === 'Jordan Example'
                && $mail->submission['service'] === 'Wedding - Gold Package';
        });
    }

    public function test_it_validates_required_contact_fields(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        Mail::assertNothingSent();
    }
}