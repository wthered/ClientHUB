<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('files', function (Blueprint $table) {
				$table->id();

				// Πού ανήκει το αρχείο; (User, Company, Lead, Activity)
				$table->nullableMorphs('fileable');

				$table->string('original_name');
				$table->string('storage_path');
				$table->string('extension', 10);
				$table->string('mime_type');
				$table->unsignedBigInteger('size')->comment('In bytes');

				$table->string('disk')->default('public')->comment('public, s3, private');
				$table->unsignedInteger('uploaded_by');
				$table->foreign('uploaded_by')->references('id')->on('users');

				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('files');
		}
	};
