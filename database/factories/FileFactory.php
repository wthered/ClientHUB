<?php

	namespace Database\Factories;

	use App\Models\Account;
	use App\Models\File;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;

	class FileFactory extends Factory {
		protected $model = File::class;

		public function definition(): array {
			// Χρησιμοποιούμε μια λίστα από πραγματικά extensions για σιγουριά
			$ext = $this->faker->randomElement(['jpg', 'png', 'pdf', 'docx', 'xlsx', 'zip']);

			return [
				'fileable_id'   => 1,
				'fileable_type' => Account::class,
				'original_name' => $this->faker->word() . '.' . $ext,
				'storage_path'  => 'uploads/' . $this->faker->uuid() . '.' . $ext,
				'extension'     => $ext,
				'mime_type'     => $this->faker->mimeType(),
				'size'          => $this->faker->numberBetween(5000, 500000),
				'disk'          => 'public',
				'uploaded_by'   => User::inRandomOrder()->first()?->id ?? User::factory(),
			];
		}
	}
