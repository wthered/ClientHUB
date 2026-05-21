<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('products', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name');
				$table->string('sku')->unique()->nullable();
				$table->text('description')->nullable();
				$table->decimal('price', 10);
				$table->decimal('cost', 10)->nullable();
				$table->string('unit')->nullable()->comment('hour, piece, month, etc.');
				$table->boolean('is_active')->default(true);
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('products');
		}
	};
