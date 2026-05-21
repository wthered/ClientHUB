<td class="col-product">
	<select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
		<option value="">-- Επιλέξτε --</option>
		@foreach($products as $product)
			<option value="{{ $product->id }}" data-price="{{ $product->price }}">
				{{ $product->name }} ({{ number_format($product->price, 2) }} €)
			</option>
		@endforeach
	</select>
</td>

<td class="col-description">
	<input type="text" name="items[{{ $index }}][description]"
	       class="form-control"
	       placeholder="Περιγραφή υπηρεσίας/προϊόντος..."
	       required>
</td>

<td class="col-qty">
	<input type="number" name="items[{{ $index }}][quantity]"
	       class="form-control qty-input text-center"
	       value="1"
	       min="1"
	       required>
</td>

<td class="col-unit-price">
	<input type="number" name="items[{{ $index }}][unit_price]"
	       class="form-control unit-price-input text-end"
	       value="0.00"
	       step="0.01"
	       required>
</td>

<td class="col-amount">
	<input type="number" name="items[{{ $index }}][amount]"
	       class="form-control amount-input text-end"
	       value="0.00"
	       step="0.01"
	       readonly>
</td>

<td class="col-actions">
	<button type="button" class="btn-remove-row remove-item" title="Διαγραφή">
		<i class="fas fa-times"></i>
	</button>
</td>