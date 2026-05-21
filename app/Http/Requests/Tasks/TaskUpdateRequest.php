<?php

	namespace App\Http\Requests\Tasks;

	use App\Enums\Tasks\TaskPriority;
	use App\Enums\Tasks\TaskStatus;
	use Illuminate\Contracts\Validation\ValidationRule;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\Rules\Enum;

	class TaskUpdateRequest extends FormRequest {
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
				'subject'     => [
					'required',
					'string',
					'max:255'
				],
				'description' => [
					'nullable',
					'string'
				],
				'status'      => [
					'required',
					new Enum(TaskStatus::class)
				],
				'priority'    => [
					'required',
					new Enum(TaskPriority::class)
				],
				'due_date'    => [
					'nullable',
					'date'
				],
				'user_id'     => [
					'required',
					'exists:users,id'
				],
			];
		}

		/**
		 * Χρησιμοποιούμε τα labels από το translation file για τα error messages
		 */
		public function attributes(): array {
			return [
				'subject'     => __('tasks.labels.subject'),
				'description' => __('tasks.labels.description'),
				'status'      => __('tasks.labels.status'),
				'priority'    => __('tasks.labels.priority'),
				'due_date'    => __('tasks.labels.due_date'),
				'user_id'     => __('tasks.labels.assigned_to'),
			];
		}

		/**
		 *
		 */
	}
