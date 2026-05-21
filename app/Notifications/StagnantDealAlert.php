<?php

	namespace App\Notifications;

	use App\Models\Opportunities\Opportunity;
	use Illuminate\Bus\Queueable;
	use Illuminate\Notifications\Notification;

	class StagnantDealAlert extends Notification {
		use Queueable;

		protected Opportunity $deal;

		/**
		 * Create a new notification instance.
		 * Πρέπει να δεχτούμε το $deal εδώ!
		 */
		public function __construct(Opportunity $deal) {
			$this->deal = $deal;
		}

		/**
		 * Get the notification's delivery channels.
		 *
		 * @return array<int, string>
		 */
		public function via(object $notifiable): array {
			return ['database'];
		}

		/**
		 * Get the array representation of the notification.
		 *
		 * @return array<string, mixed>
		 */
		public function toArray($notifiable): array {
			return [
				'title'      => 'Στάσιμη Ευκαιρία!',
				'message'    => "Η ευκαιρία '{$this->deal->name}' (>{$this->deal->amount}€) δεν έχει δραστηριότητα 5 μέρες.",
				'deal_id'    => $this->deal->id,
				'action_url' => route('opportunities.show', $this->deal->id),
			];
		}
	}
