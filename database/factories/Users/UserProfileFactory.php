<?php

	namespace Database\Factories\Users;

	use App\Models\Users\User;
	use App\Models\Users\UserProfile;
	use Illuminate\Database\Eloquent\Factories\Factory;
	use Illuminate\Support\Str;

	class UserProfileFactory extends Factory {
		/**
		 * Συνδέουμε ρητά το Factory με το Model UserProfile
		 */
		protected $model = UserProfile::class;

		/**
		 * Define the model's default state.
		 *
		 * @return array<string, mixed>
		 */
		public function definition(): array {
			return [
				// Το user_id θα περνιέται συνήθως από τον Seeder,
				// αλλά βάζουμε ένα fallback για ασφάλεια.
				'user_id'          => User::factory(),
				'first_name'       => fake()->firstName(),
				'last_name'        => fake()->lastName(),
				'phone'            => fake()->optional()->regexify('2[1-8][0-9]{8}'),
				'avatar'           => "https://robohash.org/" . md5(Str::password(mt_rand(12, 16))) . ".png?size=256x256&set=set" . mt_rand(1, 5),
				'position'         => fake()->jobTitle(),
				'bio'              => fake()->paragraph(2),
				'notify_on_report' => fake()->boolean(),
				'created_at'       => now()->subHours(mt_rand(0, 23))->subMinutes(mt_rand(0, 59))->subSeconds(mt_rand(0, 59)),
				'updated_at'       => now()->addHours(mt_rand(0, 23))->addMinutes(mt_rand(0, 59))->addSeconds(mt_rand(0, 59)),
			];
		}
	}
