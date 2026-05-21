<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Product extends Model {
		protected $fillable = [
			'name',
			'sku',
			'description',
			'price',
			'cost',
			'unit',
			'is_active',
		];
	}
