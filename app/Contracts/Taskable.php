<?php

	namespace App\Contracts;

	interface Taskable {
		/**
		 * Επιστρέφει το εικονίδιο και το όνομα για το Index των Tasks.
		 */
		public function getTaskableLabelAttribute(): string;

		/**
		 * Επιστρέφει το URL για την προβολή του μοντέλου.
		 */
		public function getTaskableUrlAttribute(): string;
	}