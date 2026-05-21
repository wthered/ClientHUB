<?php

	namespace App\Filters;

	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Http\Request;

	class ActivityLogFilters {
		protected Request $request;
		protected Builder $builder;

		public function __construct(Request $request) {
			$this->request = $request;
		}

		public function apply(Builder $builder): Builder {
			$this->builder = $builder;

			foreach ($this->filters() as $filter => $value) {
				if (method_exists($this, $filter) && $value !== null && $value !== '') {
					$this->$filter($value);
				}
			}

			return $this->builder; // Επιστρέφει το Builder object
		}

		public function filters(): array {
			return $this->request->all();
		}

		// --- Τα Φίλτρα ---

		protected function search($value): void {
//			dd("Searching for ".$value);
			$this->builder->where('description', 'like', "%".$value."%");
		}

		protected function user_id($value): void {
			$this->builder->where('user_id', $value);
		}

		protected function model($value): void {
			$this->builder->where('loggable_type', $value);
		}

		protected function date_from($value): void {
			$this->builder->whereDate('created_at', '>=', $value);
		}

		protected function date_to($value): void {
			$this->builder->whereDate('created_at', '<=', $value);
		}
	}