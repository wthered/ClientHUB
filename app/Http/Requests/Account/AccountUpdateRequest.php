<?php

	namespace App\Http\Requests\Account;

	use Illuminate\Foundation\Http\FormRequest;

	class AccountUpdateRequest extends FormRequest {
		public function authorize(): bool {
			return true; // Εδώ θα μπορούσες να ελέγξεις αν ο Auth::id() == $this->account->owner_id
		}

		public function rules(): array {
			return [
				'name'           => 'required|string|max:255',
				'email'          => 'nullable|email|max:255',
				'phone'          => 'nullable|string|max:50',
				'website'        => 'nullable|url|max:255',
				'industry'       => 'nullable|string|max:100',
				'employee_count' => 'nullable|integer|min:0',
				'annual_revenue' => 'nullable|numeric|min:0',
				'address'        => 'nullable|string',
				'city'           => 'nullable|string|max:100',
				'state'          => 'nullable|string|max:100',
				'country'        => 'nullable|string|max:100',
				'postal_code'    => 'nullable|string|max:20',
				'is_active'      => 'sometimes|boolean',
				'notes'          => 'nullable|string',
			];
		}
	}