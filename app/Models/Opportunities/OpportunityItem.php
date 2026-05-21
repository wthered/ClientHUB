<?php

	namespace App\Models\Opportunities;

	use App\Models\Product;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;

	class OpportunityItem extends Model {
		/**
		 * Ορίζουμε ρητά τον πίνακα, καθώς το migration σου είναι 'opportunity_items'.
		 */
		protected $table = 'opportunities_items';

		/**
		 * Τα πεδία που επιτρέπεται να συμπληρωθούν μαζικά (Mass Assignment).
		 */
		protected $fillable = [
			'opportunity_id',
			'product_id',
			'quantity',
			'unit_price',
			'discount',
			'tax_rate',
			'total',
			'notes'
		];

		/**
		 * Casts για αυτόματη μετατροπή τύπων δεδομένων.
		 */
		protected $casts = [
			'unit_price' => 'decimal:2',
			'discount'   => 'decimal:2',
			'tax_rate'   => 'decimal:2',
			'total'      => 'decimal:2',
			'quantity'   => 'integer',
		];

		/**
		 * Η ευκαιρία στην οποία ανήκει αυτή η γραμμή (αντίστροφη σχέση).
		 */
		public function opportunity(): BelongsTo {
			return $this->belongsTo(Opportunity::class, 'opportunity_id', 'id');
		}

		/**
		 * Το προϊόν που αφορά η συγκεκριμένη γραμμή.
		 */
		public function product(): BelongsTo {
			return $this->belongsTo(Product::class, 'product_id', 'id');
		}
	}