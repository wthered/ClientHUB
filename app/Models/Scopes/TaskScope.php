<?php

	namespace App\Models\Scopes;

	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Scope;

	class TaskScope implements Scope {
		/**
		 * Apply the scope to a given Eloquent query builder.
		 */
		public function apply(Builder $builder, Model $model): void {
			//
		}

		/**
		 * Εφαρμογή των φίλτρων στο query των Tasks.
		 */
		public function scopeFilter(Builder $query, array $filters): Builder {
			return $query->when($filters['search'] ?? null, function ($q, $search) {
					$q->where(function ($sub) use ($search) {
						$sub
							->where('subject', 'like', '%' . $search . '%')
							->orWhere('description', 'like', '%' . $search . '%');
					});
				})->when($filters['status'] ?? null, function ($q, $status) {
					$q->where('status', $status);
				})->when($filters['priority'] ?? null, function ($q, $priority) {
					$q->where('priority', $priority);
				})->when($filters['date_from'] ?? null, function ($q, $date) {
					$q->whereDate('due_date', '>=', $date);
				})->when($filters['date_to'] ?? null, function ($q, $date) {
					$q->whereDate('due_date', '<=', $date);
				})->when($filters['user_id'] ?? null, function ($q, $userId) {
					$q->where('user_id', $userId);
				});
		}
	}
