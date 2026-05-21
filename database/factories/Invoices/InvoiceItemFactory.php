<?php

	namespace Database\Factories\Invoices;

	use App\Models\Invoices\Invoice;
	use App\Models\Product;
	use Illuminate\Database\Eloquent\Factories\Factory;

	class InvoiceItemFactory extends Factory {
		public function definition(): array {
			$unitPrice = $this->faker->randomFloat(2, 10, 500);
			$quantity  = $this->faker->numberBetween(1, 5);

			return [
				// Δημιουργεί αυτόματα Invoice αν δεν δώσεις ID
				'invoice_id'  => Invoice::factory(),

				// Προαιρετική σύνδεση με τυχαίο προϊόν
				'product_id'  => Product::inRandomOrder()->first()?->id,

				'description' => $this->faker->sentence(4),
				'unit_price'  => $unitPrice,
				'quantity'    => $quantity,
				'amount'      => $unitPrice * $quantity,
			];
		}
	}
