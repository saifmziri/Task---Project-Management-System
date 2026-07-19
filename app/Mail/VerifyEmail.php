<?php

namespace App\Mail;

use App\Models\User; // تم تصحيح الاستدعاء هنا إلى موديل المستخدم
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    // تم تعديل النوع إلى النوع الصحيح وتمرير رابط التحقق الخاص بالـ React
    public User $user;
    public string $verificationUrl;

    public function __construct(User $user, string $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email Address',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.verify-email', // نستخدم view لأننا سنضع تصميم HTML مخصص وجذاب أدناه
        );
    }

    public function attachments(): array
    {
        return [];
    }
}