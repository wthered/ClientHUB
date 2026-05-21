<?php

	namespace App\Models\Opportunities;

	use App\Enums\Opportunities\OpportunityStageStatus;
	use App\Models\Pipeline;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\HasMany;

	class Stage extends Model {
		use HasFactory;

		/**
		 * The attributes that are mass assignable.
		 *
		 * @var array<int, string>
		 */
		protected $fillable = [
			'pipeline_id',
			'name',
			'order',
		];

		/**
		 * The attributes that should be cast.
		 *
		 * @var array<string, string>
		 */
		protected $casts = [
			'status'      => OpportunityStageStatus::class,
			'pipeline_id' => 'integer',
			'order'       => 'integer',
		];

		// --- Relationships ---

		/**
		 * The pipeline this stage belongs to.
		 */
		public function pipeline(): BelongsTo {
			return $this->belongsTo(Pipeline::class);
		}

		/**
		 * The opportunities currently at this stage.
		 */
		public function opportunities(): HasMany {
			return $this->hasMany(Opportunity::class, 'stage_id');
		}

		// --- Scopes ---

		/**
		 * Scope a query to order stages by their position in the pipeline.
		 */
		public function scopeOrdered($query) {
			return $query->orderBy('order', 'asc');
		}
	}
