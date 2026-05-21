<?php

	namespace App\Models\Scopes;

	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Scope;

	class AccountScope implements Scope {
		/**
		 * Apply the scope to a given Eloquent query builder.
		 * Εδώ ορίζουμε τι θα φιλτράρεται ΠΑΝΤΑ (Global Scope).
		 * Αν δεν θέλεις global φιλτράρισμα, άφησε τη μέθοδο κενή.
		 */
		public function apply(Builder $builder, Model $model): void {
			// Π.χ. $builder->where('is_active', true);
		}

		/**
		 * Εδώ ορίζουμε "Named Scopes" που καλούνται χειροκίνητα.
		 */
		public function scopeActive(Builder $builder): Builder {
			return $builder->where('is_active', true);
		}

		public function scopeByIndustry(Builder $builder, string $industry): Builder {
			return $builder->where('industry', $industry);
		}

		public function scopeRevenueGreaterThan(Builder $builder, float $amount): Builder {
			return $builder->where('annual_revenue', '>=', $amount);
		}
	}
