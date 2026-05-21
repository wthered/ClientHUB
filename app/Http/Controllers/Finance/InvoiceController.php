<?php

	namespace App\Http\Controllers\Finance;

	use App\Enums\InvoiceStatus;
	use App\Http\Controllers\Controller;
	use App\Http\Requests\Invoices\InvoiceFilterRequest;
	use App\Http\Requests\Invoices\InvoiceUpdateRequest;
	use App\Models\Account;
	use App\Models\Invoices\Invoice;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Product;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use Throwable;

	class InvoiceController extends Controller {
		/**
		 * Display a listing of the resource.
		 */
		public function index(InvoiceFilterRequest $request) {
			// Παίρνουμε τα επικυρωμένα δεδομένα
			$filters = $request->validated();

			$query = Invoice::query()->with('account');

			// Εφαρμογή φίλτρων
			$query->when($request->filled('search'), function ($q) use ($request, $filters) {
				$q->where(function ($sub) use ($filters) {
					$sub->where('invoice_number', 'like', "%{$filters['search']}%")->orWhereHas('account', function ($accountQuery) use ($filters) {
						$accountQuery->where('name', 'like', "%" . $filters['search'] . "%");
					});
				});
			});

			$query->when($request->filled('status'), function ($q) use ($request) {
				$q->where('status', $request->status);
			});

			$query->when($request->filled('date_from'), function ($q) use ($request) {
				$q->whereDate('invoice_date', '>=', $request->date_from);
			});

			$query->when($request->filled('date_to'), function ($q) use ($request) {
				$q->whereDate('invoice_date', '<=', $request->date_to);
			});

			$invoices = $query->latest('invoice_date')->paginate(25)->withQueryString();

			// Αν η τρέχουσα σελίδα είναι μεγαλύτερη από την τελευταία...
			if ($invoices->currentPage() > $invoices->lastPage() && $invoices->lastPage() > 0) {
				// Ανακατεύθυνση στην τελευταία υπάρχουσα σελίδα με τα ίδια φίλτρα
				return redirect()->to($request->fullUrlWithQuery(['page' => $invoices->lastPage()]));
			}

			return view('invoices.index', compact('invoices'));
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
		public function show(Invoice $invoice) {
			$invoice->load([
				'account',
				'opportunity'
			]);
			return view('invoices.show', compact('invoice'));
		}

		/**
		 * Show the form for editing the specified resource.
		 */
		public function edit(Invoice $invoice) {
			// Φορτώνουμε τις σχέσεις που ήδη έχεις
			$invoice->load([
				'account',
				'opportunity',
				'items'
			]);

			return view('invoices.edit', [
				'invoice'       => $invoice,
				'accounts'      => Account::query()->orderBy('name')->get(),
				'opportunities' => Opportunity::query()->where('is_active', 1)->orderBy('name')->get(),
				'statuses'      => InvoiceStatus::cases(),
				'already_paid'  => $invoice->payments()->sum('amount'),
				'products'      => Product::query()->orderBy('name')->get(),
			]);
		}

		/**
		 * Update the specified resource in storage.
		 */
		public function update(InvoiceUpdateRequest $request, Invoice $invoice) {
			try {
				DB::beginTransaction();

				// 1. Ενημέρωση των βασικών στοιχείων (account_id, dates κλπ)
				$invoice->update($request->validated());

				// 2. Σβήνουμε τις παλιές γραμμές χρησιμοποιώντας το Relationship
				// Έτσι αν αύριο βάλεις soft deletes, θα δουλέψουν αυτόματα
				$invoice->items()->delete();

				// 3. Προσθήκη των νέων γραμμών
				foreach ($request->validated('items') as $itemData) {
					$invoice->items()->create([
						'product_id'  => $itemData['product_id'],
						'description' => $itemData['description'],
						'amount'      => $itemData['amount'],
						'quantity'    => $itemData['quantity'] ?? 1,
						'unit_price'  => $itemData['unit_price'] ?? $itemData['amount'],
					]);
				}

				DB::commit();

				return redirect()->route('invoices.index')->with('success', __('invoices.messages.updated'));

			} catch (Throwable $e) {
				DB::rollBack();
				return back()->withErrors(['error' => 'Σφάλμα κατά την ενημέρωση: ' . $e->getMessage()]);
			}
		}

		/**
		 * Show the form for creating a new resource.
		 */
		public function create() {
			//
		}

		/**
		 * Remove the specified resource from storage.
		 */
		public function destroy(string $id) {
			//
		}

		public function download(Invoice $invoice) {
			dd("Downloading invoice");
		}

		public function updateStatus(Invoice $invoice) {
			dd("Updating invoice status");
		}

		/**
		 * @throws Throwable
		 */
		public function addNewRow(Request $request) {
			$index    = $request->input('index', 0);
			$products = Product::all();
			return view('partials.invoices.item-row', [
				'index'    => $index,
				'item'     => null,
				'products' => $products,
			]);
		}
	}
