<?php

	namespace App\DTOs;

	use App\Models\Lead;
	use Illuminate\Support\Collection;

	readonly class LeadObject {
		public function __construct(public int $id,
			public string $first_name,
			public string $last_name,
			public string $account_name,
			public ?string $email,
			public ?string $phone,
			public ?string $job_title,
			public ?string $website,
			public ?string $source,
			public ?float $estimated_value,
			public ?int $owner_id,
			public ?int $converted_by,
			public bool $create_opportunity) {}

		public static function fromCollection(Collection $data, Lead $lead): self {
			return new self(id: $lead->id,
				first_name: $lead->first_name,
				last_name: $lead->last_name,
				account_name: $data->get('account_name') ?? $lead->company_name ?? ($lead->last_name . ' Household'),
				email: $lead->email,
				phone: $lead->phone,
				job_title: $lead->job_title,
				website: $lead->website,
				source: $lead->source,
				estimated_value: $lead->estimated_value ? (float) $lead->estimated_value : 0.0,
				owner_id: $lead->owner_id,
				converted_by: auth()->id(),
				create_opportunity: $data->get('create_opportunity', true));
		}
	}