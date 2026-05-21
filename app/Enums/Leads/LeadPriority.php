<?php

	namespace App\Enums\Leads;

	enum LeadPriority: string {
		case LOW    = 'low';
		case MEDIUM = 'medium';
		case HIGH   = 'high';
		case URGENT = 'urgent';

		/**
		 * Get the label for the "All" filter state.
		 */
		public static function allLabel(): string {
			return __('leads.priority.all');
		}

		public function label(): string {
			return __("leads.priority." . $this->value);
		}

		public function color(): string {
			return match ($this) {
				self::LOW => '#94a3b8',
				self::MEDIUM => '#3b82f6',
				self::HIGH => '#f59e0b',
				self::URGENT => '#ef4444',
			};
		}

		public function bgColor(): string {
			return match ($this) {
				self::LOW => '#f1f5f9',
				self::MEDIUM => '#eff6ff',
				self::HIGH => '#fffbeb',
				self::URGENT => '#fef2f2',
			};
		}
	}
