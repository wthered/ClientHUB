<?php

	namespace Database\Seeders;

	use App\Enums\TeamRole;
	use App\Models\Company;
	use App\Models\Team;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class TeamsTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			// 1. Έλεγχος χρηστών
			$users = User::all();
			if ($users->isEmpty()) {
				$this->command->warn('Skipping TeamsTableSeeder: No users found.');
				return;
			}

			// 2. Διασφάλιση ύπαρξης Εταιρείας (Η κορυφή της πυραμίδας)
			$company = Company::query()
				           ->first() ?? Company::query()
				           ->create([
					           'name' => 'Global Ponzi Corp',
					           'description' => 'Think Big, Take Small'
				           ]);

			// 3. Ορισμός Ομάδων
			$teams = [
				[
					'name' => 'Sales Team',
					'desc' => 'Lead generation & closing deals'
				],
				[
					'name' => 'Marketing Team',
					'desc' => 'Campaigns & content'
				],
				[
					'name' => 'Customer Success',
					'desc' => 'Onboarding & support'
				],
				[
					'name' => 'Development',
					'desc' => 'Tech implementation'
				],
			];

			foreach ($teams as $index => $data) {
				// Δημιουργία ομάδας κάτω από την εταιρεία
				$team = Team::query()
					->updateOrCreate(['name' => $data['name']], [
							'description' => $data['desc'],
							'company_id'  => $company->id,
							'is_active'   => true,
						]);

				// 4. Ανάθεση "The Big Boss" (Manager)
				// Παίρνουμε έναν τυχαίο χρήστη για Manager (ας πούμε από τους πρώτους 3)
				$manager = $users->slice(0, 3)->random();

				// 5. Ανάθεση "The Frontman" (Leader)
				// Παίρνουμε έναν άλλον τυχαίο χρήστη για Leader
				$leader = $users->where('id', '!=', $manager->id)->random();

				$team->update([
					'manager_id' => $manager->id,
					'leader_id'  => $leader->id,
				]);

				// 6. Προσθήκη Μελών (Η βάση). Παίρνουμε 5 τυχαίους χρήστες
				$members = $users->random(min(5, $users->count()));

				foreach ($members as $member) {
					// Καθορισμός ρόλου με βάση το Enum
					$role = ($member->id === $leader->id) ? TeamRole::LEADER : TeamRole::MEMBER;

					$team->members()->syncWithoutDetaching([
						$member->id => ['role' => $role->value]
					]);
				}
			}

			$this->command->info('TeamsTableSeeder: The pyramid is ready with Companies, Managers, and Leaders.');
		}
	}
