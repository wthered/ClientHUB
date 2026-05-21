<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Activities\ActivityLog;
	use App\Models\Contact;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class ActivityLogsTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$users         = User::all();
			$accounts      = Account::all();
			$contacts      = Contact::all();
			$opportunities = Opportunity::all();

			if ($users->isEmpty()) {
				$this->command->warn('Skipping ActivityLogsTableSeeder: No users found.');
				return;
			}

			$loggableItems = [];

			// Collect all possible loggable items (polymorphic)
			foreach ($accounts as $account) {
				$loggableItems[] = [
					'model' => $account,
					'type'  => Account::class,
				];
			}

			foreach ($contacts as $contact) {
				$loggableItems[] = [
					'model' => $contact,
					'type'  => Contact::class,
				];
			}

			foreach ($opportunities as $opportunity) {
				$loggableItems[] = [
					'model' => $opportunity,
					'type'  => Opportunity::class,
				];
			}

			if (empty($loggableItems)) {
				$this->command->warn('Skipping ActivityLogsTableSeeder: No records found to log activities for.');
				return;
			}

			$events = [
				'created',
				'updated',
				'deleted',
				'status_changed',
				'note_added',
				'stage_changed',
				'assigned',
				'email_sent',
				'call_made',
				'meeting_scheduled'
			];

			$logTypes = [
				'activity',
				'audit',
				'system'
			];

			$logCount = 0;

			// Create 16 to 32 activity logs
			$totalLogs = mt_rand(16, 32);

			for ($i = 0; $i < $totalLogs; $i++) {
				$loggable = $loggableItems[array_rand($loggableItems)];
				$user     = $users->random();

				$event   = $events[array_rand($events)];
				$logType = $logTypes[array_rand($logTypes)];

				$description = $this->generateLogDescription($loggable['model'], $event);

				ActivityLog::query()->create([
					'log_type'    => $logType,
					'event'       => $event,
					'description' => $description,

					'loggable_id'   => $loggable['model']->id,
					'loggable_type' => $loggable['type'],

					'user_id'    => $user->id,
					'properties' => [
						'ip'         => fake()->ipv4(),
						'user_agent' => fake()->userAgent(),
					],
					'ip_address' => fake()->ipv4(),
					'user_agent' => fake()->userAgent(),
				]);

				$logCount++;
			}

			$this->command->info("ActivityLogsTableSeeder completed successfully. {$logCount} activity logs seeded.");
		}

		/**
		 * Generate realistic log description based on model and event.
		 */
		private function generateLogDescription($model, string $event): string {
			$name = match (true) {
				$model instanceof Account => $model->name,
				$model instanceof Contact => trim(($model->first_name ?? '') . ' ' . ($model->last_name ?? '')),
				$model instanceof Opportunity => $model->name,
				default => 'Record'
			};

			return match ($event) {
				'created' => "Created new " . strtolower(class_basename($model)) . ": {$name}",
				'updated' => "Updated {$name}",
				'status_changed' => "Changed status for {$name}",
				'stage_changed' => "Moved {$name} to new sales stage",
				'note_added' => "Added a note to {$name}",
				'assigned' => "Assigned {$name} to a team member",
				'email_sent' => "Sent email regarding {$name}",
				'call_made' => "Logged a call with {$name}",
				'meeting_scheduled' => "Scheduled a meeting about {$name}",
				default => ucfirst($event) . " on {$name}",
			};
		}
	}
