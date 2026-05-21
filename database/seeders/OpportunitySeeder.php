<?php

	namespace Database\Seeders;

	use App\Models\{Account, Opportunities\Opportunity, Opportunities\Stage, Users\User};
	use Illuminate\Database\Seeder;

	class OpportunitySeeder extends Seeder {
		public function run(): void {
			// Προ-φόρτωση δεδομένων για ταχύτητα
			$users    = User::all();
			$stages   = Stage::all();
			$accounts = Account::all();

			if ($accounts->isEmpty() || $stages->isEmpty()) {
				$this->command->error('Missing Accounts or Stages. Run those seeders first!');
				return;
			}

			// Δημιουργία μερικών Opportunities με Line Items
			Opportunity::factory()->count(mt_rand(48, 64))->recycle($users)->recycle($stages)->recycle($accounts)->create();

			$this->command->info('✅ '.Opportunity::query()->count().' Opportunities with Line Items seeded successfully!');
		}
	}
