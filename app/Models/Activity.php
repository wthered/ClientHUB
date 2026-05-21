<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;

	class Activity extends Model {
		// Ορίζουμε ρητά τον πίνακα γιατί το Laravel
		// μπορεί να μπερδευτεί με το activity_logs
		protected $table = 'activities';

		/**
		 * Η πολυμορφική σχέση προς τα πάνω (Account, Contact, Opportunity)
		 */
		public function activitable(): MorphTo {
			return $this->morphTo();
		}

		/**
		 * Ο χρήστης που δημιούργησε το activity (στο migration σου είναι owner_id)
		 */
		public function owner(): BelongsTo {
			return $this->belongsTo(Users\User::class, 'owner_id');
		}
	}
