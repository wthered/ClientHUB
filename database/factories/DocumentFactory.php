<?php

	namespace Database\Factories;

	use App\Models\Account;
	use App\Models\Document;
	use App\Models\File;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;

	class DocumentFactory extends Factory {
		protected $model = Document::class;

		public function definition(): array {
			return [
				'documentable_id'   => 1,
				'documentable_type' => Account::class,
				'name'              => $this->faker->sentence(3),
				'original_name'     => $this->faker->word() . '.pdf',
				'disk'              => 'public',
				'checksum'          => $this->faker->sha256(),
				'category'          => $this->faker->randomElement(['proposal', 'contract', 'invoice']),

				// ΚΡΑΤΑ ΜΟΝΟ ΑΥΤΟ (αφού αυτό υπάρχει στο migration σου)
				'uploaded_by'       => User::inRandomOrder()->first()?->id ?? User::factory(),

				'description'       => $this->faker->paragraph(),
				'is_active'         => fake()->boolean(66),
				'file_id'           => File::inRandomOrder()->first()?->id ?? File::factory(),
			];
		}
	}
