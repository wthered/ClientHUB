<?php

	namespace App\Models;

	use App\Models\Opportunities\Opportunity;
	use App\Models\Opportunities\Stage;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\HasMany;
	use Illuminate\Database\Eloquent\Relations\HasManyThrough;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Class Pipeline
	 * Represents a sales workflow containing multiple stages.
	 */
	class Pipeline extends Model {
		use HasFactory, SoftDeletes;

		protected $fillable = [
			'name',
			'description',
			'is_active',
			'order',
		];

		protected $casts = [
			'is_active' => 'boolean',
			'order'     => 'integer',
		];

		/****************************************
		 * Accessors
		 ***************************************/

		/**
		 * Get the total value of all opportunities currently in this pipeline.
		 */
		public function getTotalValueAttribute(): float {
			return (float) $this->opportunities()->sum('amount');
		}

		/**
		 * Get all opportunities across all stages of this pipeline.
		 * Pipeline -> Stage -> Opportunity
		 */
		public function opportunities(): Pipeline|HasManyThrough {
			return $this->hasManyThrough(Opportunity::class, Stage::class, 'pipeline_id', 'stage_id', 'id', 'id');
		}

		// --- Relationships ---

		/**
		 * Get the count of open opportunities in this pipeline.
		 */
		public function getOpenOpportunitiesCountAttribute(): int {
			return $this->opportunities()->where('status', 'open')->count();
		}

		/**
		 * The stages that belong to this pipeline.
		 */
		public function stages(): Pipeline|HasMany {
			return $this->hasMany(Stage::class)->orderBy('order');
		}
	}
