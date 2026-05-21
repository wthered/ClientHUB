<?php

	namespace App\Notifications\Opportunities;

	use App\Models\Opportunities\Opportunity;
	use App\Models\Opportunities\Stage;
	use Illuminate\Bus\Queueable;
	use Illuminate\Notifications\Messages\MailMessage;
	use Illuminate\Notifications\Notification;

	class StageChangedNotification extends Notification {
		use Queueable;

		protected Opportunity $opportunity;
		protected Stage       $newStage;

		public function __construct(Opportunity $opportunity, Stage $newStage) {
			$this->opportunity = $opportunity;
			$this->newStage    = $newStage;
		}

		public function via($notifiable): array {
			// Μπορείς να προσθέσεις 'database' αν θέλεις να φαίνονται και μέσα στο CRM
			return [
				'mail',
				'database'
			];
		}

		public function toMail($notifiable): MailMessage {
			return (new MailMessage())
				->subject('🔄 Αλλαγή Σταδίου: ' . $this->opportunity->name)
				->greeting('Γεια σου ' . $notifiable->name . '!')
				->line('Η ευκαιρία "' . $this->opportunity->name . '" μετακινήθηκε στο στάδιο: ' . $this->newStage->name)
				->action('Προβολή Ευκαιρίας', route('opportunities.edit', $this->opportunity->id))
				->line('Συνέχισε την καλή δουλειά!');
		}

		public function toArray($notifiable): array {
			return [
				'opportunity_id' => $this->opportunity->id,
				'opportunity_name' => $this->opportunity->name,
				'new_stage' => $this->newStage->name,
				'message' => 'Η ευκαιρία μετακινήθηκε στο στάδιο ' . $this->newStage->name
			];
		}
	}
