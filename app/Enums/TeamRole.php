<?php

	namespace App\Enums;

	enum TeamRole: string {
		case MEMBER  = 'member';
		case LEADER  = 'leader';
		case ADMIN   = 'admin';
		case VIEWERS = 'viewer';

		// Επιστρέφει labels για το UI (π.χ. αν θέλεις κεφαλαίο το πρώτο γράμμα)
		public static function options(): array {
			return array_column(self::cases(), 'value');
		}

		// Χρήσιμο για το <select> στο Blade

		public function label(): string {
			return match ($this) {
				self::MEMBER => 'Member',
				self::LEADER => 'Leader',
				self::ADMIN => 'Administrator',
				self::VIEWERS => 'Viewer',
			};
		}

		public function badgeClass(): string {
			return match($this) {
				self::ADMIN  => 'badge-admin',   // π.χ. κόκκινο/μωβ
				self::LEADER => 'badge-leader',  // χρυσό
				self::MEMBER => 'badge-member',  // μπλε/γκρι
				self::VIEWERS => 'badge-viewer', // ανοιχτό γκρι
			};
		}
	}
