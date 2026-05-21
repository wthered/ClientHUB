<?php

	namespace App\Http\Controllers;

	use App\Enums\Tasks\TaskPriority;
	use App\Enums\Tasks\TaskStatus;
	use App\Filters\TaskFilters;
	use App\Http\Requests\Tasks\TaskFilterRequest;
	use App\Http\Requests\Tasks\TaskUpdateRequest;
	use App\Models\Task;
	use App\Models\Users\User;
	use Exception;
	use Illuminate\Http\Request;

	class TaskController extends Controller {
		/**
		 * Display a listing of the resource.
		 */
		public function index(TaskFilterRequest $request, TaskFilters $filters) {
			$query = Task::with(['user.profile', 'taskable']);

			$tasks = $filters->apply($query)
				->orderBy('due_date', 'asc')
				->paginate(15)
				->withQueryString()
				->through(function ($task) {
					// Μεταφέρουμε το logic εδώ
					$daysRemaining = now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false);
					$isCompleted = $task->status->value === 'completed';

					// Προσθέτουμε "on-the-fly" τα properties στο αντικείμενο
					$task->date_class = $this->getDateClass($isCompleted, $daysRemaining);
					$task->days_text = $this->getDaysText($isCompleted, $daysRemaining);

					return $task;
				});

			return view('tasks.index', [
				'tasks' => $tasks,
				'priorities' => TaskPriority::options(),
				'statuses' => TaskStatus::options(),
			]);
		}

		/**
		 * Helper methods μέσα στον Controller (ή σε ένα Trait)
		 */
		private function getDateClass($isCompleted, $daysRemaining) {
			if ($isCompleted) return 'date-completed';
			if ($daysRemaining < 0) return 'date-overdue';
			if ($daysRemaining <= 2) return 'date-urgent';
			if ($daysRemaining <= 7) return 'date-upcoming';
			return '';
		}

		private function getDaysText($isCompleted, $daysRemaining) {
			if ($isCompleted) return 'Ολοκληρώθηκε';
			if ($daysRemaining < 0) return 'Λήξη πριν ' . abs($daysRemaining) . (abs($daysRemaining) == 1 ? ' μέρα' : ' μέρες');
			if ($daysRemaining == 0) return 'Λήγει σήμερα';
			if ($daysRemaining == 1) return 'Λήγει αύριο';
			return "Σε ".$daysRemaining." μέρες";
		}

		/**
		 * Show the form for creating a new resource.
		 */
		public function create() {
			//
		}

		/**
		 * Store a newly created resource in storage.
		 */
		public function store(Request $request) {
			//
		}

		/**
		 * Display the specified resource.
		 */
		public function show(Task $task) {
			//
		}

		/**
		 * Show the form for editing the specified resource.
		 */
		public function edit(Task $task) {
			return view('tasks.edit', [
				'task' => $task,
				'users' => User::with(['profile'])->get(),
				'statuses' => TaskStatus::cases(),
				'priorities' => TaskPriority::cases(),
			]);
		}

		/**
		 * Update the specified resource in storage.
		 */
		public function update(TaskUpdateRequest $request, Task $task) {
			$validated = $request->validated();

			// Αν το status αλλάξει σε completed, ενημερώνουμε το completed_at
			if ($validated['status'] === TaskStatus::COMPLETED->value && !$task->completed_at) {
				$validated['completed_at'] = now();
			} elseif ($validated['status'] !== TaskStatus::COMPLETED->value) {
				$validated['completed_at'] = null;
			}

			$task->update($validated);

			return redirect()->route('tasks.index')->with('success', __('tasks.messages.updated'));
		}

		/**
		 * Remove the specified resource from storage.
		 */
		public function destroy(Task $task) {
			try {
				$task->delete();
				return back()->with('success', __('tasks.messages.deleted'));
			} catch (Exception $e) {
				// Σε περίπτωση που υπάρχει κάποιο database constraint (π.χ. foreign key)
				return redirect()->back()->with('error', 'Παρουσιάστηκε σφάλμα κατά τη διαγραφή της εργασίας.');
			}
		}
	}
