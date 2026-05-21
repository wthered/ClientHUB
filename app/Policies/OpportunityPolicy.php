<?php

	namespace App\Policies;

	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;

	class OpportunityPolicy {
		/**
		 * Ο Super Admin έχει πρόσβαση σε όλα αυτόματα.
		 */
		public function before(User $user, string $ability): ?bool {
			if ($user->hasRole('super-admin')) {
				return true;
			}

			return null; // Συνεχίζει στους υπόλοιπους ελέγχους
		}

		/**
		 * Μπορεί ο χρήστης να δει τη λίστα;
		 */
		public function viewAny(User $user): bool {
			return $user->hasPermissionTo('view opportunities');
		}

		/**
		 * Μπορεί ο χρήστης να δει μια συγκεκριμένη ευκαιρία;
		 */
		public function view(User $user, Opportunity $opportunity): bool {
			if (!$user->hasPermissionTo('view opportunities')) {
				return false;
			}

			// Sales Manager & Admin βλέπουν τα πάντα.
			// Sales Rep βλέπει μόνο τα δικά του.
			return $user->hasAnyRole([
					'admin',
					'Sales Manager'
				]) || $user->id === $opportunity->employee_id;
		}

		/**
		 * Μπορεί ο χρήστης να δημιουργήσει;
		 */
		public function create(User $user): bool {
			return $user->hasPermissionTo('create opportunities');
		}

		/**
		 * Μπορεί ο χρήστης να κάνει επεξεργασία;
		 */
		public function update(User $user, Opportunity $opportunity): bool {
			if (!$user->hasPermissionTo('edit opportunities')) {
				return false;
			}

			// Ο Sales Rep μπορεί να επεξεργαστεί μόνο ό,τι του ανήκει.
			return $user->hasAnyRole([
					'admin',
					'Sales Manager'
				]) || $user->id === $opportunity->employee_id;
		}

		/**
		 * Μπορεί ο χρήστης να διαγράψει;
		 */
		public function delete(User $user, Opportunity $opportunity): bool {
			if (!$user->hasPermissionTo('delete opportunities')) {
				return false;
			}

			return $user->hasAnyRole([
					'admin',
					'Sales Manager'
				]) || $user->id === $opportunity->employee_id;
		}
	}