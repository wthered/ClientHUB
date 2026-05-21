<?php

	namespace Database\Seeders;

	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Reminder;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Collection;

	class RemindersTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$users = User::all();
			$accounts = Account::all();
			$contacts = Contact::all();
			$opportunities = Opportunity::all();

			if ($users->isEmpty()) {
				$this->command->warn('Skipping RemindersTableSeeder: No users found.');
				return;
			}

			$remindableItems = [];

			// Collect all possible remindable items (polymorphic)
			if (!$accounts->isEmpty()) {
				foreach ($accounts as $account) {
					$remindableItems[] = ['model' => $account, 'type' => Account::class];
				}
			}

			if (!$contacts->isEmpty()) {
				foreach ($contacts as $contact) {
					$remindableItems[] = ['model' => $contact, 'type' => Contact::class];
				}
			}

			if (!$opportunities->isEmpty()) {
				foreach ($opportunities as $opportunity) {
					$remindableItems[] = ['model' => $opportunity, 'type' => Opportunity::class];
				}
			}

			if (empty($remindableItems)) {
				$this->command->warn('Skipping RemindersTableSeeder: No remindable items (accounts/contacts/opportunities) found.');
				return;
			}

			$statuses = ['pending', 'done', 'dismissed'];
			$reminderCount = 0;

			// Create 8 to 16 reminders
			for ($i = 0; $i < mt_rand(8, 16); $i++) {
				$remindable = Collection::make($remindableItems)->random();

				$dueAt = now()->addDays(rand(-5, 30))->addHours(rand(8, 18));

				Reminder::query()->create([
					'remindable_id'   => $remindable['model']->id,
					'remindable_type' => $remindable['type'],

					'title'           => $this->getRandomReminderTitle($remindable['model']),
					'notes'           => 'This is a seeded reminder for testing purposes.',
					'due_at'          => $dueAt,
					'status'          => $statuses[array_rand($statuses)],

					'user_id'         => $users->random()->id,
					'created_by'      => $users->random()->id,
				]);

				$reminderCount++;
			}

			$this->command->info("RemindersTableSeeder completed successfully. ".$reminderCount." reminders seeded.");
		}

		/**
		 * Generate a realistic reminder title based on the remindable model.
		 */
		private function getRandomReminderTitle($model): string {
			$titles = [
				Account::class => [
					'Follow up on proposal for ' . $model->name,
					'Schedule quarterly business review with ' . $model->name,
					'Send contract renewal reminder to ' . $model->name,
				],
				Contact::class => [
					'Call ' . ($model->first_name ?? 'Contact'),
					'Send personalized email to ' . ($model->first_name ?? 'Contact'),
					'Prepare meeting agenda for ' . ($model->first_name ?? 'Contact'),
				],
				Opportunity::class => [
					'Follow up on ' . ($model->name ?? 'Opportunity'),
					'Prepare final proposal for ' . ($model->name ?? 'Opportunity'),
					'Check status on ' . ($model->name ?? 'Opportunity'),
				],
			];

			$possibleTitles = $titles[get_class($model)] ?? ['General follow-up required'];

			return Collection::make($possibleTitles)->random();
		}
	}
