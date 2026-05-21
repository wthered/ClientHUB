<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Note;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class NotesTableSeeder extends Seeder {
		public function run(): void {
			$users = User::all();
			$accounts = Account::all();
			$opportunities = Opportunity::all();

			if ($users->isEmpty()) {
				$this->command->error('❌ No users found to write notes.');
				return;
			}

			$this->command->info('📝 Seeding polymorphic notes...');

			// 1. Σημειώσεις για Accounts
			foreach ($accounts as $account) {
				Note::factory()->count(mt_rand(1, 3))->forAccount($account)->recycle($users)->create();
			}

			// 2. Σημειώσεις για Opportunities
			foreach ($opportunities as $opportunity) {
				Note::factory()->count(mt_rand(0, 2))->forOpportunity($opportunity)->recycle($users)->create();
			}

			$this->command->info('✅ Polymorphic notes linked to Accounts & Opportunities!');
		}
	}
