<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiPengaduanStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $pengaduan;
    /**
     * Create a new message instance.
     */
    public function __construct($pengaduan)
    {
        $this->pengaduan = $pengaduan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi Pengaduan Status',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifikasi_pengaduan_status',
            with: [
                'judul' => $this->pengaduan->judul,
                'status' => $this->pengaduan->status,
                'isi_pengaduan' => $this->pengaduan->isi_pengaduan,
                'updated_at' => $this->pengaduan->updated_at,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
