<?php

	namespace App\Mail;

	use App\Models\Users\User;
	use Illuminate\Bus\Queueable;
	use Illuminate\Mail\Mailable;
	use Illuminate\Mail\Mailables\Content;
	use Illuminate\Mail\Mailables\Envelope;
	use Illuminate\Queue\SerializesModels;

	class WeeklyReportMail extends Mailable {
		use Queueable, SerializesModels;

		public array $stats;
		public User  $user;

		/**
		 * Δημιουργία νέας αναφοράς.
		 */
		public function __construct($stats, $user) {
			$this->stats = $stats;
			$this->user  = $user;
		}

		/**
		 * Ορισμός θέματος (Subject).
		 */
		public function envelope(): Envelope {
			return new Envelope(subject: '📊 CRM Weekly Report - ' . now()->format('d/m/Y'),);
		}

		/**
		 * Ορισμός του Template (Blade view).
		 */
		public function content(): Content {
			return new Content(view: 'emails.weekly-report',);
		}
	}
