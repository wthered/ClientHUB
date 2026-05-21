<?php

	namespace App\Enums\Tasks;

	enum TaskPriority: string {
		case URGENT = 'urgent';
		case HIGH   = 'high';
		case MEDIUM = 'medium';
		case LOW    = 'low';

		public static function options(): array {
			return collect(self::cases())->mapWithKeys(function ($priority) {
				return [$priority->value => $priority->label()];
			})->toArray();
		}

		public function label(): string {
			return __("tasks.priority.".$this->value);
		}
	}