<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Lead;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Facades\DB;

	class AddressesTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$users = User::all();

			// Configuration: Model => Chance of having an address (0-100)
			$targets = [
				Account::class => 100, // Every company needs a location
				Contact::class => 60,  // Some contacts have specific home/office addresses
				Lead::class    => 40,  // Leads are early stage, might not have address yet
			];

			foreach ($targets as $model => $probability) {
				$entities = $model::all();

				foreach ($entities as $entity) {
					if (mt_rand(1, 100) <= $probability) {
						// Create Primary Address
						$this->createAddress($entity, 'billing', true, $users->random()->id);

						// 20% chance of a secondary shipping/office address
						if (mt_rand(1, 100) <= 20) {
							$this->createAddress($entity, 'shipping', false, $users->random()->id);
						}
					}
				}
			}

			$this->command->info('Addresses seeded successfully!');
		}

		/**
		 * Helper to insert polymorphic address record
		 */
		private function createAddress($model, string $type, bool $isPrimary, int $userId): void {
			DB::table('addresses')->insert([
				'addressable_id'   => $model->id,
				'addressable_type' => get_class($model),
				'type'             => $type,

				'address_line1'    => fake()->streetAddress(),
				'address_line2'    => fake()->optional(0.2)->buildingNumber(),

				'city'             => fake()->city(),
				'state'            => fake()->citySuffix(),
				'postal_code'      => fake()->postcode(),
				'country'          => fake()->country(),

				'phone'            => fake()->optional(0.5)->phoneNumber(),
				'email'            => fake()->optional(0.3)->safeEmail(),

				'is_primary'       => $isPrimary,
				'created_by'       => $userId,
				'is_active'        => true,

				'created_at'       => now(),
				'updated_at'       => now(),
			]);
		}
	}
