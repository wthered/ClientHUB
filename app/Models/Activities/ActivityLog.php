<?php

	namespace App\Models\Activities;

	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;

	/**
	 * Class ActivityLog
	 * Records every significant action within the CRM for audit and history purposes.
	 */

	/**
	 * Class ActivityLog
	 * Records every significant action within the CRM for audit and history purposes.
	 */
	class ActivityLog extends Model {
		use HasFactory;

		/**
		 * Τα attributes πρέπει να αντιστοιχούν ακριβώς στο Schema σου.
		 */
		protected $fillable = [
			'user_id',
			'log_type',
			'event',
			'description',
			'loggable_id',
			'loggable_type',
			'properties',
			'ip_address',
			'user_agent',
		];

		/**
		 * Cast properties as array για να παίζουν αυτόματα με το JSON της βάσης.
		 */
		protected $casts = [
			'properties' => 'array',
			'created_at' => 'datetime',
			'updated_at' => 'datetime',
		];

		// --- Static Helper ---

		/**
		 * Static helper προσαρμοσμένο στα νέα ονόματα πεδίων.
		 */
		public static function log(Model $model, string $event, ?string $description = null, ?array $properties = null, string $type = 'activity'): self {
			return self::create([
				'user_id'       => auth()->id(),
				'loggable_id'   => $model->getKey(),
				'loggable_type' => get_class($model),
				'log_type'      => $type,
				'event'         => $event,
				'description'   => $description,
				'properties'    => $properties,
				'ip_address'    => request()->ip(),
				'user_agent'    => request()->userAgent(),
			]);
		}

		// --- Accessors ---

		/**
		 * Human-readable summary.
		 */
		public function getSummaryAttribute(): string {
			$userName  = $this->user ? $this->user->name : 'System';
			$modelName = class_basename($this->loggable_type);
			return "{$userName} {$this->event} on {$modelName}";
		}

		// --- Relationships ---

		/**
		 * Ο χρήστης που έκανε την ενέργεια.
		 */
		public function user(): BelongsTo {
			return $this->belongsTo(User::class);
		}

		/**
		 * Η πολυμορφική σχέση. Το όνομα 'loggable' πρέπει να κάνει match
		 * με τα loggable_id / loggable_type του Schema.
		 */
		public function loggable(): MorphTo {
			return $this->morphTo();
		}
	}
