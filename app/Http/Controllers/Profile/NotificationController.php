<?php

	namespace App\Http\Controllers\Profile;

	use App\Http\Controllers\Controller;

	class NotificationController extends Controller {
		public function markAllRead() {
			auth()->user()->unreadNotifications->markAsRead();
			return redirect()
				->back()
				->with('success', 'Όλες οι ειδοποιήσεις διαβάστηκαν.');
		}
	}
