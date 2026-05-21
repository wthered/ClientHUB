<?php

	namespace App\Http\Requests\Contacts;

	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Str;

	class ContactUpdateRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			// Καλούμε το Policy (ContactPolicy) χρησιμοποιώντας τη μέθοδο can() του User
			return $this->user()->can('update', $this->route('contact'));
		}

		/**
		 * Get the validation rules that apply to the request.
		 */
		public function rules(): array {
			return [
				'first_name' => ['required', 'string', 'max:255'],
				'last_name'  => ['nullable', 'string', 'max:255'],
				'account_id' => ['required', 'exists:accounts,id'],
				'email'      => ['nullable', 'email', 'max:255'],
				'phone'      => ['nullable', 'string', 'max:50'],
				'job_title'  => ['nullable', 'string', 'max:255'],
				'address'    => ['nullable', 'string'],
				'city'       => ['nullable', 'string', 'max:255'],
				'country'    => ['nullable', 'string', 'max:255'],
				'owner_id'   => ['nullable', 'exists:users,id'],
				'notes'      => ['nullable', 'string'],
				'is_primary' => ['required', 'boolean'],
			];
		}

		/**
		 * Custom error messages for better UX.
		 */
		public function messages(): array {
			return [
				'first_name.required' => 'Please provide a first name for the contact.',
				'account_id.required' => 'Every contact must be linked to an account.',
				'account_id.exists'   => 'The selected account is invalid.',
				'email.email'         => 'Please enter a valid email address.',
				'owner_id.exists'     => 'The assigned owner must be a valid system user.',
			];
		}

		/**
		 * Pre-validation data manipulation.
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				'is_primary' => $this->boolean('is_primary'),
			]);
		}

		/**
		 * Final data grooming after validation passes.
		 */
		protected function passedValidation(): void {
			$this->replace([
				// Ensure names are capitalized correctly (Berlin-style attention to detail)
				'first_name' => Str::title($this->validated('first_name')),
				'last_name'  => Str::title($this->validated('last_name')),
				'account_id' => intval($this->validated('account_id')),
				'email'      => $this->validated('email'),
				'phone'      => $this->validated('phone'),
				'job_title'  => $this->validated('job_title'),
				'address'    => $this->validated('address'),
				'city'       => $this->validated('city'),
				'country'    => $this->validated('country'),
				'owner_id'   => intval($this->validated('owner_id')),
				'notes'      => $this->validated('notes'),
				'is_primary' => $this->validated('is_primary'),
			]);
		}
	}
