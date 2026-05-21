<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Document;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class DocumentsTableSeeder extends Seeder {
		public function run(): void {
			// 1. Φέρε τις οντότητες που χρειάζεσαι
			$accounts      = Account::all();
			$opportunities = Opportunity::all();

			// 2. ΕΛΕΓΧΟΣ ΑΣΦΑΛΕΙΑΣ (Εδώ γινόταν η ζημιά)
			if ($accounts->isEmpty() && $opportunities->isEmpty()) {
				$this->command->warn('⚠️ Skipping DocumentsTableSeeder: No Accounts or Opportunities found.');
				return;
			}

			// 3. Δημιουργία Documents
			foreach ($accounts as $account) {
				// Αντί για $users->random() που μπορεί να σκάσει αν οι Users είναι 0:
				$user = User::inRandomOrder()->first();

				if ($user) {
					Document::factory()->count(mt_rand(1, 8))->create([
						'documentable_id'   => $account->id,
						'documentable_type' => Account::class,
						'uploaded_by'       => $user->id,
					]);
				}
			}
		}
	}