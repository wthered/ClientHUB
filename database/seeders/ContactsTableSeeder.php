<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class ContactsTableSeeder extends Seeder {

		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$this->command->info('🚀 Starting Professional Contacts Seeder...');

			$accounts = Account::all();
			$users    = User::all();

			foreach ($accounts as $account) {
				// 1. Primary Contact: Matches the Account's location for realism
				Contact::factory()->primary()->state([
					'account_id' => $account->id,
					'owner_id'   => $account->owner_id, // Usually the person who owns the account owns the primary contact
					'city'       => $account->city,     // Synced with Account
					'country'    => 'Greece',           // Assuming a local focus, or use $account->country
				])->create();

				// 2. Secondary Contacts: More random data
				Contact::factory()
					->count(mt_rand(1, 4))
					->state([
						'account_id' => $account->id,
						'is_primary' => false
					])
					->recycle($users)
					->create();
			}

			$this->displayStatistics();
		}

		/**
		 * Εμφάνιση στατιστικών στο τερματικό
		 */
		protected function displayStatistics(): void {
			$total     = Contact::count();
			$primary   = Contact::where('is_primary', true)->count();
			$withEmail = Contact::whereNotNull('email')->count();

			$this->command->info("   ┌─────────────────────────────────────────────────┐");
			$this->command->info("   │ Total Contacts Created:     {$total}");
			$this->command->info("   │ Primary Contacts (1/acc):   {$primary}");
			$this->command->info("   │ Contacts with Email:        {$withEmail}");
			$this->command->info("   └─────────────────────────────────────────────────┘");
			$this->command->info('🎉 ContactsTableSeeder completed successfully!');
		}
	}