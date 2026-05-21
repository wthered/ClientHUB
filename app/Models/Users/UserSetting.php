<?php

	namespace App\Models\Users;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;

	class UserSetting extends Model {
		// Αν ο πίνακάς σου ονομάζεται όντως user_settings, η Laravel το βρίσκει μόνη της.
		// Απλά πρόσθεσε τα fillable για να μπορείς να κάνεις update.
		protected $fillable = [
			'user_id',
			'stagnant_report_enabled',
			'daily_pulse_enabled',
			'notify_on_sales',
			'language',
			'timezone',
			'theme'
		];

		public function user(): BelongsTo {
			return $this->belongsTo(User::class);
		}
	}
