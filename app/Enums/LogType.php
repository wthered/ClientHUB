<?php

	namespace App\Enums;

	enum LogType: string {
		case SYSTEM   = 'system';
		case AUDIT    = 'audit';
		case ACTIVITY = 'activity';

		public function label(): string {
			return __('activity.type.' . $this->value);
		}

		public function icon(): string {
			return match ($this) {
				self::SYSTEM => '⚙️',
				self::AUDIT => '🔍',
				self::ACTIVITY => '👤',
			};
		}
	}