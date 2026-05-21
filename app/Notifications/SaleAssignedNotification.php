<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SaleAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
	public function via($notifiable): array {
		// ΕΔΩ ΕΙΝΑΙ ΤΟ ΚΛΕΙΔΙ:
		// Στέλνουμε email ΜΟΝΟ αν το πεδίο notify_on_sales στο προφίλ είναι true (1)
		return $notifiable->profile->notify_on_sales ? ['mail'] : [];
	}

    /**
     * Get the mail representation of the notification.
     */
	public function toMail($notifiable): MailMessage {
		return (new MailMessage)
			->subject('Νέα Ανάθεση Πώλησης: ' . $this->sale->title)
			->greeting('Γεια σας ' . $notifiable->name . '!')
			->line('Σας ανατέθηκε μια νέα πώληση στο σύστημα.')
			->action('Δείτε την Πώληση', url('/sales/' . $this->sale->id))
			->line('Καλή επιτυχία!');
	}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
