<?php

	namespace App\Http\Requests\Leads;

	use App\Enums\Leads\LeadStatus;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Validation\Rule;
	use Illuminate\Validation\Rules\Enum;

	class LeadUpdateRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			$lead = $this->route('lead');

			// 1. Use the 'can' method to trigger Gate::before (Super Admin override)
			// 2. Or allow if the user is the explicit owner of the lead.
			return $this->user()->can('edit leads') || (int) $this->user()->id === (int) $lead->owner_id;
		}

		/**
		 * Get the validation rules that apply to the request.
		 */
		public function rules(): array {
			$leadId = $this->route('lead')->id;

			return [
				// Personal & Company
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
					'required',
					'string',
					'max:200'
				],

				// Communication (Unique check ignores current ID)
				'email'           => [
					'required',
					'email',
					'max:255',
					Rule::unique('leads')->ignore($leadId),
				],
				'phone'           => [
					'nullable',
					'string',
					'max:50'
				],
				'website'         => [
					'nullable',
					'url',
					'max:255'
				],

				// Pipeline & Logic
				'status'          => [
					'required',
					new Enum(LeadStatus::class)
				],
				'priority'        => [
					'required',
					'string',
					'in:low,medium,high,urgent'
				],
				'source'          => [
					'required',
					'string',
					'max:100'
				],
				'estimated_value' => [
					'nullable',
					'numeric',
					'min:0',
					'max:9999999.99'
				],

				// Metadata
				'notes'           => [
					'nullable',
					'string',
					'max:5000'
				],
				'is_active'       => [
					'sometimes',
					'boolean'
				],
			];
		}

		protected function prepareForValidation(): void {
			$this->merge([
				// Standardize email for unique check consistency
				'email'     => strtolower(trim($this->input('email'))),
				// Ensure boolean checkbox handling
				'is_active' => $this->has('is_active'),
			]);
		}
	}
