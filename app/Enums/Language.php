<?php

	namespace App\Enums;

	enum Language: string {
		case GREEK   = 'el';
		case ENGLISH = 'en';

		public static function values(): array {
			return array_column(self::cases(), 'value');
		}
	}