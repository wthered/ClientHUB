<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('documents', function (Blueprint $table) {
				$table->increments('id');

				// Μετακίνησε τη γραμμή εδώ για να είναι "μετά το id" φυσικά
				$table->unsignedBigInteger('file_id')->nullable();
				$table->foreign('file_id')->references('id')->on('files')->onDelete('set null');

				// Polymorphic link
				$table->unsignedInteger('documentable_id');
				$table->string('documentable_type');
				$table->index(['documentable_type', 'documentable_id']);

				// Naming & Storage
				$table->string('name')->comment('Display name in the UI');
				$table->string('original_name')->nullable()->comment('Original name (e.g. My_Contract.pdf)');
				$table->string('disk')->default('public')->comment('local, public, s3');

				// Rest of your columns...
				$table->string('checksum')->nullable();
				$table->string('category')->nullable()->index();
				$table->unsignedInteger('uploaded_by');
				$table->foreign('uploaded_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
				$table->text('description')->nullable();
				$table->boolean('is_active')->default(true);
				$table->timestamps();
				$table->softDeletes();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('documents');
		}
	};
