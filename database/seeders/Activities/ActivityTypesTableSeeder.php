<?php

	namespace Database\Seeders\Activities;

	use App\Models\Activities\ActivityType;
	use Illuminate\Database\Seeder;

	class ActivityTypesTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$activityTypes = [
				['name' => 'Call', 'slug' => 'call', 'icon' => 'phone', 'color' => '#10B981', 'description' => 'Phone call with client', 'is_active' => true],
				['name' => 'Meeting', 'slug' => 'meeting', 'icon' => 'users', 'color' => '#3B82F6', 'description' => 'In-person or virtual meeting', 'is_active' => true],
				['name' => 'Email', 'slug' => 'email', 'icon' => 'envelope', 'color' => '#6366F1', 'description' => 'Email communication', 'is_active' => true],
				['name' => 'Task', 'slug' => 'task', 'icon' => 'check-square', 'color' => '#F59E0B', 'description' => 'General task or to-do', 'is_active' => true],
				['name' => 'Demo', 'slug' => 'demo', 'icon' => 'desktop', 'color' => '#8B5CF6', 'description' => 'Product demonstration', 'is_active' => true],
				['name' => 'SMS', 'slug' => 'sms', 'icon' => 'comment', 'color' => '#EF4444', 'description' => 'Text message', 'is_active' => true],
				['name' => 'Proposal', 'slug' => 'proposal', 'icon' => 'file-alt', 'color' => '#14B8A6', 'description' => 'Proposal sent or received', 'is_active' => true],
				['name' => 'Follow-up', 'slug' => 'follow-up', 'icon' => 'bell', 'color' => '#EC4899', 'description' => 'Follow-up activity', 'is_active' => true],
				['name' => 'Note', 'slug' => 'note', 'icon' => 'sticky-note', 'color' => '#6B7280', 'description' => 'General note', 'is_active' => true],
			];

			foreach ($activityTypes as $type) {
				ActivityType::query()->updateOrCreate([
					'slug' => $type['slug']
				], $type);
			}

			$this->command->info('✅ Activity types seeded: ' . count($activityTypes));
		}
	}