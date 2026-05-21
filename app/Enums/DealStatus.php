<?php

	namespace App\Enums;

	enum DealStatus: string {
		case OPEN = 'open';
		case WON  = 'won';
		case LOST = 'lost';

		// Helper για να παίρνεις τα labels (π.χ. για το UI)
		public function label(): string {
			return match ($this) {
				self::OPEN => 'Ανοιχτό',
				self::WON => 'Κερδισμένο',
				self::LOST => 'Χαμένο',
			};
		}
	}
