<?php

	namespace App\Observers;

	use App\Models\Invoices\Invoice;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;

	class InvoiceObserver {
		public function created(Invoice $invoice): void {
			DB::table('activity_logs')->insert([
				'log_type'      => 'activity',
				'event'         => 'invoice_created',
				'description'   => "Εκδόθηκε το τιμολόγιο #{$invoice->invoice_number}.",
				'loggable_id'   => $invoice->id,
				'loggable_type' => Invoice::class,
				'user_id'       => Auth::id(),
				'created_at'    => now(),
			]);
		}

		public function updated(Invoice $invoice): void {
			if ($invoice->isDirty('status')) {
				// Log της αλλαγής κατάστασης (π.χ. από Unpaid σε Paid)
			}
		}
	}
