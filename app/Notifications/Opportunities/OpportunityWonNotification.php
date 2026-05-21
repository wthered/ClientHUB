<?php

	namespace App\Notifications\Opportunities;

	use App\Models\Opportunities\Opportunity;
	use Illuminate\Bus\Queueable;
	use Illuminate\Notifications\Messages\MailMessage;
	use Illuminate\Notifications\Notification;

	class OpportunityWonNotification extends Notification {
		use Queueable;

		protected Opportunity $opportunity;

		public function __construct(Opportunity $opportunity) {
			$this->opportunity = $opportunity;
		}

		public function via($notifiable): array {
			return [
				'mail',
				'database'
			];
		}

		public function toMail($notifiable): MailMessage {
			$amount = number_format($this->opportunity->amount, 2, ',', '.') . '€';

			return (new MailMessage())
				->subject('🎉 Κερδήθηκε νέα Ευκαιρία: ' . $this->opportunity->name)
				->greeting('Συγχαρητήρια!')
				->line('Η ευκαιρία "' . $this->opportunity->name . '" έκλεισε με επιτυχία (WON)!')
				->line('Συνολικό ποσό: **' . $amount . '**')
				->action('Δείτε τις λεπτομέρειες', route('opportunities.edit', $this->opportunity->id))
				->line('Εξαιρετική προσπάθεια από την ομάδα!');
		}

		public function toArray($notifiable): array {
			return [
				'opportunity_id' => $this->opportunity->id,
				'amount'         => $this->opportunity->amount,
				'message'        => '🎉 Η ευκαιρία ' . $this->opportunity->name . ' κερδήθηκε!'
			];
		}
	}
