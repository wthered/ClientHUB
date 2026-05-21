<?php

	namespace Database\Seeders;

	use App\Models\Setting;
	use Illuminate\Database\Seeder;

	class SettingsTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$settings = [
				// General Settings
				[
					'key'   => 'company_name',
					'value' => 'Acme CRM Solutions',
					'type'  => 'string',
					'group' => 'general',
				],
				[
					'key'   => 'company_email',
					'value' => 'info@acmecrm.com',
					'type'  => 'string',
					'group' => 'general',
				],
				[
					'key'   => 'company_phone',
					'value' => '+30 210 123 4567',
					'type'  => 'string',
					'group' => 'general',
				],
				[
					'key'   => 'company_address',
					'value' => '123 Business Street, Athens, Greece',
					'type'  => 'text',
					'group' => 'general',
				],

				// Currency & Localization
				[
					'key'   => 'default_currency',
					'value' => 'EUR',
					'type'  => 'string',
					'group' => 'locale',
				],
				[
					'key'   => 'default_timezone',
					'value' => 'Europe/Athens',
					'type'  => 'string',
					'group' => 'locale',
				],
				[
					'key'   => 'date_format',
					'value' => 'd/m/Y',
					'type'  => 'string',
					'group' => 'locale',
				],

				// Business Configuration
				[
					'key'   => 'default_tax_rate',
					'value' => '24',
					'type'  => 'number',
					'group' => 'billing',
				],
				[
					'key'   => 'invoice_prefix',
					'value' => 'INV',
					'type'  => 'string',
					'group' => 'billing',
				],
				[
					'key'   => 'quote_prefix',
					'value' => 'QUO',
					'type'  => 'string',
					'group' => 'billing',
				],

				// System Settings
				[
					'key'   => 'items_per_page',
					'value' => '25',
					'type'  => 'number',
					'group' => 'system',
				],
				[
					'key'   => 'enable_notifications',
					'value' => 'true',
					'type'  => 'boolean',
					'group' => 'system',
				],
				[
					'key'   => 'enable_activity_logging',
					'value' => 'true',
					'type'  => 'boolean',
					'group' => 'system',
				],

				// Email Settings
				[
					'key'   => 'email_from_name',
					'value' => 'Acme CRM',
					'type'  => 'string',
					'group' => 'email',
				],
				[
					'key'   => 'email_from_address',
					'value' => 'noreply@acmecrm.com',
					'type'  => 'string',
					'group' => 'email',
				],
			];

			foreach ($settings as $setting) {
				Setting::query()->updateOrCreate(
					['key' => $setting['key']],
					$setting
				)->update([
					'updated_at' => today()->setHour(mt_rand(0, 23))->setMinute(mt_rand(0, 59))->setSecond(mt_rand(0, 59))->toDateTimeString()
				]);
			}

			$this->command->info('SettingsTableSeeder completed successfully. ' . count($settings) . ' settings seeded.');
		}
	}
