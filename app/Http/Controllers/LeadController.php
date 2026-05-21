<?php

	namespace App\Http\Controllers;

	use App\Http\Requests\Leads\LeadConvertRequest;
	use App\Http\Requests\Leads\LeadStoreRequest;
	use App\Http\Requests\Leads\LeadUpdateRequest;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Opportunities\Stage;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use Log;
	use Throwable;

	class LeadController extends Controller {
		public function index(Request $request)
		{
			$leads = Lead::query()
				->with(['owner'])
				// 1. Apply the filter scope FIRST (while it's still a Query Builder)
				->filter($request->only([
					'search',
					'status',
					'priority'
				]))
				// 2. Order the results
				->latest()
				// 3. Paginate LAST (this executes the query)
				->paginate(15)
				// 4. Carry the URL parameters to the next page links
				->withQueryString();

			return view('leads.index', compact('leads'));
		}

		public function store(LeadStoreRequest $request) {
			dd($request->input());
		}

		public function show(Lead $lead) {
			return view('leads.show', compact('lead'));
		}

		public function edit(Lead $lead) {
			// Ensure the user is authorized to edit this specific lead
			// $this->authorize('update', $lead);

			return view('leads.edit', compact('lead'));
		}

		/**
		 * @throws Throwable
		 */
		public function convert(LeadConvertRequest $request, Lead $lead) {
			// 1. Enhanced Guard Clause
			// Uses the new Model helpers to ensure we don't convert junk or already converted leads.
			if (!$lead->isConvertible()) {
				return redirect()
					->back()
					->with('error', 'This lead cannot be converted in its current state.');
			}

			// 2. Data Preparation
			$data = $request->validated();

			// 3. Pipeline & Stage Resolution
			// We target Pipeline 1 (Standard Sales) and grab the first stage by 'order'.
			$initialStage = Stage::query()
				->where('pipeline_id', 1)
				->orderBy('order', 'asc')
				->first();

			DB::beginTransaction();

			try {
				// 4. Execute Conversion via Model Logic
				// This handles the creation of Account and Contact in a single transaction.
				$result = $lead->convert(accountData: [
						'name'    => $data['account_name'],
						'website' => $lead->website,
					], contactData: [
						'first_name' => $lead->first_name,
						'last_name'  => $lead->last_name,
						'email'      => $lead->email,
						'phone'      => $lead->phone,
						'job_title'  => $lead->job_title,
					]);

				$account = $result['account'];
				$contact = $result['contact'];

				// 5. Conditional Opportunity Creation
				// We only proceed if the checkbox was ticked in the form.
				if ($request->boolean('create_opportunity')) {
					$opportunity = Opportunity::query()
						->create([
							'name'       => $account->name . ' - Opportunity',
							'account_id' => $account->id,
							'contact_id' => $contact->id,
							'owner_id'   => $lead->owner_id,
							'stage_id'   => $initialStage?->id,
							'status'     => 'open',
							'amount'     => (float) ($lead->estimated_value ?? 0),
							'currency'   => $lead->currency ?? 'EUR',
						]);

					// 5.1 Attach Owner to the Team (Pivot Table)
					$opportunity
						->users()
						->attach($lead->owner_id, [
							'role' => 'owner',
						]);

					// 5.2 Link the Opportunity back to the Lead for tracking
					$lead->update(['converted_to_opportunity_id' => $opportunity->id]);
				}

				DB::commit();

				return redirect()
					->route('contacts.show', $contact->id)
					->with('success', "Mission Accomplished! {$contact->full_name} is now a Contact.");

			} catch (Throwable $e) {
				DB::rollBack();

				Log::error("Lead Conversion Failed [Lead ID: {$lead->id}]: " . $e->getMessage(), [
					'exception' => $e,
					'payload'   => $data
				]);

				return redirect()
					->back()
					->withInput()
					->with('error', 'Conversion failed. Please check the system logs.');
			}
		}

		public function create() {
			return view('leads.create');
		}

		public function update(LeadUpdateRequest $request, Lead $lead) {
			// The LeadStoreRequest handles validation and data cleaning
			$lead->update($request->validated());

			return redirect()
				->route('leads.index')
				->with('success', "Lead " . $lead->full_name . " has been updated successfully.");
		}
	}
