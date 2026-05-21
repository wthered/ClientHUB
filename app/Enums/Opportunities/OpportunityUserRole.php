<?php

	namespace App\Enums\Opportunities;

	enum OpportunityUserRole: string {
		case OWNER = 'owner';
		case COLLABORATOR = 'collaborator';
		case VIEWER = 'viewer';

		/**
		 * Get human-readable label for the role from lang files
		 */
		public function label(): string {
			return __("opportunities.roles.".$this->value);
		}

		/**
		 * Get options array for select dropdowns
		 */
		public static function options(): array {
			return collect(self::cases())->mapWithKeys(fn($case) => [
				$case->value => $case->label()
			])->toArray();
		}
	}
