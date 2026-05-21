<?php

	namespace App\Models\Invoices;

	use App\Enums\InvoiceStatus;
	use App\Models\Account;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Payment;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\HasMany;

	class Invoice extends Model {
		use HasFactory;

		protected $fillable = [
			'account_id',
			'opportunity_id',
			'invoice_number',
			'total_amount',
			'invoice_date',
			'due_date',
			'status',
		];

		protected $casts = [
			'invoice_date' => 'date',
			'due_date'     => 'date',
			'total_amount' => 'decimal:2',
			'paid_amount'  => 'decimal:2',
			'status'       => InvoiceStatus::class,
		];

		public function account(): BelongsTo {
			return $this->belongsTo(Account::class);
		}

		public function opportunity(): BelongsTo {
			return $this->belongsTo(Opportunity::class);
		}

		public function payments(): Invoice|HasMany {
			return $this->hasMany(Payment::class);
		}

		/**
		 * Οι γραμμές (items) του τιμολογίου.
		 */
		public function items(): HasMany {
			return $this->hasMany(InvoiceItem::class);
		}

		// Accessor για να αναγνωρίζει αυτόματα το "Partial" status
		// αν το status είναι Unpaid αλλά υπάρχει πληρωμή
		public function getEffectiveStatusAttribute(): InvoiceStatus {
			if ($this->status === InvoiceStatus::UNPAID && $this->paid_amount > 0) {
				return InvoiceStatus::PARTIAL;
			}

			return $this->status;
		}
	}
