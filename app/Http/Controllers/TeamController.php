<?php

	namespace App\Http\Controllers;

	use App\Enums\TeamRole;
	use App\Http\Requests\Teams\AssignUserToTeamRequest;
	use App\Http\Requests\Teams\RemoveUserRequest;
	use App\Http\Requests\Teams\TeamStoreRequest;
	use App\Http\Requests\Teams\TeamUpdateRequest;
	use App\Models\Company;
	use App\Models\Team;
	use App\Models\Users\User;
	use Illuminate\Contracts\View\Factory;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Support\Facades\DB;
	use Illuminate\View\View;
	use Throwable;

	class TeamController extends Controller {
		/**
		 * Λίστα όλων των ομάδων
		 */
		public function index() {
			$teams = Team::with([
				'leader',
				'manager',
				'company',
				'members'
			])->get();
			return view('admin.teams.index', compact('teams'));
		}

		/**
		 * Αποθήκευση νέας ομάδας (Store)
		 *
		 * @throws Throwable
		 */
		public function store(TeamStoreRequest $request): RedirectResponse {
			$team = Team::create($request->validated());

			// Αν ορίστηκε Leader κατά τη δημιουργία, συγχρονίζουμε και τον pivot
			if ($request->filled('leader_id')) {
				$this->syncLeaderRole($team, $request->validated('leader_id'));
			}

			return redirect()->route('teams.index')->with('success', 'Team created successfully!');
		}

		/**
		 * Φόρμα δημιουργίας νέας ομάδας (Create)
		 */
		public function create(): Factory|\Illuminate\Contracts\View\View|View {
			$users     = User::with('profile')->get()->sortBy('profile.surname');
			$companies = Company::query()->orderBy('name')->get();

			return view('admin.teams.create', compact('users', 'companies'));
		}

		/**
		 * Εσωτερική μέθοδος για τον πλήρη συγχρονισμό του Leader ρόλου
		 *
		 * @throws Throwable
		 */
		private function syncLeaderRole(Team $team, $userId): void {
			DB::transaction(function () use ($team, $userId) {
				// 1. Βρίσκουμε ποιοι είναι ΗΔΗ leaders στον pivot και τους κάνουμε members
				DB::table('team_user')->where('team_id', $team->id)->where('role', TeamRole::LEADER->value)->update(['role' => TeamRole::MEMBER->value]);

				// 2. Ενημέρωση του leader_id στο κεντρικό table 'teams'
				$team->update(['leader_id' => $userId]);

				// 3. Ορισμός του νέου leader στον pivot
				// Το syncWithoutDetaching θα κάνει update αν υπάρχει ή insert αν δεν υπάρχει
				$team->members()->syncWithoutDetaching([
					$userId => ['role' => TeamRole::LEADER->value]
				]);
			});
		}

		/**
		 * Ενημέρωση των βασικών στοιχείων της ομάδας στη βάση
		 *
		 * @throws Throwable
		 */
		public function update(TeamUpdateRequest $request, Team $team) {
			$input = $request->validated();

			$team->update($input);

			// Αν έχεις και το logic για τον Leader pivot:
			if ($request->filled('leader_id')) {
				$this->syncLeaderRole($team, $request->validated('leader_id'));
			}

			return redirect()->route('teams.index')->with('success', 'Team updated!');
		}

		/**
		 * Προβολή συγκεκριμένης ομάδας και φόρμα ανάθεσης
		 */
		public function show(Team $team) {
			$team->load([
				'members' => function ($query) {
					$query->with('profile'); // Φόρτωση προφίλ για κάθε μέλος
				},
				'leader.profile'
			]);

			// Φέρνουμε μόνο τους χρήστες που ΔΕΝ ανήκουν σε αυτή την ομάδα
			$availableUsers = User::with('profile')
				->whereDoesntHave('teams', function ($query) use ($team) {
					$query->where('team_id', $team->id);
				})
				->orderBy('name')
				->get();

			return view('admin.teams.show', [
				'team'  => $team,
				'roles' => TeamRole::cases(),
				'users' => $availableUsers,
			]);
		}

		/**
		 * Φόρμα επεξεργασίας των στοιχείων της ομάδας (Όνομα, Leader κλπ)
		 */
		public function edit(Team $team) {
			// Φορτώνουμε τον τωρινό leader για να είναι επιλεγμένος στο dropdown
			$team->load('leader');

			$users     = User::with('profile')
				->orderBy('name')
				->get();
			$companies = Company::query()
				->orderBy('name')
				->get();

			return view('admin.teams.edit', [
				'team'      => $team,
				'users'     => $users,
				'companies' => $companies
			]);
		}

		/**
		 * Ανάθεση χρήστη σε ομάδα (από τη φόρμα στη sidebar)
		 *
		 * @throws Throwable
		 */
		public function assignUser(AssignUserToTeamRequest $request, Team $team) {
			$role = $request->input('role', TeamRole::MEMBER->value);

			// Αν ο χρήστης μπαίνει ως Leader από τη φόρμα,
			// πρέπει να ενημερώσουμε και το leader_id της ομάδας.
			if ($role === TeamRole::LEADER->value) {
				$this->syncLeaderRole($team, $request->user_id);
			} else {
				$team->members()->syncWithoutDetaching([
					$request->user_id => ['role' => $role]
				]);
			}

			return redirect()->back()->with('success', 'User added to the team successfully.');
		}

		/**
		 * Ορισμός Leader (από το αστέρι στον πίνακα)
		 *
		 * @throws Throwable
		 */
		public function setLeader(Team $team, User $user) {
			$this->syncLeaderRole($team, $user->id);

			// Προαιρετικό αλλά βοηθητικό για να είσαι σίγουρος
			$team->refresh();

			return redirect()
				->back()
				->with('success', "{$user->profile->full_name} is now leading the team!");
		}

		/**
		 * @throws Throwable
		 */
		public function removeUser(RemoveUserRequest $request, Team $team) {
			$userId = $request->input('user_id');

			DB::transaction(function () use ($team, $userId) {
				// 1. Αν ο χρήστης που διαγράφεται είναι ο Leader της ομάδας
				if ($team->leader_id == $userId) {
					$team->update(['leader_id' => null]);

					// Προαιρετικά: Μπορείς να στείλεις ένα alert στο log
					// ή να αναθέσεις αυτόματα τον Manager ως προσωρινό Leader
				}

				// 2. Αφαίρεση από τον pivot πίνακα
				$team
					->members()
					->detach($userId);
			});

			return redirect()
				->back()
				->with('success', 'User removed from the team.');
		}


	}
