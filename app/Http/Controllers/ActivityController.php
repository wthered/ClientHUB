<?php

	namespace App\Http\Controllers;

	use App\Enums\ModelType;
	use App\Filters\ActivityLogFilters;
	use App\Http\Requests\Activities\ActivityFilterRequest;
	use App\Models\Activity;
	use App\Models\ActivityLog;
	use App\Models\Users\User;
	use Artisan;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Symfony\Component\HttpFoundation\StreamedResponse;
	use Throwable;

	class ActivityController extends Controller {

		/**
		 * @throws Throwable
		 */
		public function index(ActivityFilterRequest $request, ActivityLogFilters $filters) {
			$activities = ActivityLog::with([
				'user.profile',
				'user.roles'
			])->filter($filters)->latest()->paginate(25);

			// Αν είναι AJAX request
			if ($request->ajax()) {
				return response()->json([
					'html'       => view('partials.activities.rows', compact('activities'))->render(),
					'count'      => $activities->total(),
					'pagination' => $activities->links('partials.pagination')->render()
				]);
			}

			// Κανονικό φόρτωμα σελίδας
			return view('activities.index', [
				'activities' => $activities,
				'users'      => User::all(),
				'models'     => ModelType::cases(),
			]);
		}

		/**
		 * Αποθήκευση νέας δραστηριότητας (Call, Meeting, κλπ)
		 */
		public function store(Request $request) {
			$validated = $request->validate([
				'type'             => 'required|string',
				// call, meeting, email, task
				'subject'          => 'required|string|max:255',
				'description'      => 'nullable|string',
				'due_at'           => 'nullable|date',
				'activitable_id'   => 'required|integer',
				'activitable_type' => 'required|string',
				'activity_type_id' => 'nullable|exists:activity_types,id',
			]);

			// Προσθήκη του owner (του τρέχοντος χρήστη)
			$validated['owner_id']     = Auth::id();
			$validated['status']       = $request->has('is_completed') ? 'completed' : 'pending';
			$validated['is_completed'] = $request->has('is_completed');

			$activity = Activity::create($validated);

			return response()->json([
				'success'  => true,
				'message'  => 'Η δραστηριότητα καταγράφηκε!',
				'activity' => $activity->load('owner.profile')
			]);
		}

		/**
		 * Γρήγορη ολοκλήρωση (Toggle) μιας δραστηριότητας
		 */
		public function toggleComplete(Activity $activity) {
			$activity->update([
				'is_completed' => !$activity->is_completed,
				'status'       => !$activity->is_completed ? 'completed' : 'pending',
				'completed_at' => !$activity->is_completed ? now() : null,
			]);

			return back()->with('success', 'Η κατάσταση της δραστηριότητας ενημερώθηκε.');
		}

		public function export(Request $request, ActivityLogFilters $filters) {
			// Χρησιμοποιούμε query builder για να αποφύγουμε το hydration χιλιάδων Eloquent objects
			$activities = ActivityLog::filter($filters)
				->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
				->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
				->select([
					'activity_logs.created_at', // Χρησιμοποιούμε created_at από το log
					'user_profiles.first_name',
					'user_profiles.last_name',
					'activity_logs.log_type',
					'activity_logs.event',
					'activity_logs.description',
					'activity_logs.ip_address'
				])
				->latest('activity_logs.updated_at')
				->toBase()
				->get();

			$response = new StreamedResponse(function () use ($activities) {
				$handle = fopen('php://output', 'w');
				fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

				fputcsv($handle, [
					'Ημερομηνία',
					'Χρήστης',
					'Τύπος',
					'Ενέργεια',
					'Περιγραφή',
					'IP Address'
				], ';');

				foreach ($activities as $log) {
					fputcsv($handle, [
						// Με toBase() το created_at είναι string, οπότε:
						date('d/m/Y H:i', strtotime($log->created_at)),
						$log->first_name ? ($log->first_name . ' ' . $log->last_name) : 'System',
						$log->log_type,
						$log->event,
						$log->description,
						$log->ip_address
					], ';');
				}

				fclose($handle);
			});

			$response->headers->set('Content-Type', 'text/csv; charset=utf-8');
			$response->headers->set('Content-Disposition', 'attachment; filename="crm_export_' . now()->format('Y-m-d_H-i') . '.csv"');

			return $response;
		}

		public function getDetails(ActivityLog $activity) {
			return response()->json([
				'html' => view('partials.activities.properties', ['props' => $activity->properties])->render()
			]);
		}

		public function clearOldLogs() {
			// Καλούμε το artisan command
			Artisan::call('logs:clear', [
				'--months' => 6
			]);

			// Παίρνουμε το μήνυμα επιτυχίας από το output του command
			$output = Artisan::output();

			return back()->with('success', $output);
		}
	}