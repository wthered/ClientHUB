<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('notes', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('notable_id')->comment('ID of the related model (Client, Deal, etc.)');
				$table->string('notable_type')->comment('Polymorphic type: the class name of the related model (Client, Deal, etc.)');
				$table->unsignedInteger('user_id')->nullable()->comment('User that wrote the note');
				$table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
				$table->text('content')->comment('Content of the note');
				$table->timestamps();
				$table->index(['notable_id', 'notable_type']);
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('notes');
		}
	};
