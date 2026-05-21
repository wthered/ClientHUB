<?php

	namespace App\Enums\Opportunities;

	enum OpportunityStageStatus: string {
		case OPEN = 'open';
		case WON = 'won';
		case LOST = 'lost';

		public function label(): string {
			return __("opportunities.status.".$this->value);
		}

		/**
		 * Επιστρέφει το κύριο χρώμα (Hex) για το κείμενο ή τα borders
		 */
		public function color(): string {
			return match($this) {
				self::OPEN => '#2563eb', // Sophisticated Blue
				self::WON  => '#059669', // Emerald Green
				self::LOST => '#dc2626', // Soft Red
			};
		}

		/**
		 * Επιστρέφει ένα απαλό φόντο (Hex ή RGBA) για badges
		 */
		public function background(): string {
			return match($this) {
				self::OPEN => '#eff6ff',
				self::WON  => '#ecfdf5',
				self::LOST => '#fef2f2',
			};
		}
	}
