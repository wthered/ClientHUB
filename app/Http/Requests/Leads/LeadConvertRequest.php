<?php

	namespace App\Http\Requests\Leads;

	use App\Models\Lead;
	use Illuminate\Contracts\Validation\ValidationRule;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Collection;

	class LeadConvertRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			$lead = $this->route('lead');

			// 1. Safety check for Route Model Binding
			if (!$lead instanceof Lead) {
				return false;
			}

			// 2. Check Permission (Triggers Gate::before for Super Admin)
			if ($this->user()->can('convert leads')) {
				return true;
			}

			// 3. Fallback: Check Ownership (Loose comparison for Safety)
			return (int) $this->user()->id === (int) $lead->owner_id;
		}

		/**
		 * Get the validation rules that apply to the request.
		 *
		 * @return array<string, ValidationRule|array|string>
		 */
		public function rules(): array {
			return [
				'account_name'       => [
					'required',
					'string',
					'max:255',
				],
				'create_opportunity' => [
					'nullable',
					'boolean',
				],
			];
		}

		/**
		 * Prepare the data for validation.
		 * This converts "on" from a checkbox into a proper boolean.
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				'create_opportunity' => $this->has('create_opportunity') && ($this->create_opportunity === 'on' || $this->create_opportunity == 1),
			]);
		}

		/**
		 *
		 * Handle custom logic after validation passes.
		 * We prepare a collection for the Controller/DTO.
		 */
		protected function passedValidation(): Collection {
			return Collection::make($this->validated());
		}
	}