<?php

	namespace Database\Seeders\Tags;

	use App\Models\Tag;
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Str;

	class TagsTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$tags = [
				['name'  => 'High Priority',
				 'color' => '#ef4444',
				 'group' => 'status'
				],
				[
					'name'  => 'New Lead',
					'color' => '#3b82f6',
					'group' => 'sales'
				],
				[
					'name'  => 'VIP Customer',
					'color' => '#8b5cf6',
					'group' => 'customers'
				],
				[
					'name'  => 'Follow Up',
					'color' => '#f59e0b',
					'group' => 'tasks'
				],
				[
					'name'  => 'Completed',
					'color' => '#10b981',
					'group' => 'status'
				],
			];

			foreach ($tags as $tag) {
				Tag::create([
					'name'      => $tag['name'],
					'slug'      => Str::slug($tag['name']),
					'color'     => $tag['color'],
					'group'     => $tag['group'],
					'is_active' => true,
				]);
			}
		}
	}
