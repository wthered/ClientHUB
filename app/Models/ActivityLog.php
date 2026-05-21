<?php

	namespace App\Models;

	use App\Enums\ActivityEvent;
	use App\Enums\LogType;
	use App\Enums\ModelType;
	use App\Filters\ActivityLogFilters;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;

	class ActivityLog extends Model {
		// Ορίζουμε τα fillable πεδία βάσει του migration σου
		protected $fillable = [
			'log_type',
			'event',
			'description',
			'loggable_id',
			'loggable_type',
			'user_id',
			'properties',
			'ip_address',
			'user_agent'
		];

		// Casting για το JSON πεδίο properties
		protected $casts = [
			'properties' => 'array',
			'event'      => ActivityEvent::class,
			'log_type'   => LogType::class,
		];

		/**
		 * Η σχέση με τον χρήστη που εκτέλεσε την ενέργεια.
		 */
		public function user(): BelongsTo {
			return $this->belongsTo(User::class);
		}

		/**
		 * Η πολυμορφική σχέση με το μοντέλο που αφορά το log (Opportunity, Account, κλπ).
		 */
		public function loggable(): MorphTo {
			return $this->morphTo();
		}

		public function getModelTypeAttribute(): ?ModelType {
			return ModelType::tryFrom($this->loggable_type);
		}

		/**
		 * Scope a query to apply filters from the ActivityLogFilters class.
		 *
		 * @param  Builder  $query
		 * @param  ActivityLogFilters  $filters
		 *
		 * @return Builder
		 */
		public function scopeFilter(Builder $query, ActivityLogFilters $filters): Builder {
			return $filters->apply($query);
		}
	}
