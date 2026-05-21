<?php

	namespace App\Enums\Opportunities;

	enum OpportunityStage: string {
		case DISCOVERY    = 'discovery';
		case PROPOSAL     = 'proposal';
		case NEGOTIATION  = 'negotiation';
		case AWAITING_SIG = 'awaiting_signature';
		case WON          = 'won';
		case LOST         = 'lost';

		public static function allLabel(): string {
			return __('opportunities.stages.all');
		}

		public function label(): string {
			return __("opportunities.stage." . strtolower($this->value));
		}

		public function color(): string {
			return match ($this) {
				self::DISCOVERY => '#6366f1',
				self::PROPOSAL => '#a855f7',
				self::NEGOTIATION => '#f59e0b',
				self::AWAITING_SIG => '#06b6d4',
				self::WON => '#10b981',
				self::LOST => '#ef4444',
			};
		}

		/**
		 * Επιστρέφει το χρώμα με 15% opacity για το φόντο (αντίστοιχο του 26 σε hex)
		 */
		public function background(): string {
			return $this->color() . '26';
		}

		/**
		 * Επιστρέφει το χρώμα με 25% opacity για το border (αντίστοιχο του 44 σε hex)
		 */
		public function border(): string {
			return $this->color() . '44';
		}
	}