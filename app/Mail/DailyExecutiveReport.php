<?php

	namespace App\Mail;


	use App\Models\Invoices\Invoice;
	use App\Models\Lead;
	use App\Models\Payment;
	use Illuminate\Bus\Queueable;
	use Illuminate\Mail\Mailable;
	use Illuminate\Mail\Mailables\Content;
	use Illuminate\Mail\Mailables\Envelope;
	use Illuminate\Queue\SerializesModels;

	class DailyExecutiveReport extends Mailable {
		use Queueable, SerializesModels;

		public array $stats;

		public function __construct() {
			// Μαζεύουμε τα δεδομένα της χθεσινής ημέρας
			$this->stats = [
				// Πόσοι νέοι πελάτες μπήκαν χθες
				'new_leads'        => Lead::query()->whereDate('created_at', today()->subDay())->count(),

				// Πόσα λεφτά μπήκαν στο ταμείο χθες
				'cash_in'          => Payment::whereDate('created_at', today()->subDay())->sum('amount'),

				// Πόσα τιμολόγια παραμένουν απλήρωτα γενικά (για πίεση)
				'pending_invoices' => Invoice::query()->where('status', 'unpaid')->count(),
			];
		}

		public function envelope(): Envelope {
			return new Envelope(subject: '📊 ClientHub: Executive Summary (' . today()->subDay()->format('d/m/Y') . ')',);
		}

		public function content(): Content {
			return new Content(
				view: 'emails.reports.executive',
			);
		}
	}
