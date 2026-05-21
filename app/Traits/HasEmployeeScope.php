<?php

	namespace App\Traits;

	use App\Models\Scopes\OpportunityScope;

	trait HasEmployeeScope {
		/**
		 * Το Laravel καλεί αυτόματα μεθόδους που ξεκινούν με "boot" + το όνομα του Trait.
		 */
		protected static function bootHasEmployeeScope(): void {
			static::addGlobalScope(new OpportunityScope());
		}
	}
