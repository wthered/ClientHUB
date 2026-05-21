<?php

	namespace App\Observers;

	use App\Models\Contact;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;


	class ContactObserver {
		/**
		 * Handle the Contact "created" event.
		 */
		public function created(Contact $contact): void {
			DB::table('activity_logs')->insert([
				'log_type'      => 'activity',
				'event'         => 'created',
				'description'   => "Η επαφή '".$contact->full_name."' δημιουργήθηκε.",
				'loggable_id'   => $contact->id,
				'loggable_type' => get_class($contact),
				'user_id'       => Auth::id(),
				'created_at'    => now(),
				'updated_at'    => now(),
			]);
		}

		/**
		 * Handle the Contact "deleted" event.
		 */
		public function deleted(Contact $contact): void {
			DB::table('activity_logs')->insert([
				'log_type'      => 'activity',
				'event'         => 'deleted',
				'description'   => "Η επαφή '".$contact->full_name."' (ID: {$contact->id}) διαγράφηκε.",
				'loggable_id'   => $contact->id,
				'loggable_type' => get_class($contact),
				'user_id'       => Auth::id(),
				'created_at'    => now(),
				'updated_at'    => now(),
			]);
		}

		/**
		 * Handle the Contact "restored" event.
		 */
		public function restored(Contact $contact): void {
			DB::table('activity_logs')->insert([
				'log_type'      => 'activity',
				'event'         => 'restored',
				'description'   => "Η επαφή '".$contact->full_name."' ανακτήθηκε.",
				'loggable_id'   => $contact->id,
				'loggable_type' => get_class($contact),
				'user_id'       => Auth::id(),
				'created_at'    => now(),
				'updated_at'    => now(),
			]);
		}
	}
