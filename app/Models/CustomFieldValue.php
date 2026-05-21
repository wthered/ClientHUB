<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;

	class CustomFieldValue extends Model {
		protected $fillable = [
			'model_id',
			'model_type',
			'custom_field_id',
			'value'
		];

		/**
		 * Σύνδεση με τον ορισμό του πεδίου.
		 */
		public function field(): BelongsTo {
			return $this->belongsTo(CustomField::class, 'custom_field_id');
		}

		/**
		 * Polymorphic relationship: Επιτρέπει στην τιμή να ανήκει
		 * σε οποιοδήποτε μοντέλο (Account, Contact, Opportunity).
		 */
		public function model(): MorphTo {
			return $this->morphTo();
		}
	}