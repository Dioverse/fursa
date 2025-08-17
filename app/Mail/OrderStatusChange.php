<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusChange extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $items;
    public $status;
    public $messageText;
    public $shippingAdd;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $status)
    {
        $this->order = $order;
        $this->status = $status;
        $this->messageText = $status == "out for delivery" ? "your order with ID: {$this->order->order_id} is out for delivery" : "your order with ID: {$this->order->order_id} has been {$status}";

        if ($this->order->shipping_address_id) {
            $this->shippingAdd = ShippingAddress::where("id", $order->shipping_address_id)->first();
        } else {
            $this->items = collect();
        }

        if ($this->order->id) {
            $this->items = OrderItem::with('product:name,short_description,image')->where("order_id", $this->order->id)->get();
        } else {
            $this->items = collect();
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "Order Update :: " . config('app.name');
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.order-status-update',
            with: [
                "order"         => $this->order,
                "items"         => $this->items,
                "messageText"   => $this->messageText,
                'shippingAdd'   => $this->shippingAdd
                
            ]
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
