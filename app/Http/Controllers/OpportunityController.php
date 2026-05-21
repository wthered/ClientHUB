<?php

	namespace App\Http\Controllers;

	use App\Enums\DealStatus;
	use App\Enums\Opportunities\OpportunityStageStatus;
	use App\Http\Requests\Opportunities\OpportunityUpdateRequest;
	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Deal;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Opportunities\Stage;
	use App\Models\Pipeline;
	use App\Models\Tag;
	use App\Models\Users\User;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;

	class OpportunityController extends Controller {
		/**
		 * Display a listing of the resource.
		 */
		public function index(Request $request) {
			$query = Opportunity::query()->with([
				'account',
				'contact',
				'stage',
				'owner'
			]);

			// Filter by Stage
			if ($request->filled('stage_id')) {
				$query->where('stage_id', $request->input('stage_id'));
			}

			// Filter by Status (open, won, lost)
			if ($request->filled('status')) {
				$query->where('status', $request->input('status'));
			}

			// Simple Search
			if ($request->filled('search')) {
				$query->where('name', 'like', '%' . $request->input('search') . '%');
			}

			return view('opportunities.index', [
				'opportunities' => $query->latest()->paginate(15)->withQueryString(),
				'stages'        => Stage::with('pipeline')->orderBy('pipeline_id')->orderBy('order')->get(),
			]);
		}

		public function create() { /* ... */ }

		public function store(Request $request) { /* ... */ }

		public function show(Opportunity $opportunity) {
			$opportunity->load([
				'owner.profile',
				'users.profile',
				'tags',
				'items.product',
				'account',
				'contact',
				'stage',
				'activities' => function($query) {
					// Φορτώνουμε τον 'owner' του activity (τον πωλητή που έκανε την ενέργεια)
					$query->with('owner.profile')->latest()->limit(20);
				}
			]);

			return view('opportunities.show', compact('opportunity'));
		}

		public function edit(Opportunity $opportunity) {
			return view('opportunities.edit', [
				'opportunity'  => $opportunity,
				'accounts'     => Account::query()->orderBy('name')->get(),
				'contacts'     => Contact::where('account_id', $opportunity->account_id)->get(),
				'users'        => User::query()->with(['profile'])->get(),
				'pipelines'    => Pipeline::with('stages')->get(),
				'allTags'      => Tag::query()->where('is_active', true)->get(),
				'lostStageIds' => Stage::query()->where('status', OpportunityStageStatus::LOST)->pluck('id'),
			]);
		}

		public function destroy(Opportunity $opportunity) { /* ... */ }

		public function markWon(Opportunity $opportunity) {
			// 1. Ενημέρωση της Ευκαιρίας
			$opportunity->update([
				'status'      => OpportunityStageStatus::WON->value,
				'probability' => 100,
			]);

			// 2. Δημιουργία του Deal
			// Χρησιμοποιούμε τα δεδομένα της ευκαιρίας για να "γεννήσουμε" το Deal
			$deal = Deal::create([
				'title'          => $opportunity->name,
				'lead_id'        => $opportunity->lead_id, // Αν υπάρχει στο μοντέλο σου
				'opportunity_id' => $opportunity->id,
				'pipeline_id'    => $opportunity->stage->pipeline_id ?? 1, // Fallback αν δεν υπάρχει pipeline
				'stage_id'       => $opportunity->stage_id,
				'value'          => $opportunity->amount,
				'currency'       => $opportunity->currency,
				'user_id'        => Auth::id(), // Ο πωλητής που "έκλεισε" το deal
				'status'         => DealStatus::WON, // Το Enum που φτιάξαμε
				'closed_at'      => now(),
			]);

			return redirect()->route('deals.show', $deal->id)->with('success', __('opportunities.marked_won_success'));
		}

		public function update(OpportunityUpdateRequest $request, Opportunity $opportunity) {
			// 1. Παίρνουμε μόνο τα επικυρωμένα δεδομένα
			$data = $request->validated();

			// 2. Ενημερώνουμε την ευκαιρία.
			// Ο Observer θα "δει" την αλλαγή στο stage_id και θα ρυθμίσει
			// αυτόματα τα status, probability, is_active και loss_reason.
			$opportunity->update($data);

			// 3. Συγχρονισμός Tags
			$opportunity->tags()->sync($data['tags']);

			return redirect()
				->route('opportunities.index')
				->with('success', __('opportunities.update_success'));
		}
	}