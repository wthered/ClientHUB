<?php

	namespace App\Enums;

	enum ActivityEvent: string {
		case LOGIN             = 'login';
		case LOGOUT            = 'logout';
		case CREATED           = 'created';
		case UPDATED           = 'updated';
		case DELETED           = 'deleted';
		case EMAIL_SENT        = 'email_sent';
		case CALL_MADE         = 'call_made';
		case MEETING_SCHEDULED = 'meeting_scheduled';
		case ASSIGNED          = 'assigned';
		case STATUS_CHANGED    = 'status_changed';
		case STAGE_CHANGED     = 'stage_changed';
		case NOTE_ADDED        = 'note_added';

		public function label(): string {
			return __('activity.event.' . $this->value);
		}

		public function colorClass(): string {
			return match ($this) {
				self::CREATED => 'event-success',
				self::DELETED => 'event-danger',
				self::UPDATED, self::STATUS_CHANGED, self::STAGE_CHANGED => 'event-warning',
				self::EMAIL_SENT, self::CALL_MADE, self::MEETING_SCHEDULED => 'event-info',
				self::LOGIN, self::LOGOUT => 'event-auth',
				self::ASSIGNED, self::NOTE_ADDED => 'event-secondary',
			};
		}

		public function icon(): string {
			return match ($this) {
				self::EMAIL_SENT => '📧',
				self::CALL_MADE => '📞',
				self::LOGIN => '🔑',
				self::DELETED => '🗑️',
				self::MEETING_SCHEDULED => '📅',
				default => '📝',
			};
		}
	}
