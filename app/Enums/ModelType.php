<?php

	namespace App\Enums;

	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;

	enum ModelType: string {
		case OPPORTUNITY = Opportunity::class;
		case ACCOUNT     = Account::class;
		case CONTACT     = Contact::class;
		case USER        = User::class;
		const LEAD = Lead::class;

		public function label(): string {
			return match ($this) {
				self::OPPORTUNITY => 'Opportunity',
				self::ACCOUNT => 'Account',
				self::CONTACT => 'Contact',
				self::USER => 'User System',
			};
		}

		public function icon(): string {
			return match ($this) {
				self::OPPORTUNITY => '💰',
				self::ACCOUNT => '🏢',
				self::CONTACT => '👤',
				self::USER => '🔑',
			};
		}

		public function colors(): string {
			return match ($this) {
				self::OPPORTUNITY => 'background: #e0e7ff; color: #4338ca;',
				self::ACCOUNT => 'background: #fef3c7; color: #92400e;',
				self::CONTACT => 'background: #d1fae5; color: #065f46;',
				self::USER => 'background: #f3f4f6; color: #374151;',
			};
		}

		public function route(): string {
			return match ($this) {
				self::OPPORTUNITY => 'opportunities.show',
				self::ACCOUNT => 'accounts.show',
				self::CONTACT => 'contacts.show',
				self::LEAD => 'leads.show',
				self::USER => 'profile.edit',
			};
		}
	}
