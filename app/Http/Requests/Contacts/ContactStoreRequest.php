<?php

	namespace App\Http\Requests\Contacts;

	use Illuminate\Contracts\Validation\ValidationRule;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Str;

	class ContactStoreRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			return Auth::check();
		}

		/**
		 * Get the validation rules that apply to the request.
		 *
		 * @return array<string, ValidationRule|array|string>
		 */
		public function rules(): array {
			return [
				'first_name' => [
					'required',
					'string',
					'max:255'
				],
				'last_name'  => [
					'nullable',
					'string',
					'max:255'
				],
				'account_id' => [
					'required',
					'exists:accounts,id'
				],
				'email'      => [
					'nullable',
					'email',
					'max:255',
					'unique:contacts,email'
				],
				'phone'      => [
					'nullable',
					'string',
					'max:50'
				],
				'job_title'  => [
					'nullable',
					'string',
					'max:255'
				],
				'address'    => [
					'nullable',
					'string'
				],
				'city'       => [
					'nullable',
					'string',
					'max:255'
				],
				'country'    => [
					'nullable',
					'string',
					'max:255'
				],
				'owner_id'   => [
					'nullable',
					'exists:users,id'
				],
				'notes'      => [
					'nullable',
					'string'
				],
				'is_primary' => [
					'required',
					'boolean'
				],
			];
		}

		/**
		 * Custom error messages for a better user experience.
		 */
		public function messages(): array {
			return [
				'first_name.required' => 'We need at least a first name to create a contact.',
				'account_id.required' => 'Please select a company/account for this contact.',
				'email.unique'        => 'This email address is already assigned to another contact.',
				'is_primary.required' => 'Please specify if this is a primary contact.',
			];
		}

		/**
		 * Prepare data for validation (cast inputs).
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				'is_primary' => $this->boolean('is_primary'),
				// Optional: trim strings early if needed
				'email'      => filter_var($this->input('email'), FILTER_SANITIZE_EMAIL),
			]);
		}

		/**
		 * Groom data after validation succeeds.
		 * We use replace() to ensure the Controller only sees this sanitized set.
		 */
		protected function passedValidation(): void {
			$this->replace([
				'first_name' => Str::title($this->validated('first_name')),
				'last_name'  => $this->validated('last_name') ? Str::title($this->validated('last_name')) : null,
				'account_id' => $this->validated('account_id'),
				'email'      => $this->validated('email') ? Str::lower($this->validated('email')) : null,
				'phone'      => $this->validated('phone'),
				'job_title'  => $this->validated('job_title'),
				'address'    => $this->validated('address'),
				'city'       => $this->validated('city'),
				'country'    => $this->validated('country'),
				'owner_id'   => $this->validated('owner_id') ?? Auth::id(),
				'notes'      => $this->validated('notes'),
				'is_primary' => $this->validated('is_primary'),
			]);
		}
	}
