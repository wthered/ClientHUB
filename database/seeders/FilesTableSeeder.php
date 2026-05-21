<?php

	namespace Database\Seeders;

	use App\Models\Company;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Str;

	class FilesTableSeeder extends Seeder {
		public function run(): void {
			$users = User::all();
			$companies = Company::all();

			// Seed αρχεία για εταιρείες (Logos)
			foreach ($companies as $company) {
				DB::table('files')->insert([
					'fileable_id'   => $company->id,
					'fileable_type' => Company::class,
					'original_name' => 'logo.png',
					'storage_path'  => 'logos/' . Str::random(20) . '.png',
					'extension'     => 'png',
					'mime_type'     => 'image/png',
					'size'          => mt_rand(5000, 50000),
					'uploaded_by'   => $users->random()->id,
					'created_at'    => now()->subHours(mt_rand(0, 23))->subMinutes(mt_rand(0, 59))->subSeconds(mt_rand(0, 59))->toDateTimeString(),
					'updated_at'    => now()->addHours(mt_rand(0, 23))->addMinutes(mt_rand(0, 59))->addSeconds(mt_rand(0, 59))->toDateTimeString(),
				]);
			}
		}
	}