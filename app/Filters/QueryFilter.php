<?php

	namespace App\Filters;

	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Http\Request;

	abstract class QueryFilter {
		protected Request $request;
		protected Builder $builder;

		public function __construct(Request $request) {
			$this->request = $request;
		}

		public function apply(Builder $builder): Builder {
			$this->builder = $builder;

			foreach ($this->request->all() as $name => $value) {
				if (method_exists($this, $name) && $value !== null && $value !== '') {
					call_user_func([
						$this,
						$name
					], $value);
				}
			}

			return $this->builder;
		}
	}