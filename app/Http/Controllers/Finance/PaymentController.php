<?php

	namespace App\Http\Controllers\Finance;

	use App\Http\Controllers\Controller;
	use App\Http\Requests\Payments\PaymentIndexRequest;
	use App\Models\Invoices\Invoice;
	use App\Models\Payment;
	use Illuminate\Http\Request;
	use Throwable;

	class PaymentController extends Controller {
		public function index(PaymentIndexRequest $request) {
			$payments = $this->applyFilters($request);
			return view('payments.index', ['payments' => $payments]);
		}

		/**
		 * Κοινή λογική φιλτραρίσματος για να παραμένει ο κώδικας καθαρός
		 */
		private function applyFilters(PaymentIndexRequest $request) {
			return Payment::with('invoice.account')
				->when($request->filled('invoice_id'), function ($q) use ($request) {
					return $q->where('invoice_id', $request->validated('invoice_id'));
				})
				->when($request->filled('method'), function ($q) use ($request) {
					return $q->where('method', $request->validated('method'));
				})
				->when($request->filled('date_from'), function ($q) use ($request) {
					return $q->whereDate('payment_date', '>=', $request->validated('date_from'));
				})
				->when($request->filled('date_to'), function ($q) use ($request) {
					return $q->whereDate('payment_date', '<=', $request->validated('date_to'));
				})
				->latest('payment_date')
				->paginate(25);
		}

		public function store(Request $request, Invoice $invoice) {
			$validated = $request->validate([
				'amount'       => 'required|numeric|min:0.01',
				'payment_date' => 'required|date',
				'method'       => 'required|string',
			]);

			// Δημιουργία της πληρωμής
			$payment = $invoice
				->payments()
				->create($validated);

			// Ο PaymentObserver θα αναλάβει αυτόματα να ενημερώσει
			// το status του Invoice (Paid, Partially Paid κλπ)

			return response()->json([
				'success'    => true,
				'message'    => 'Η πληρωμή καταχωρήθηκε!',
				'new_status' => $invoice->fresh()->status->label()
			]);
		}

		public function show(Request $request, Invoice $invoice) {
			// Φορτώνουμε τα payments μαζί με το τιμολόγιο για καλύτερο performance (Eager Loading)
			$invoice->load([
				'payments',
				'account',
				'items'
			]);
			$payments = $invoice->payments();
			$balance  = $invoice->total_amount - $payments->sum('amount');

			return view('payments.show', [
				'invoice'     => $invoice,
				'payments'    => $payments->get(),
				'balance'     => $balance,
				'isSettled'   => $balance <= 0,
				'alreadyPaid' => $payments->sum('amount')
			]);
		}

		// PaymentController.php

		public function destroy(Payment $payment) {
			$invoice = $payment->invoice;
			$payment->delete();

			// Σημείωση: Αν υπάρχει Observer, θα ενημερώσει το Invoice status.
			// Αν όχι, θα έπρεπε να γίνει εδώ.

			return back()->with('success', 'Η πληρωμή διαγράφηκε με επιτυχία.');
		}

		/**
		 * @throws Throwable
		 */
		public function filter(PaymentIndexRequest $request) {
			$payments = $this->applyFilters($request);

			// Επιστρέφουμε κατευθείαν το view ως string
			return view('partials.payments.table', ['payments' => $payments])->render();
		}
	}
