{{-- resources/views/partials/pagination.blade.php --}}
@if ($paginator->hasPages())
	<nav class="custom-pagination">
		{{-- Previous Page Link --}}
		@if ($paginator->onFirstPage())
			<span class="disabled">« Προηγούμενο</span>
		@else
			<a href="{{ $paginator->previousPageUrl() }}" rel="prev">« Προηγούμενο</a>
		@endif

		{{-- Page Numbers --}}
		<div class="page-numbers">
			@foreach ($elements as $element)
				{{-- "Three Dots" Separator --}}
				@if (is_string($element))
					<span class="dots">{{ $element }}</span>
				@endif

				{{-- Array Of Links --}}
				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
							<span class="current">{{ $page }}</span>
						@else
							<a href="{{ $url }}">{{ $page }}</a>
						@endif
					@endforeach
				@endif
			@endforeach
		</div>

		{{-- Next Page Link --}}
		@if ($paginator->hasMorePages())
			<a href="{{ $paginator->nextPageUrl() }}" rel="next">Επόμενο »</a>
		@else
			<span class="disabled">Επόμενο »</span>
		@endif
	</nav>
@endif