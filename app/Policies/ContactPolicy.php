<?php

	namespace App\Policies;

	use App\Models\Contact;
	use App\Models\Users\User;
	use Illuminate\Auth\Access\HandlesAuthorization;

	class ContactPolicy {
		use HandlesAuthorization;

		/**
		 * Determine whether the user can view the model.
		 */
		public function view(User $user, Contact $contact): bool {
			if (!$user->hasPermissionTo('view contacts')) {
				return false;
			}

			// Αν είναι Sales Rep, βλέπει μόνο όσα του ανήκουν
			if ($user->hasRole('Sales Representative')) {
				return $contact->owner_id === $user->id;
			}

			return true;
		}

		/**
		 * Determine whether the user can update the model.
		 */
		public function update(User $user, Contact $contact): bool {
			if (!$user->hasPermissionTo('edit contacts')) {
				return false;
			}

			// Περιορισμός: Μόνο ο ιδιοκτήτης ή ανώτεροι ρόλοι μπορούν να κάνουν edit
			if ($user->hasRole('Sales Representative')) {
				return $contact->owner_id === $user->id;
			}

			return true;
		}

		// Οι υπόλοιπες μέθοδοι (viewAny, create κλπ) παραμένουν όπως πριν
		public function viewAny(User $user): bool {
			return $user->hasPermissionTo('view contacts');
		}

		public function create(User $user): bool {
			return $user->hasPermissionTo('create contacts');
		}

		public function delete(User $user, Contact $contact): bool {
			return $user->hasPermissionTo('delete contacts');
		}
	}
