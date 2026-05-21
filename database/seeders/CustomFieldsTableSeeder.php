<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Lead;
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Facades\DB;

	class CustomFieldsTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			// 1. Ορισμός των Πεδίων (Templates)
			$fields = [
				[
					'model_type'  => Account::class,
					'label'       => 'VAT Number',
					'name'        => 'vat_number',
					'type'        => 'text',
					'is_required' => true,
				],
				[
					'model_type' => Account::class,
					'label'      => 'Industry Sector',
					'name'       => 'industry_sector',
					'type'       => 'select',
					'options'    => json_encode([
						'Technology',
						'Real Estate',
						'Retail',
						'Logistics',
						'Healthcare'
					]),
				],
				[
					'model_type' => Contact::class,
					'label'      => 'Preferred Contact Method',
					'name'       => 'preferred_contact',
					'type'       => 'select',
					'options'    => json_encode([
						'Email',
						'Phone',
						'WhatsApp',
						'LinkedIn'
					]),
				],
				[
					'model_type' => Lead::class,
					'label'      => 'Lead Score',
					'name'       => 'lead_score',
					'type'       => 'number',
				],
				[
					'model_type' => Contact::class,
					'label'      => 'Birthday',
					'name'       => 'birthday',
					'type'       => 'date',
				],
			];

			foreach ($fields as $index => $fieldData) {
				// Εισαγωγή του ορισμού του πεδίου
				$fieldId = DB::table('custom_fields')->insertGetId(array_merge($fieldData, [
						'sort_order' => $index,
						'created_at' => now(),
						'updated_at' => now(),
					]));

				// 2. Δημιουργία τυχαίων τιμών για τα υπάρχοντα records
				$this->seedValuesForField($fieldId, $fieldData);
			}
		}

		/**
		 * Γεμίζει τυχαίες τιμές στον πίνακα custom_field_values
		 */
		private function seedValuesForField(int $fieldId, array $fieldData): void {
			$modelClass = $fieldData['model_type'];
			$records    = $modelClass::all();

			foreach ($records as $record) {
				// Σπέρνουμε τιμές στο 60% των εγγραφών για να είναι ρεαλιστικό το δείγμα
				if (mt_rand(1, 100) <= 60) {
					DB::table('custom_field_values')->insert([
						'model_id'        => $record->id,
						'model_type'      => $modelClass,
						'custom_field_id' => $fieldId,
						'value'           => $this->fakeValueByType($fieldData),
						'created_at'      => now(),
						'updated_at'      => now(),
					]);
				}
			}
		}

		/**
		 * Παράγει τυχαία τιμή ανάλογα με τον τύπο του πεδίου
		 */
		private function fakeValueByType(array $fieldData): string {
			return match ($fieldData['type']) {
				'text' => ($fieldData['name'] === 'vat_number') ? 'EL' . mt_rand(100000000, 999999999) : fake()->word(),
				'number' => (string) mt_rand(1, 100),
				'date' => fake()->date(),
				'select' => fake()->randomElement(json_decode($fieldData['options'])),
				default => fake()->sentence(),
			};
		}
	}
