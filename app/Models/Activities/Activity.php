<?php

	namespace App\Models\Activities;

	use App\Models\Note;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphMany;
	use Illuminate\Database\Eloquent\Relations\MorphTo;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class Activity extends Model {
		use HasFactory, SoftDeletes;

		protected $fillable = [
			'type',
			'subject',
			'description',
			'content',
			'direction',
			'priority',
			'due_at',
			'completed_at',
			'sent_at',
			'status',
			'is_completed',
			'activitable_id',
			'activitable_type',
			'owner_id',
			'activity_type_id',
			'notes',
			'is_active'
		];

		protected $casts = [
			'due_at'       => 'datetime',
			'completed_at' => 'datetime',
			'sent_at'      => 'datetime',
			'is_completed' => 'boolean',
			'is_active'    => 'boolean',
		];

		// --- Relationships ---

		/**
		 * Η πολυμορφική σχέση (Lead, Account, Opportunity, κλπ)
		 */
		public function activitable(): MorphTo {
			return $this->morphTo();
		}

		/**
		 * Ο τύπος της δραστηριότητας (Call, Meeting, κλπ) από το lookup table.
		 */
		public function activityType(): BelongsTo {
			return $this->belongsTo(ActivityType::class);
		}

		/**
		 * Ο χρήστης που είναι υπεύθυνος.
		 */
		public function owner(): BelongsTo {
			return $this->belongsTo(User::class, 'owner_id');
		}

		/**
		 * Σχέση με τα Notes (Polymorphic)
		 */
		public function notes(): MorphMany {
			return $this->morphMany(Note::class, 'notable');
		}
	}
