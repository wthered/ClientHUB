<?php

	use App\Enums\DealStatus;
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('deals', function (Blueprint $table) {
				$table->increments('id');
				$table->string('title')->index();

				$table->unsignedInteger('account_id')->nullable();
				$table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');

				// Σύνδεση με τον πελάτη (Lead ή Contact)
				$table->unsignedInteger('lead_id')->nullable();
				$table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');

				$table->unsignedInteger('contact_id')->nullable();
				$table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');

				$table->unsignedInteger('opportunity_id')->nullable();
				$table->foreign('opportunity_id')->references('id')->on('opportunities')->onDelete('set null');

				// Σύνδεση με το Pipeline & Stage
				$table->unsignedInteger('pipeline_id');
				$table->foreign('pipeline_id')->references('id')->on('pipelines')->onDelete('restrict');

				$table->unsignedInteger('stage_id');
				$table->foreign('stage_id')->references('id')->on('stages')->onDelete('restrict');

				// Οικονομικά στοιχεία (Συνέπεια με Opportunities/Invoices)
				$table->decimal('value', 15)->default(0);
				$table->char('currency', 3)->default('EUR');

				// Κατάσταση και Ιδιοκτησία
				$table->string('status')->default(DealStatus::OPEN->value)->index();

				$table->unsignedInteger('user_id')->comment('The owner of the deal');
				$table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

				$table->timestamp('expected_close_date')->nullable();
				$table->timestamp('closed_at')->nullable();

				$table->timestamps();
				$table->softDeletes();

				$table->comment('Deals track the final negotiation and closing phase of the sales process.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('deals');
		}
	};
