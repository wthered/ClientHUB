<?php

	namespace Database\Seeders;

	use App\Models\AuditLog;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class AuditLogsTableSeeder extends Seeder {
		public function run(): void {
			$users = User::all();
			$leads = Lead::all();
			$opportunities = Opportunity::all();

			if ($users->isEmpty()) return;

			$actions = ['created', 'updated', 'deleted', 'status_change'];

			for ($i = 0; $i < 200; $i++) {
				$user = $users->random();

				// Επιλογή τυχαίας οντότητας για το log
				$target = fake()->randomElement([
					$leads->isNotEmpty() ? $leads->random() : null,
					$opportunities->isNotEmpty() ? $opportunities->random() : null,
					null // Log χωρίς οντότητα (π.χ. Login)
				]);

				AuditLog::create([
					'user_id'        => $user->id,
					'action'         => $target ? fake()->randomElement($actions) : 'login',
					'auditable_id'   => $target ? $target->id : null,
					'auditable_type' => $target ? get_class($target) : null,
					'old_values'     => ['status' => 'pending'],
					'new_values'     => ['status' => 'completed'],
					'ip_address'     => fake()->ipv4(),
					'user_agent'     => fake()->userAgent(),
					'created_at'     => fake()->dateTimeBetween('-1 month', 'now'),
				]);
			}
		}
	}