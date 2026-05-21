<?php

	namespace App\Http\Requests\Leads;

	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Str;

	class LeadStoreRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 * // TO-DO: Integrate LeadPolicy here.
		 */
		public function authorize(): bool {
			return Auth::check();
		}

		/**
		 * Get the validation rules that apply to the request.
		 */
		public function rules(): array {
			return [
				'first_name'      => [
					'required',
					'string',
					'max:100'
				],
				'last_name'       => [
					'required',
					'string',
					'max:100'
				],
				'job_title'       => [
					'nullable',
					'string',
					'max:150'
				],
				'company_name'    => [
					'nullable',
					'string',
					'max:200'
				],
				'email'           => [
					'nullable',
					'email',
					'max:255'
				],
				'phone'           => [
					'nullable',
					'string',
					'max:20'
				],
				'website'         => [
					'nullable',
					'url',
					'max:255'
				],
				'status'          => [
					'required',
					'string',
					'in:new,contacted,qualified'
				],
				'priority'        => [
					'required',
					'string',
					'in:low,medium,high,urgent'
				],
				'source'          => [
					'nullable',
					'string',
					'max:50'
				],
				'estimated_value' => [
					'nullable',
					'numeric',
					'min:0'
				],
				'notes'           => [
					'nullable',
					'string',
					'max:2000'
				],
			];
		}

		/**
		 * Custom error messages for a better UX.
		 */
		public function messages(): array {
			return [
				'first_name.required' => 'We need at least a first name to start a lead.',
				'last_name.required'  => 'The last name is required for CRM integrity.',
				'status.in'           => 'Please select a valid pipeline status.',
				'website.url'         => 'The website must be a valid URL (starting with https://).',
			];
		}

		/**
		 * Prepare the data for validation (Sanitization).
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				'email'   => $this->input('email') ? Str::lower(trim($this->input('email'))) : null,
				'phone'   => $this->input('phone') ? preg_replace('/[^0-9+]/', '', $this->input('phone')) : null,
				'website' => $this->input('website') ? Str::start($this->input('website'), 'https://') : null,
			]);
		}

		/**
		 * Final data formatting after validation is successful.
		 */
		protected function passedValidation(): void {
			$this->merge([
				'first_name' => Str::ucfirst($this->validated('first_name')),
				'last_name'  => Str::ucfirst($this->validated('last_name')),
				'owner_id'   => Auth::id(),
				// Silently assign the current user as the lead owner
			]);
		}
	}
