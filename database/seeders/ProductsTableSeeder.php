<?php

	namespace Database\Seeders;

	use App\Models\Product;
	use Illuminate\Database\Seeder;

	class ProductsTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$products = [
				[
					'name'        => 'Enterprise Software License',
					'sku'         => 'ESL-2026-001',
					'description' => 'Annual enterprise license for our flagship CRM platform',
					'price'       => 8999.00,
					'cost'        => 1200.00,
					'unit'        => 'year',
					'is_active'   => true,
				],
				[
					'name'        => 'Professional Services - Implementation',
					'sku'         => 'PSI-2026',
					'description' => 'Full system implementation and data migration services',
					'price'       => 4500.00,
					'cost'        => 2800.00,
					'unit'        => 'project',
					'is_active'   => true,
				],
				[
					'name'        => 'Monthly Support & Maintenance',
					'sku'         => 'MSM-2026',
					'description' => 'Ongoing technical support and software updates',
					'price'       => 599.00,
					'cost'        => 150.00,
					'unit'        => 'month',
					'is_active'   => true,
				],
				[
					'name'        => 'Premium Training Package',
					'sku'         => 'TRAIN-PREM',
					'description' => '2-day on-site or virtual training for up to 10 users',
					'price'       => 2500.00,
					'cost'        => 800.00,
					'unit'        => 'package',
					'is_active'   => true,
				],
				[
					'name'        => 'Custom Development Hours',
					'sku'         => 'DEV-HOUR',
					'description' => 'Custom feature development and integration work',
					'price'       => 185.00,
					'cost'        => 95.00,
					'unit'        => 'hour',
					'is_active'   => true,
				],
				[
					'name'        => 'Basic Plan - 5 Users',
					'sku'         => 'BASIC-5',
					'description' => 'Basic CRM plan for small teams (5 users)',
					'price'       => 299.00,
					'cost'        => 50.00,
					'unit'        => 'month',
					'is_active'   => true,
				],
			];

			foreach ($products as $productData) {
				Product::query()->updateOrCreate(
					['sku' => $productData['sku']],
					$productData
				);
			}

			$this->command->info('ProductsTableSeeder completed successfully. ' . count($products) . ' products seeded.');
		}
	}
