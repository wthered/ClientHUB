<?php

	namespace App\Http\Controllers;

	use App\Http\Requests\Account\AccountUpdateRequest;
	use App\Models\Account;
	use App\Models\Note;
	use Auth;
	use Illuminate\Http\Request;

	class AccountController extends Controller {
		/**
		 * Display a listing of the accounts.
		 */
		public function index() {
			// Φορτώνουμε contacts & owner
			$accounts = Account::with([
				'owner.profile',
				'contacts'
			])->paginate(10);
			return view('accounts.index', compact('accounts'));
		}

		public function store(Request $request) {
			$validated = $request->validate([
				'name'           => 'required|string|max:255',
				'email'          => 'nullable|email|max:255',
				'industry'       => 'nullable|string|max:100',
				'website'        => 'nullable|url',
				'annual_revenue' => 'nullable|numeric|min:0',
				'employee_count' => 'nullable|integer|min:0',
				// ... υπόλοιπα πεδία
			]);

			// Σύνδεση με τον τρέχοντα χρήστη ως owner_id
			$validated['owner_id']  = auth()->id();
			$validated['is_active'] = true;

			Account::create($validated);

			return redirect()
				->route('accounts.index')
				->with('success', 'Account created!');
		}

		/**
		 * Show the form for creating a new account.
		 */
		public function create() {
			return view('accounts.create');
		}

		public function edit(Account $account) {
			return view('accounts.edit', compact('account'));
		}

		public function show(Request $request, Account $account) {
			// Φορτώνουμε τις σχέσεις για να αποφύγουμε extra queries στο view
			$account->load([
				'owner',
				'contacts'
			]);

			return view('accounts.show', compact('account'));
		}

		public function getContacts(Request $request, Account $account) {
			// Tracking Logic: Ενημέρωση του "Last Activity" του χρήστη
			$request
				->user()
				->update(['last_active_at' => now()]);

			$contacts = $account->contacts;
			return view('partials.accounts.contacts', compact('contacts'))->render();
		}

		public function update(AccountUpdateRequest $request, Account $account) {
			// Το validation έχει ήδη τρέξει αυτόματα λόγω του UpdateAccountRequest
			$data = $request->validated();

			// Χειρισμός του boolean checkbox (is_active) αν δεν σταλεί στο request
			$data['is_active'] = $request->has('is_active');

			$account->update($data);

			return redirect()
				->route('accounts.index')
				->with('success', "Ο λογαριασμός {$account->name} ενημερώθηκε επιτυχώς.");
		}

		public function getInvoices(Account $account) {
			// Φορτώνουμε τα τιμολόγια του λογαριασμού
			$invoices = $account->invoices()->orderBy('invoice_date', 'desc')->get();

			return view('partials.accounts.invoices', compact('account', 'invoices'))->render();
		}

		public function getNotes(Account $account) {
			// Υποθέτοντας ότι έχεις σχέση notes() στο Account Model
			$notes = $account->notes()->with('user')->latest()->get();

			return view('partials.accounts.notes', compact('account', 'notes'))->render();
		}

		public function storeNote(Request $request, Account $account) {
			$validated = $request->validate([
				'content' => 'required|string|min:3',
			]);

			$account->notes()->create([
				'content' => $validated['content'],
				'user_id' => Auth::id(),
			]);

			// Στον Controller, αντί για back(), μπορείς να κάνεις:
			return redirect()->to(url()->previous() . '#notes')->with('success', 'Η σημείωση προστέθηκε!');
		}

		public function destroyNote(Request $request, Note $note) {
			// Προαιρετικά: Έλεγχος αν ο χρήστης έχει δικαίωμα να τη διαγράψει
			if ($note->user_id !== $request->user()->id() && !$request->user()->is_admin) {
				return back()->with('error', 'Δεν έχετε δικαίωμα διαγραφής.');
			}

			$note->delete();

			return back()->with('success', 'Η σημείωση διαγράφηκε.');
		}
	}
