<?php

	namespace App\Models;

	use App\Models\Invoices\Invoice;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;

	class Payment extends Model {
		use HasFactory;

		protected $fillable = [
			'invoice_id',
			'amount',
			'method',
			'payment_date',
			'notes',
		];

		protected $casts = [
			'payment_date' => 'date',
		];

		/**
		 * Σχέση: Η πληρωμή ανήκει σε ένα τιμολόγιο.
		 */
		public function invoice(): BelongsTo {
			return $this->belongsTo(Invoice::class);
		}
	}
