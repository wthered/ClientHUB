<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('invoices', function (Blueprint $table) {
				$table->increments('id');

				// Relationships
				$table->unsignedInteger('account_id')->comment('Client billed by this invoice');
				$table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();

				$table->unsignedInteger('opportunity_id')->nullable()->comment('Opportunity associated with this invoice');
				$table->foreign('opportunity_id')->references('id')->on('opportunities')->nullOnDelete();

				// Identification
				$table->string('invoice_number')->unique()->comment('Unique invoice identifier');

				// Financials
				// Financials
				$table->decimal('net_amount', 12, 2)->default(0)->comment('Amount before tax');
				$table->decimal('tax_amount', 12, 2)->default(0)->comment('Total tax amount');
				$table->decimal('total_amount', 12, 2)->default(0)->comment('Final amount including tax');
				$table->decimal('paid_amount', 12, 2)->default(0)->comment('Total amount paid so far');
				$table->char('currency', 3)->default('EUR')->comment('ISO 4217 currency code');

				// Dates
				$table->date('invoice_date')->nullable()->comment('Date invoice was issued');
				$table->date('due_date')->nullable()->comment('Date invoice is due');

				// Status (We will link this to your Enum later)
				$table->string('status')->default('unpaid')->index()->comment('Invoice status: unpaid, paid, overdue, etc.');

				$table->text('notes')->nullable()->comment('Public notes visible on the invoice');
				$table->text('internal_notes')->nullable()->comment('Notes for staff only, not on PDF');

				$table->timestamps();
				$table->softDeletes();
			});

			Schema::create('invoice_items', function (Blueprint $table) {
				$table->increments('id');

				// Σύνδεση με το τιμολόγιο
				$table->unsignedInteger('invoice_id');
				$table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();

				// Προαιρετική σύνδεση με Προϊόν (για να ξέρεις ΤΙ πουλάς)
				$table->unsignedInteger('product_id')->nullable();
				$table->foreign('product_id')->references('id')->on('products')->nullOnDelete();

				$table->string('description')->comment('Περιγραφή υπηρεσίας/προϊόντος');

				// Οικονομικά στοιχεία (Συνέπεια με το 12, 2 που έχεις στα Invoices)
				$table->decimal('unit_price', 7, 2)->default(0);
				$table->unsignedInteger('quantity')->default(1);

				// Το συνολικό ποσό της γραμμής (unit_price * quantity)
				$table->decimal('amount', 12, 2)->default(0)->comment('Το συνολικό ποσό της γραμμής (unit_price * quantity)');

				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('invoices');
		}
	};
