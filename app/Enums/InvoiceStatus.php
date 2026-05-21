<?php

	namespace App\Enums;

	use Illuminate\Support\Facades\Lang;
	use Illuminate\Support\Str;

	enum InvoiceStatus: string {
		case DRAFT     = 'draft';
		case SENT      = 'sent';
		case UNPAID    = 'unpaid';
		case PARTIAL   = 'partially_paid';
		case PAID      = 'paid';
		case OVERDUE   = 'overdue';
		case CANCELLED = 'cancelled';

		/**
		 * Επιστρέφει το Hex Code για το κύριο χρώμα (Text/Border)
		 */
		public function color(): string {
			return match ($this) {
				self::DRAFT     => '#6b7280',
				self::SENT      => '#0050b3',
				self::UNPAID    => '#d48806',
				self::PARTIAL   => '#08979c',
				self::PAID      => '#1f7a33',
				self::OVERDUE   => '#cf1322',
				self::CANCELLED => '#8c8c8c',
			};
		}

		/**
		 * Επιστρέφει το Hex Code για το φόντο (Light version)
		 */
		public function bgColor(): string {
			return match ($this) {
				self::DRAFT     => '#f3f4f6',
				self::SENT      => '#e6f7ff',
				self::UNPAID    => '#fffbe6',
				self::PARTIAL   => '#e6fffb',
				self::PAID      => '#e3f9e5',
				self::OVERDUE   => '#fff1f0',
				self::CANCELLED => '#f5f5f5',
			};
		}

		public function label(): string {
			$key = "invoices.status.".$this->value;

			// Αν υπάρχει το translation key, το επιστρέφουμε.
			if (Lang::has($key)) {
				return __($key);
			}

			// Fallback: Μετατρέπουμε το 'partially_paid' σε 'Partially Paid'
			return Str::headline($this->value);
		}
	}
