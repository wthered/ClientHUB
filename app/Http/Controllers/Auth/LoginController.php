<?php

	namespace App\Http\Controllers\Auth;

	use App\Http\Controllers\Controller;
	use App\Http\Requests\Auth\LoginRequest;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\ValidationException;

	class LoginController extends Controller {
		public function showLoginForm() {
			return view('auth.login');
		}

		public function login(LoginRequest $request) {
			// 1. Προσπάθεια ταυτοποίησης (μέσω της μεθόδου που φτιάξαμε στο Request)
			$request->authenticate();

			$user = Auth::user();

			// 2. Έλεγχος αν είναι κλειδωμένος (is_locked)
			if ($user->is_locked) {
				Auth::logout();
				throw ValidationException::withMessages([
					'email' => 'Ο λογαριασμός σας είναι κλειδωμένος: ' . ($user->lock_reason ?? 'Άγνωστη αιτία'),
				]);
			}

			// 3. Έλεγχος αν είναι ενεργός (is_active)
			if (!$user->is_active) {
				Auth::logout();
				throw ValidationException::withMessages([
					'email' => 'Ο λογαριασμός σας είναι ανενεργός.',
				]);
			}

			// Αν όλα οκ
			$request->session()->regenerate();

			return redirect()->intended(route('dashboard'));
		}

		public function logout(Request $request) {
			Auth::logout();
			$request->session()->invalidate();
			$request->session()->regenerateToken();

			return redirect(route('login'));
		}
	}
