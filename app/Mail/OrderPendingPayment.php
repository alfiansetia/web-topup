<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPendingPayment extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pesanan {$this->order->order_number} — Menunggu Pembayaran",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-pending',
            with: [
                'order' => $this->order->load('items'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
