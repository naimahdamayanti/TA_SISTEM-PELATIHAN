<?php

// ============================================================
// FILE: app/Mail/ResetPasswordMail.php
// Jalankan untuk membuat file ini:
//   php artisan make:mail ResetPasswordMail
// Kemudian isi dengan kode di bawah ini.
// ============================================================

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $token Token reset yang sudah disimpan ke tabel users
     * @param string $nama  Nama pengguna penerima email
     */
    public function __construct(
        public readonly string $token,
        public readonly string $nama,
    ) {}

    /** Subjek dan pengirim email */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password — SIMPERTI',
        );
    }

    /** View Blade yang digunakan sebagai isi email */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: [
                'nama'       => $this->nama,
                'resetUrl'   => route('password.reset', $this->token),
                'expireMenit'=> 60,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
