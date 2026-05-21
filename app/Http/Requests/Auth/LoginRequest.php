<?php

	namespace App\Http\Requests\Auth;

	use Illuminate\Contracts\Validation\ValidationRule;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\ValidationException;

	class LoginRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			return true;
		}

		/**
		 * Get the validation rules that apply to the request.
		 *  Οι κανόνες επικύρωσης.
		 *
		 * @return array<string, ValidationRule|array|string>
		 */
		public function rules(): array {
			return [
				'email'    => [
					'required',
					'string',
					'email'
				],
				'password' => [
					'required',
					'string'
				],
			];
		}

		/**
		 * Μεταφέρουμε τη λογική του Auth::attempt εδώ.
		 */
		public function authenticate(): void {
			$credentials = $this->only('email', 'password');

			if (!Auth::attempt($credentials, $this->boolean('remember'))) {
				throw ValidationException::withMessages([
					'email' => __('auth.failed'),
				]);
			}
		}
	}
