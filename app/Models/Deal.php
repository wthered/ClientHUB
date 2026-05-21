<?php

	namespace App\Models;

	use App\Enums\DealStatus;
	use App\Models\Opportunities\Opportunity;
	use App\Traits\Auditable;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphMany;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class Deal extends Model {
		use SoftDeletes, Auditable;

		protected $fillable = [
			'title',
			'account_id',
			'contact_id',
			'lead_id',
			'pipeline_id',
			'stage_id',
			'value',
			'currency',
			'status',
			'user_id',
			'expected_close_date',
			'closed_at'
		];

		protected $casts = [
			'status'    => DealStatus::class,
			'closed_at' => 'datetime',
			'value'     => 'decimal:2',
		];

		/**
		 * Σχέσεις (Relationships)
		 */

		// Ο ιδιοκτήτης του Deal (User)
		public function owner(): BelongsTo {
			return $this->belongsTo(User::class, 'user_id');
		}

		// Σύνδεση με Lead
		public function lead(): BelongsTo {
			return $this->belongsTo(Lead::class);
		}

		// Σύνδεση με Opportunity
		public function opportunity(): BelongsTo {
			return $this->belongsTo(Opportunity::class);
		}

		// Σύνδεση με Pipeline
		public function pipeline(): BelongsTo {
			return $this->belongsTo(Pipeline::class);
		}

		// Σύνδεση με Stage
		public function stage(): BelongsTo {
			return $this->belongsTo(Stage::class);
		}

		public function notes(): MorphMany {
			return $this->morphMany(Note::class, 'notable');
		}
	}