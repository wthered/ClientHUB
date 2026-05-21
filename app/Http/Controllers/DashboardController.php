<?php

	namespace App\Http\Controllers;

	use App\Enums\Opportunities\OpportunityStageStatus;
	use App\Models\Activities\ActivityLog;
	use App\Models\Activity;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Auth;

	class DashboardController extends Controller {
		public function index() {
			// Χρήστες που "φάνηκαν" τα τελευταία 5 λεπτά
			$onlineUsers = User::query()->where('last_login_at', '>=', Carbon::now()->subMinutes(5))->where('id', '!=', Auth::id())->get();

			// Τα 10 τελευταία logs συστήματος/ασφάλειας (Audit)
			$recentAuditLogs = ActivityLog::query()->where('log_type', 'audit')
				->latest()
				->take(10)
				->get();

			$openOpportunities = cache()->remember('open_opportunities_count', 600, function () {
				return Opportunity::query()->where('status', OpportunityStageStatus::OPEN->value)->count();
			});

			$todayActivitiesCount = Activity::whereDate('created_at', Carbon::today())->count();

			return view('dashboard', compact('onlineUsers', 'recentAuditLogs', 'openOpportunities', 'todayActivitiesCount'));
		}
	}
