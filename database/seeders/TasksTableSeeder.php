<?php

	namespace Database\Seeders;

	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Facades\DB;

	class TasksTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$users         = User::all();
			$opportunities = Opportunity::all();
			$leads         = Lead::all();

			if ($users->isEmpty()) {
				$this->command->warn('No users found. Skipping tasks seeding.');
				return;
			}

			$this->command->info('🚀 Seeding 128 tasks with creators and assignees...');

			for ($i = 0; $i < 128; $i++) {
				// Επιλογή υπεύθυνου και δημιουργού (μπορεί να είναι το ίδιο πρόσωπο ή διαφορετικό)
				// Μέσα στο for loop του TasksTableSeeder
				$assignedTo = $users->random();

				// Παίρνουμε όλους τους χρήστες ΕΚΤΟΣ από αυτόν που μόλις ορίσαμε ως $assignedTo
				$createdBy = $users->reject(function ($user) use ($assignedTo) {
					return $user->id === $assignedTo->id;
				})->random();

				// Τυχαία ανάθεση σε Lead, Opportunity ή τίποτα (Global Task)
				$taskableType = null;
				$taskableId   = null;

				$choice = mt_rand(1, 3);
				if ($choice === 1 && $opportunities->isNotEmpty()) {
					$taskableType = Opportunity::class;
					$taskableId   = $opportunities->random()->id;
				} elseif ($choice === 2 && $leads->isNotEmpty()) {
					$taskableType = Lead::class;
					$taskableId   = $leads->random()->id;
				}

				DB::table('tasks')->insert([
					'subject' => fake()->randomElement([
							'Call',
							'Email',
							'Meeting',
							'Follow up',
							'Send Proposal'
						]) . ': ' . fake()->words(2, true),

					'description'   => fake()->paragraph(),
					'status'        => fake()->randomElement(['pending', 'in_progress', 'completed', 'deferred']),
					'priority'      => fake()->randomElement(['low', 'medium', 'high', 'urgent']),

					// Ημερομηνίες
					'due_date'      => fake()->dateTimeBetween('-1 month', '+1 month'),
					'completed_at'  => fake()->optional(0.3)->dateTimeBetween('-1 month', 'now'),

					// Σχέσεις Χρηστών
					'user_id'       => $assignedTo->id,   // Ο εκτελεστής
					'creator_id'    => $createdBy->id,    // Ο δημιουργός (Πλέον κάνει comply με το migration!)

					// Polymorphic link
					'taskable_id'   => $taskableId,
					'taskable_type' => $taskableType,

					'created_at'    => now(),
					'updated_at'    => now(),
				]);
			}

			$this->command->info('✅ Tasks seeded successfully with creators!');
		}
	}
