<?php

	namespace Database\Factories\Activities;

	use App\Models\Account;
	use App\Models\ActivityLog;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;
	use Illuminate\Support\Carbon;

	class ActivityLogFactory extends Factory {
		protected $model = ActivityLog::class;

		public function definition(): array {
			$subjects = [Account::class, Lead::class, Opportunity::class];
			$subjectType = $this->faker->randomElement($subjects);

			return [
				'user_id'       => User::inRandomOrder()->first()?->id ?? User::factory(),
				'loggable_id'   => $subjectType::inRandomOrder()->first()?->id ?? 1,
				'loggable_type' => $subjectType,
				'log_type'      => $this->faker->randomElement(['activity', 'audit', 'system']),
				'event'         => $this->faker->randomElement(['created', 'updated', 'deleted', 'converted']),
				'description'   => $this->faker->sentence(),
				'properties'    => [
					'old' => ['status' => 'pending'],
					'new' => ['status' => 'active']
				],
				'ip_address'    => $this->faker->ipv4(),
				'user_agent'    => $this->faker->userAgent(),
				'created_at'    => Carbon::now()->subDays(mt_rand(0, 30)),
				'updated_at'    => Carbon::now(),
			];
		}
	}
