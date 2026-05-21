<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Yesterday's Executive Report</title>
	<style>
        body {
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f7f6;
            padding: 40px 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #2c3e50;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .content {
            padding: 30px;
        }
        .stats-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .stat-card {
            padding: 20px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
        }
        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            background-color: #f8fafc;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #3498db;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 20px;
        }
	</style>
</head>
<body>
<div class="wrapper">
	<div class="container">
		<div class="header">
			<h1 style="margin:0; font-size: 20px;">Daily Executive Pulse</h1>
			<p style="margin:5px 0 0; opacity: 0.8;">{{ today()->subDay()->format('d/m/Y') }}</p>
		</div>

		<div class="content">
			<p>Καλημέρα,</p>
			<p>Ακολουθεί η σύνοψη των χθεσινών επιδόσεων του <strong>ClientHub</strong>:</p>

			<table class="stats-grid">
				<tr>
					<td width="50%" style="padding-right: 10px;">
						<div class="stat-card">
							<span class="stat-value">{{ $stats['new_leads'] }}</span>
							<span class="stat-label">Νέα Leads</span>
						</div>
					</td>
					<td width="50%" style="padding-left: 10px;">
						<div class="stat-card">
							<span class="stat-value">{{ number_format($stats['cash_in'], 2) }}€</span>
							<span class="stat-label">Εισπράξεις</span>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top: 20px;">
						<div class="stat-card" style="border-left: 4px solid #e74c3c;">
							<span class="stat-value" style="color: #e74c3c;">{{ $stats['pending_invoices'] }}</span>
							<span class="stat-label">Εκκρεμή Τιμολόγια</span>
						</div>
					</td>
				</tr>
			</table>

			<div style="text-align: center;">
				<a href="{{ config('app.url') }}" class="btn">Μετάβαση στο Dashboard</a>
			</div>
		</div>

		<div class="footer">
			Αυτόματη ενημέρωση από το ClientHub CRM &bull; {{ date('Y') }}
		</div>
	</div>
</div>
</body>
</html>