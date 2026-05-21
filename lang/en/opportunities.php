<?php

	return [
		// --- Titles & Statistics ---
		'title'              => 'Sales Opportunities',
		'subtitle'           => 'Manage and track your sales pipeline',
		'create_title'       => 'New Opportunity',
		'total_count'        => 'Total Opportunities',
		'no_records'         => 'No opportunities found.',
		'history'            => 'History',

		// --- Table Headers & Labels ---
		'deal_name'          => 'Deal Title',
		'account'            => 'Account',
		'contact'            => 'Contact',
		'amount'             => 'Amount',
		'currency'           => 'Currency',
		'probability'        => 'Probability',
		'closing_date'       => 'Closing Date',
		'actions'            => 'Actions',

		// --- Validation Mapping (Attributes for Request) ---
		'account_id'         => 'Account',
		'contact_id'         => 'Contact',
		'stage_id'           => 'Stage',
		'close_date'         => 'Closing Date',

		// --- Filters & Search ---
		'search_placeholder' => 'Search opportunities...',
		'all_stages'         => 'All Stages',
		'all_statuses'       => 'All Statuses',

		// --- Form Grouping ---
		'basic_info'         => 'Basic Information',
		'financials'         => 'Financials',
		'tags'               => 'Tags',

		// --- Stages (Matching your stages) ---
		'stage' => [
			'label'              => 'Stage',
			'all'                => 'All Stages',
			'lead'               => 'Lead',
			'discovery'          => 'Discovery',
			'proposal'           => 'Proposal',
			'negotiation'        => 'Negotiation',
			'awaiting_signature' => 'Awaiting Signature',
			'won'                => 'Won',
			'lost'               => 'Lost',
		],

		// --- Statuses ---
		'status' => [
			'label'              => 'Status',
			'all'                => 'All Statuses',
			'open'               => 'Open',
			'won'                => 'Won',
			'lost'               => 'Lost',
		],

		// --- Actions ---
		'edit'               => 'Edit',
		'delete'             => 'Delete',
		'mark_won'           => 'Mark as Won',
		'na'                 => 'N/A',

		// --- Success Messages ---
		'create_success'     => 'Opportunity created successfully!',
		'update_success'     => 'Opportunity updated successfully!',
		'delete_success'     => 'Opportunity deleted.',
		'marked_won_success' => 'Opportunity marked as Won! Congratulations! ✨',

		// --- Line Items (Products/Services) ---
		'items' => [
			'product'            => 'Product / Service',
			'quantity'           => 'Quantity',
			'unit_price'         => 'Unit Price',
			'discount'           => 'Discount',
			'tax'                => 'Tax/VAT',
			'total'              => 'Total',
		],

		// --- User Roles (For Enum Labels) ---
		'roles' => [
			'owner'              => 'Owner',
			'collaborator'       => 'Collaborator',
			'viewer'             => 'Viewer',
		],
	];