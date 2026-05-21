<?php

	namespace App\Models;

	use App\Models\Opportunities\Opportunity;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\MorphToMany;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class Tag extends Model {
		use SoftDeletes;

		/**
		 * Τα πεδία που επιτρέπεται να γεμίσουν μαζικά (Mass Assignment).
		 */
		protected $fillable = [
			'name',
			'slug',
			'color',
			'description',
			'group',
			'is_active',
		];

		/**
		 * Μετατροπή τύπων δεδομένων κατά την ανάκτηση.
		 */
		protected $casts = [
			'is_active'  => 'boolean',
			'created_at' => 'datetime',
			'updated_at' => 'datetime',
			'deleted_at' => 'datetime',
		];

		/*
		|--------------------------------------------------------------------------
		| Relationships
		|--------------------------------------------------------------------------
		*/
		/**
		 * Σχέση με τα Leads (Polymorphic M:M).
		 */
		public function leads(): MorphToMany {
			return $this->morphedByMany(Lead::class, 'taggable');
		}

		/**
		 * Σχέση με τα Opportunities (Polymorphic M:M).
		 */
		public function opportunities(): MorphToMany {
			return $this->morphedByMany(Opportunity::class, 'taggable');
		}

		/**
		 * Σχέση με τα Accounts (Polymorphic M:M).
		 */
		public function accounts(): MorphToMany {
			return $this->morphedByMany(Account::class, 'taggable');
		}
	}
