<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class AccountsTableSeeder extends Seeder {
		public function run(): void {
			$users = User::all();

			if ($users->isEmpty()) {
				$this->command->error('❌ Skipping AccountsTableSeeder: No users found.');
				return;
			}

			// 1. Δημιουργία των "Hardcoded" Accounts για το Demo
			$fixedAccounts = [
				[
					'name'     => 'Acme Corporation',
					'industry' => 'Technology',
					'city'     => 'Αθήνα',
					'state'    => 'Αττικής'
				],
				[
					'name'     => 'Global Finance Ltd',
					'industry' => 'Financial Services',
					'city'     => 'Θεσσαλονίκη',
					'state'    => 'Κεντρικής Μακεδονίας'
				],
				[
					'name'     => 'HealthPlus Hospital',
					'industry' => 'Healthcare',
					'city'     => 'Πάτρα',
					'state'    => 'Δυτικής Ελλάδας'
				],
				[
					'name'     => 'Green Energy Solutions',
					'industry' => 'Renewable Energy',
					'city'     => 'Ηράκλειο',
					'state'    => 'Κρήτης'
				],
				[
					'name'     => 'Summit Retail Group',
					'industry' => 'Retail',
					'city'     => 'Λάρισα',
					'state'    => 'Θεσσαλίας'
				],
			];

			foreach ($fixedAccounts as $data) {
				// Επιλογή τυχαίου χρήστη/προφίλ
				$selectedUser = $users->random();
				$profile = $selectedUser->profile;

				Account::factory()->create([
					'name'             => $data['name'],
					'industry'         => $data['industry'],
					'city'             => $data['city'],
					'email'            => fake()->unique()->companyEmail,
					'phone'            => fake()->phoneNumber,
					'website'          => 'https://www.' . strtolower(str_replace(' ', '', $data['name'])) . '.gr',
					'address'          => fake()->streetAddress,
					'state'            => 'Περιφέρεια ' . $data['state'],
					'country'          => 'Ελλάδα',
					'postal_code'      => fake()->postcode,
					'employee_count'   => fake()->numberBetween(10, 5000),
					'annual_revenue'   => fake()->randomFloat(2, 50000, 10000000),
					'is_active'        => fake()->boolean(),
					'owner_id'         => $selectedUser->id,
					'notes'            => 'Official Account Managed by ' . ($profile->first_name ?? $selectedUser->name),
				]);
			}

			// 2. Δημιουργία επιπλέον 19 (5 + 19 = 24) τυχαίων accounts
			Account::factory()->count(19)->recycle($users)->create();

			$this->command->info('✅ Accounts seeded successfully (Fixed + Random).');
		}
	}
