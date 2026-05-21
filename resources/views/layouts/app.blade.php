<!DOCTYPE html>
<html lang="el">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>CRM Pro | @yield('page_title', 'Dashboard')</title>
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('css/partials/topbar.css') }}">
	<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
	@stack('styles')
	<link rel="stylesheet" href="{{ asset('css/partials/footer.css') }}">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="app-wrapper">

	@include('partials.sidebar')

	<div class="main-area">
		@include('partials.topbar')

		<section class="content-body">
			@yield('content')
		</section>

		@include('partials.footer')
	</div>

	<script src="{{ asset('js/app.js') }}"></script>
	@stack('scripts')
</div>

<div id="toast-container" class="toast-container"></div>

</body>
</html>