<?php

	namespace App\Filters;

	use Illuminate\Database\Eloquent\Builder;

	class TaskFilters extends QueryFilter {
		public function status($value): Builder {
			return $this->builder->where('status', $value);
		}

		public function priority($value): Builder {
			return $this->builder->where('priority', $value);
		}

		public function search($value): Builder {
			return $this->builder->where(function ($query) use ($value) {
				$query
					->where('subject', 'like', "%".$value."%")
					->orWhere('description', 'like', "%".$value."%");
			});
		}

		public function date_from($value): Builder {
			return $this->builder->whereDate('due_date', '>=', $value);
		}

		public function date_to($value): Builder {
			return $this->builder->whereDate('due_date', '<=', $value);
		}
	}
