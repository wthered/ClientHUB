<!DOCTYPE html>
<html lang="el">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CRM Pro | Είσοδος</title>

	<link rel="stylesheet" href="{{ asset('css/style.css') }}">

	<style>
        /* Συμπληρωματικό CSS μόνο για τη σελίδα Login */
        .guest-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #141c24 100%);
        }
        .login-card {
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn-primary-block {
            width: 100%;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-primary-block:hover { background: var(--primary-dark); }
        .error-msg { color: var(--danger); font-size: 0.85rem; margin-top: 10px; }
	</style>
</head>
<body>
<div class="guest-wrapper">
	@yield('content')
</div>
</body>
</html>