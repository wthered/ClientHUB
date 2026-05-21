<?php

	namespace App\Enums\Tasks;

	enum TaskStatus: string {
		case PENDING     = 'pending';
		case IN_PROGRESS = 'in_progress';
		case COMPLETED   = 'completed';
		case DEFERRED    = 'deferred';
		case CANCELLED   = 'cancelled';

		// Μέθοδος για να παίρνουμε το μεταφρασμένο label
		public static function options(): array {
			return collect(self::cases())->mapWithKeys(function ($status) {
				return [$status->value => $status->label()];
			})->toArray();
		}

		// Μέθοδος για να επιστρέφει όλα τα cases σε array (χρήσιμο για τα selects)
		public function label(): string {
			return __("tasks.status.".$this->value);
		}
	}