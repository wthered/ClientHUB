<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('stages', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('pipeline_id')->comment('Pipeline this stage belongs to');
				$table->foreign('pipeline_id')->references('id')->on('pipelines')->cascadeOnDelete();
				$table->string('name')->comment('Stage name');
				$table->integer('order')->default(0)->comment('Order of the stage in the pipeline');
				$table->string('status')->default('open')->comment('Status: open, won, or lost');
				$table->timestamps();
				$table->comment('Stages represent individual steps within a pipeline. Deals progress through stages to track sales progress.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('stages');
		}
	};
