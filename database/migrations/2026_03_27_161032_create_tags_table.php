<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('tags', function (Blueprint $table) {
				$table->increments('id');  // INT UNSIGNED - matches your other tables
				$table->string('name')->unique();
				$table->string('slug')->unique();
				$table->string('color')->nullable();
				$table->text('description')->nullable();
				$table->string('group')->nullable();
				$table->boolean('is_active')->default(true);
				$table->timestamps();
				$table->softDeletes();

				$table->comment('Tags for categorizing and organizing records.');
			});

			Schema::create('taggables', function (Blueprint $table) {
				$table->increments('id');  // INT UNSIGNED

				$table->unsignedInteger('tag_id');  // INT UNSIGNED - matches tags.id
				$table->unsignedInteger('taggable_id');  // INT UNSIGNED
				$table->string('taggable_type');

				$table->timestamps();

				$table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

				$table->index(['taggable_type', 'taggable_id']);
				$table->index('tag_id');
				$table->unique(['tag_id', 'taggable_id', 'taggable_type']);

				$table->comment('Polymorphic pivot table for tags.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('tags');
			Schema::dropIfExists('taggables');
		}
	};
