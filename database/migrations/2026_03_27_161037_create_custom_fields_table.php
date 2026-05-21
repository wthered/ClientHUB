<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('custom_fields', function (Blueprint $table) {
				$table->increments('id');
				$table->string('model_type')->comment('Which model this field applies to (e.g. Account, Contact, etc)');
				$table->string('name')->comment('Internal field name (snake_case)');
				$table->string('label')->comment('Display label shown to users');
				$table->string('type')->comment('Field type: text, textarea, number, date, select, checkbox, etc.');

				$table->json('options')->nullable()->comment('Options for select/dropdown fields');
				$table->boolean('is_required')->default(false);
				$table->integer('sort_order')->default(0);

				$table->timestamps();

				$table->unique([
					'model_type',
					'name'
				]);

				$table->comment('Defines custom fields available for different models in the CRM.');
			});

			Schema::create('custom_field_values', function (Blueprint $table) {
				$table->increments('id');

				// Polymorphic relationship to the actual record (Account, Contact, Opportunity, etc.)
				$table->unsignedInteger('model_id');
				$table->string('model_type')->comment('Account, Contact, Opportunity, etc.');
				$table->index([
					'model_type',
					'model_id'
				]);

				$table->unsignedInteger('custom_field_id');
				$table->foreign('custom_field_id')->references('id')->on('custom_fields')->onDelete('cascade');

				$table->text('value')->nullable();

				$table->timestamps();

				$table->comment('Stores the actual values for custom fields on various CRM records.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('custom_fields');
		}
	};
