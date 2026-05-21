<?php

	namespace Database\Seeders;

	use App\Models\Company;
	use App\Models\Team;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class CompaniesTableSeeder extends Seeder {
		public function run(): void {
			$users = User::all();

			if ($users->isEmpty()) {
				$this->command->error('No users found. Run UsersTableSeeder first!');
				return;
			}

			// Δημιουργία μερικών εταιρειών
			Company::factory()
				->count(mt_rand(20, 32))
				->create()
				->each(function ($company) use ($users) {

					// 1. Φτιάξε 1-4 ομάδες για την εταιρεία
					Team::factory()
						->count(rand(1, 4))
						->create([
							'company_id' => $company->id,
							'manager_id' => $users->random()->id,
							'leader_id'  => $users->random()->id,
						])
						->each(function ($team) use ($users) {

							// 2. Εξασφάλισε ότι ο Leader είναι ΠΑΝΤΑ μέλος της ομάδας με τον σωστό ρόλο
							$team->members()->attach($team->leader_id, ['role' => 'leader']);

							// 3. Πρόσθεσε επιπλέον τυχαία μέλη (αποκλείοντας τον leader για να μην έχουμε duplicate)
							$potentialMembers = $users->where('id', '!=', $team->leader_id);
							$randomMembers = $potentialMembers->random(min(rand(3, 6), $potentialMembers->count()));

							foreach ($randomMembers as $member) {
								$team->members()->attach($member->id, ['role' => 'member']);
							}
						});
				});

			$this->command->info('Ponzi levels established: '.Company::query()->count().' Companies with nested Teams and Members.');
		}
	}