<?php

	namespace App\Models\Scopes;

	use Illuminate\Database\Eloquent\Builder;

	class LeadScope implements \Illuminate\Database\Eloquent\Scope {
		/**
		 * GLOBAL SCOPE: Automatically applied to all Lead queries.
		 */
		public function apply(Builder $builder, \Illuminate\Database\Eloquent\Model $model): Builder {

		}

		/**
		 * UNIFIED FILTER: This handles the dynamic searching from your request.
		 */
		public function scopeFilter(Builder $builder, array $filters): Builder {
			$builder->when($filters['search'] ?? null, function ($query, $search) {
				$query->where(function ($q) use ($search) {
					$q
						->where('first_name', 'like', "%{$search}%")
						->orWhere('last_name', 'like', "%{$search}%")
						->orWhere('company_name', 'like', "%{$search}%");
				});
			});

			$builder->when($filters['status'] ?? null, function ($query, $status) {
				$query->where('status', $status);
			});

			$builder->when($filters['priority'] ?? null, function ($query, $priority) {
				$query->where('priority', $priority);
			});

			return $builder;
		}

		public function scopeNew(Builder $query): Builder {
			return $query->where('status', 'new');
		}

		public function scopeNotContactedSince(Builder $query, int $days = 7): Builder {
			return $query->where(function ($q) use ($days) {
				$q->whereNull('last_contacted_at')
					->orWhere('last_contacted_at', '<', now()->subDays($days));
			});
		}

		public function scopeContacted(Builder $query): Builder {
			return $query->where('status', 'contacted');
		}

		public function scopeQualified(Builder $query): Builder {
			return $query->where('status', 'qualified');
		}

		public function scopeLost(Builder $query): Builder {
			return $query->where('status', 'lost');
		}

		/**
		 * Special scope to see Converted leads (Bypasses Global Scope)
		 */
		public function scopeConverted(Builder $query): Builder {
			return $query
				->withoutGlobalScope($this)
				->where('status', 'converted');
		}

		// --- Priority Scopes ---

		public function scopeHighOrUrgentPriority(Builder $query): Builder {
			return $query->whereIn('priority', [
				'high',
				'urgent'
			]);
		}

		public function scopePriority(Builder $query, string $priority): Builder {
			return $query->where('priority', $priority);
		}

		// --- Activity Scopes ---

		public function scopeContactedRecently(Builder $query, int $days = 7): Builder {
			return $query->where('last_contacted_at', '>=', now()->subDays($days));
		}
	}
