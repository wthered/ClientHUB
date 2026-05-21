<?php

	namespace Database\Seeders\Activities;

	use App\Models\Activities\Activity;
	use App\Models\Opportunities\Opportunity;
	use Illuminate\Database\Seeder;

	class ActivitiesTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$opportunities = Opportunity::all();

			foreach ($opportunities as $opportunity) {
				Activity::factory()->count(mt_rand(2, 4))->create([
					'activitable_id'   => $opportunity->id,
					'activitable_type' => Opportunity::class,
					'description'      => "Discussion about " . $opportunity->name,
				])->each(function ($activity) use ($opportunity) {
					// Δημιουργία Note μέσω της σχέσης
					$activity->notes()->create([
						'user_id' => $activity->owner_id,
						'content' => "System: Activity for opportunity '".$opportunity->name."' was logged.",
					]);
				});
			}
		}

		/**
		 * Determine status based on completion and activity type
		 */
		private function determineStatus(bool $isCompleted, string $type): string {
			if ($isCompleted) {
				return 'completed';
			}

			$statuses = [
				'pending',
				'in_progress'
			];
			return $statuses[array_rand($statuses)];
		}

		/**
		 * Get random description based on activity type
		 */
		private function getRandomDescription(string $type, string $opportunityName): string {
			$descriptions = [
				'call'     => "Call with client to discuss progress on {$opportunityName}",
				'meeting'  => "Meeting regarding {$opportunityName}",
				'email'    => "Email correspondence about {$opportunityName}",
				'task'     => "Complete tasks related to {$opportunityName}",
				'demo'     => "Product demonstration for {$opportunityName}",
				'proposal' => "Review proposal for {$opportunityName}",
			];

			return $descriptions[$type] ?? "Activity for {$opportunityName}";
		}

		/**
		 * Get random content for emails/calls
		 */
		private function getRandomContent(string $type): string {
			if ($type === 'email') {
				$emails = [
					"Dear client,\n\nI hope this email finds you well. I wanted to follow up on our previous conversation...",
					"Hello,\n\nThank you for your time. As discussed, please find the proposal attached...",
					"Hi there,\n\nJust checking in to see if you have any questions about the proposal...",
				];
				return $emails[array_rand($emails)];
			}

			if ($type === 'call') {
				$callNotes = [
					"Client is interested in premium package. Sent proposal via email.",
					"Discussed implementation timeline. Client to provide feedback by next week.",
					"Answered technical questions about integration. Client seems positive.",
				];
				return $callNotes[array_rand($callNotes)];
			}

			return '';
		}

		/**
		 * Get random direction for calls/emails
		 */
		private function getRandomDirection(): string {
			return mt_rand(0, 1) ? 'inbound' : 'outbound';
		}

		/**
		 * Get random notes
		 */
		private function getRandomNotes(): string {
			$notes = [
				'Priority: High',
				'Follow-up required',
				'Awaiting client response',
				'Requires manager approval',
				'Internal note: Discuss in team meeting',
			];

			return mt_rand(0, 100) > 70 ? $notes[array_rand($notes)] : '';
		}
	}
