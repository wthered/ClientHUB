<?php

	namespace App\Models\Scopes;

	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Scope;
	use Illuminate\Support\Facades\Auth;

	class OpportunityScope implements Scope {

		public function apply(Builder $builder, Model $model): void {
			// 1. Global Filter
			$builder->where('is_active', true);

			// 2. Security Scope
			if (Auth::check()) {
				$user = Auth::user();

				// ΕΞΑΙΡΕΣΗ: Admin, Super-Admin και Sales Manager βλέπουν ΤΑ ΠΑΝΤΑ.
				// Οι υπόλοιποι (Sales Rep, κλπ) βλέπουν μόνο τα δικά τους.
				if (!$user->hasAnyRole(['admin', 'super-admin', 'Sales Manager'])) {
					$builder->where('employee_id', $user->id);
				}
			}
		}

		public function extend(Builder $builder): void {
			$builder->macro('onlyWon', function (Builder $builder) {
				return $builder->where('status', 'won');
			});

			$builder->macro('highValue', function (Builder $builder, $amount = 10000) {
				return $builder->where('amount', '>=', $amount);
			});
		}
	}