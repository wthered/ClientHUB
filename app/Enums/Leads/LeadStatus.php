<?php

	namespace App\Enums\Leads;

	/**
	 * Enum LeadStatus
	 * * Defines the lifecycle stages of a Lead within the CRM.
	 * Includes methods for UI coloring and localized labeling.
	 */
	enum LeadStatus: string {
		case NEW       = 'new';
		case CONTACTED = 'contacted';
		case QUALIFIED = 'qualified';
		case LOST      = 'lost';
		case JUNK      = 'junk';
		case CONVERTED = 'converted';

		/**
		 * Get the label for the "All Statuses" filter option.
		 */
		public static function allLabel(): string {
			return __('leads.status.all');
		}

		/**
		 * Helper to return an array of all cases formatted for a select dropdown.
		 * Useful for API responses or complex form builders.
		 * * @return array<string, string>
		 */
		public static function options(): array {
			return collect(self::cases())->mapWithKeys(fn($case) => [
				$case->value => $case->label()
			])->toArray();
		}

		/**
		 * Get the translated label for the specific status.
		 * Uses the 'lang/{locale}/leads.php' file.
		 */
		public function label(): string {
			return __("leads.status.".$this->value);
		}

		/**
		 * Get the primary Hex color code for text or borders.
		 * Based on Tailwind-style palette (600 weights).
		 */
		public function color(): string {
			return match ($this) {
				self::NEW => '#2563eb',
				self::CONTACTED => '#06b6d4',
				self::QUALIFIED => '#d97706',
				self::LOST => '#64748b',
				self::JUNK => '#475569',
				self::CONVERTED => '#059669',
			};
		}

		/**
		 * Get the light background Hex code for badges/pills.
		 * Based on Tailwind-style palette (50 weights).
		 */
		public function bgColor(): string {
			return match ($this) {
				self::NEW => '#eff6ff',
				self::CONTACTED => '#ecfeff',
				self::QUALIFIED => '#fffbeb',
				self::LOST => '#f8fafc',
				self::JUNK => '#f1f5f9',
				self::CONVERTED => '#ecfdf5',
			};
		}
	}