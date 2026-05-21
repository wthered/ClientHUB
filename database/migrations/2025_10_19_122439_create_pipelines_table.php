<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('pipelines', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->comment('Pipeline name');
				$table->text('description')->nullable()->comment('Description of the pipeline');
				$table->timestamps();
				$table->softDeletes();
				$table->comment('Used for organizing deals into logical workflows. You could have multiple pipelines like Software Sales, Consulting Sales, each with its own stages.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('pipelines');
		}
	};
