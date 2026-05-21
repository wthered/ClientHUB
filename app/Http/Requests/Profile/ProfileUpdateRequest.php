<?php

	namespace App\Http\Requests\Profile;

	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\Rule;

	class ProfileUpdateRequest extends FormRequest {
		public function authorize(): bool {
			return true;
		}

		public function rules(): array {

			return [
				'email'      => [
					'required',
					'email',
					'max:255',
					Rule::unique('users')->ignore(Auth::id())
				],
				'first_name' => [
					'required',
					'string',
					'min:2',
					'max:100'
				],
				'last_name'  => [
					'required',
					'string',
					'min:2',
					'max:100'
				],
				'phone'      => [
					'nullable',
					'string',
					'regex:/^2[0-9]{9}$/'
				],
				'position'   => [
					'nullable',
					'string',
					'max:150'
				],
				'bio'        => [
					'nullable',
					'string',
					'max:1000'
				],
				'avatar'     => [
					'nullable',
					'image',
					'mimes:jpeg,png,jpg',
					'max:2048'
				],
			];
		}

		/**
		 * Εξατομικευμένα μηνύματα για κάθε πιθανό σφάλμα.
		 */
		public function messages(): array {
			return [
				'email.required' => 'Η διεύθυνση email είναι απαραίτητη.',
				'email.email'    => 'Παρακαλώ εισάγετε μια έγκυρη διεύθυνση email.',
				'email.unique'   => 'Αυτό το email χρησιμοποιείται ήδη από άλλον χρήστη.',

				'first_name.required' => 'Το όνομα είναι υποχρεωτικό πεδίο.',
				'first_name.min'      => 'Το όνομα πρέπει να έχει τουλάχιστον :min χαρακτήρες.',

				'last_name.required' => 'Το επώνυμο είναι υποχρεωτικό πεδίο.',

				'phone.regex' => 'Το τηλέφωνο πρέπει να είναι 10ψήφιο ελληνικό σταθερό (ξεκινάει από 2).',

				'avatar.image' => 'Το αρχείο πρέπει να είναι εικόνα (jpg, png).',
				'avatar.mimes' => 'Επιτρέπονται μόνο αρχεία τύπου: jpeg, png, jpg.',
				'avatar.max'   => 'Η εικόνα δεν μπορεί να ξεπερνά τα 2MB.',

				'bio.max' => 'Το βιογραφικό είναι πολύ μεγάλο (μέγιστο 1000 χαρακτήρες).',
			];
		}

		/**
		 * Αν θέλεις να αλλάξεις τα ονόματα των πεδίων στα μηνύματα λάθους
		 */
		public function attributes(): array {
			return [
				'first_name' => 'Όνομα',
				'last_name'  => 'Επώνυμο',
				'email'      => 'Email Διεύθυνση',
				'phone'      => 'Τηλέφωνο',
				'position'   => 'Θέση Εργασίας',
				'avatar'     => 'Φωτογραφία Προφίλ',
			];
		}

		/**
		 * Εδώ "καθαρίζουμε" τα δεδομένα πριν το Validation.
		 */
		protected function prepareForValidation(): void {
			if ($this->has('phone')) {
				$this->merge([
					// Αφαιρούμε κενά, παύλες και τελείες από το τηλέφωνο
					'phone' => preg_replace('/[^0-9]/', '', $this->input('phone')),
				]);
			}
		}
	}
