<?php

	namespace Database\Seeders;

	use App\Models\{Account, Contact, Lead, Users\User};
	use Carbon\Carbon;
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Facades\DB;

	class CommunicationsTableSeeder extends Seeder {
		public function run(): void {
			$users   = User::all();
			$targets = [
				Account::class => Account::all(),
				Contact::class => Contact::all(),
				Lead::class    => Lead::all(),
			];

			$types = [
				'email',
				'call',
				'sms',
				'meeting'
			];

			for ($i = 0; $i < 150; $i++) {
				$type      = fake()->randomElement($types);
				$modelType = fake()->randomKey($targets);
				$model     = $targets[$modelType]->random();

				DB::table('communications')
					->insert([
						'communicable_id'   => $model->id,
						'communicable_type' => $modelType,
						'type'              => $type,
						'direction'         => fake()->randomElement([
							'inbound',
							'outbound'
						]),
						'subject'           => $type === 'email' ? fake()->sentence() : null,
						'content'           => fake()->paragraph(),

						'from' => $type === 'call' ? fake()->phoneNumber() : fake()->safeEmail(),
						'to'   => $type === 'call' ? fake()->phoneNumber() : fake()->safeEmail(),

						'duration' => in_array($type, [
							'call',
							'meeting'
						]) ? mt_rand(30, 3600) : null,
						'result'   => $type === 'call' ? fake()->randomElement([
							'no_answer',
							'busy',
							'completed'
						]) : 'delivered',

						'status'      => 'completed',
						'occurred_at' => fake()->dateTimeBetween('-6 months'),
						'user_id'     => $users->random()->id,
						'created_at'  => Carbon::yesterday()->setHours(mt_rand(0, 23))->setMinutes(mt_rand(0, 59))->setSeconds(mt_rand(0, 59))->toDateTimeString(),
						'updated_at'  => today()->setHours(mt_rand(0, 23))->setMinutes(mt_rand(0, 59))->setSeconds(mt_rand(0, 59))->toDateTimeString(),
					]);
			}
		}
	}