<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('companies', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->index();
				$table->text('description')->nullable();
				$table->string('legal_name')->nullable();
				$table->string('vat_number')->nullable()->unique();
				$table->string('tax_office')->nullable();

				$table->string('website')->nullable();
				$table->string('email')->nullable();
				$table->string('phone')->nullable();

				$table->string('industry')->nullable();
				$table->string('logo_path')->nullable()->comment('Link προς τον πίνακα files');

				$table->unsignedInteger('owner_id')->nullable();
				$table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');

				$table->boolean('is_active')->default(true);
				$table->timestamps();
				$table->softDeletes();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('companies');
		}
	};
