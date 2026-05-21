<?php

	namespace App\Http\Controllers;

	use App\Enums\DealStatus;
	use App\Http\Requests\Contacts\ContactStoreRequest;
	use App\Http\Requests\Contacts\ContactUpdateRequest;
	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Users\User;
	use Illuminate\Support\Facades\Gate;
	use Illuminate\Support\Facades\Auth;

	class ContactController extends Controller {

		public function index() {
			return view('contacts.index', [
				'user'     => Auth::user(),
				'contacts' => Contact::with(['account'])->latest()->paginate(25),
			]);
		}

		public function store(ContactStoreRequest $request) {
			// Create the contact
			$contact = Contact::create($request->validated());

			// Handle primary status logic (optional service class)
			if ($contact->is_primary) {
				$contact->account->contacts()->where('id', '!=', $contact->id)->update(['is_primary' => false]);
			}

			return redirect()->route('contacts.show', $contact)
				->with('success', "Contact {$contact->full_name} created successfully.");
		}

		public function create() {
			return view('contacts.create', [
				'accounts' => Account::query()->orderBy('name')->get(),
				'users'    => User::query()->orderBy('name')->get(),
			]);
		}

		public function show(Contact $contact) {
			// 1. Eager load relationships
			// Φορτώνουμε τα 'deals' ώστε να έχουμε πρόσβαση στα οικονομικά στοιχεία
			$contact->load([
				'account',
				'owner',
				'deals' => function($query) {
					$query->orderBy('created_at', 'desc');
				}
			]);

			// 2. Υπολογισμός Total Sales (Μόνο τα 'won' deals)
			// Χρησιμοποιούμε τη συλλογή (collection) για να αποφύγουμε επιπλέον queries στη βάση
			$contact->total_sales = $contact->deals->where('status', DealStatus::WON)->sum('value');

			// 3. Εύρεση Last Contact (Προς το παρόν από το updated_at ή το τελευταίο deal)
			// Αν αργότερα φτιάξεις πίνακα 'interactions', θα το τραβάμε από εκεί.
			$contact->last_contacted_at = $contact->deals->first()?->created_at ?? $contact->updated_at;

			return view('contacts.show', compact('contact'));
		}

		public function edit(Contact $contact) {
			$accounts = Account::query()
				->orderBy('name')
				->get();
			$users    = User::query()
				->with(['profile'])
				->orderBy('name')
				->get();

			return view('contacts.edit', compact('contact', 'accounts', 'users'));
		}

		public function update(ContactUpdateRequest $request, Contact $contact) {
			$data = $request->validated();

			if ($data['is_primary']) {
				// Silently demote other contacts for this account
				$contact->account->contacts()
					->where('id', '!=', $contact->id)
					->update(['is_primary' => false]);
			}

			$contact->update($data);

			return redirect()->route('contacts.show', $contact)->with('success', 'Contact updated.');
		}

		public function destroy(Contact $contact) {
			// Αν δεν έχει δικαίωμα, θα πετάξει αυτόματα 403 Forbidden
			Gate::authorize('delete', $contact);

			$contact->delete();

			return redirect()->route('contacts.index')
				->with('success', "Η επαφή ".$contact->full_name." διαγράφηκε με επιτυχία.");
		}
	}
