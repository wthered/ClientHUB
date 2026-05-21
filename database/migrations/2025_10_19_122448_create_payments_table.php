<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('payments', function (Blueprint $table) {
				$table->increments('id');

				// Relationships
				$table->unsignedInteger('invoice_id')->comment('Invoice this payment belongs to');
				$table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();

				// Financials
				$table->decimal('amount', 12, 2)->comment('Payment amount');
				$table->char('currency', 3)->default('EUR')->comment('ISO 4217 currency code');

				// Metadata
				$table->string('method')->nullable()->comment('bank_transfer or cash or stripe or whatever');
				$table->string('reference_id')->nullable()->comment('Bank Ref, Stripe ID, Check #');
				$table->text('notes')->nullable()->comment('Internal staff notes about this specific payment');

				$table->date('payment_date')->comment('Date payment was made');
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('payments');
		}
	};
