<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Tirta Perwira | Cek Tagihan</title>

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<!-- Icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<style>
		/* Variables */
		:root {
			--primary: #2563eb;
			--primary-hover: #1d4ed8;
			--secondary: #0f172a;
			--accent: #38bdf8;
			--background: #f8fafc;
			--surface: rgba(255, 255, 255, 0.85);
			--text-main: #1e293b;
			--text-muted: #64748b;
			--border: rgba(226, 232, 240, 0.8);
			--success: #10b981;
			--danger: #ef4444;
			--warning: #f59e0b;
			--radius-lg: 24px;
			--radius-md: 16px;
			--radius-sm: 8px;
		}

		/* Reset & Base */
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body {
			font-family: 'Inter', sans-serif;
			background: linear-gradient(135deg, #e0e7ff 0%, #bae6fd 100%);
			color: var(--text-main);
			min-height: 100vh;
			display: flex;
			flex-direction: column;
			position: relative;
			overflow-x: hidden;
		}

		/* Water Overlay Background (Subtle Water Texture) */
		.water-bg-overlay {
			position: fixed;
			top: 0; left: 0; width: 100%; height: 100%;
			pointer-events: none;
			z-index: -1;
			opacity: 0.18;
			background-image: 
				radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.3) 0%, transparent 40%),
				radial-gradient(circle at 80% 70%, rgba(37, 99, 235, 0.2) 0%, transparent 45%),
				url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%230284c7' fill-opacity='0.6' d='M0,192L48,197.3C96,203,192,213,288,197.3C384,181,480,139,576,138.7C672,139,768,181,864,197.3C960,213,1056,203,1152,186.7C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3Cpath fill='%230369a1' fill-opacity='0.4' d='M0,96L48,122.7C96,149,192,203,288,208C384,213,480,171,576,144C672,117,768,107,864,128C960,149,1056,203,1152,213.3C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
			background-repeat: repeat-x;
			background-position: bottom center;
			background-size: 1440px auto;
		}

		/* Header Section Redesign */
		.header-container {
			text-align: center;
			margin-bottom: 36px;
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 12px;
		}
		.header-badge {
			display: inline-flex;
			align-items: center;
			justify-content: center;
		}
		.water-drop-icon {
			width: 56px;
			height: 56px;
			background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
			color: #ffffff;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.6rem;
			box-shadow: 0 10px 20px rgba(2, 132, 199, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.4);
			animation: floatIcon 4s ease-in-out infinite;
		}
		@keyframes floatIcon {
			0%, 100% { transform: translateY(0px) scale(1); }
			50% { transform: translateY(-6px) scale(1.05); }
		}
		.header-title {
			font-size: 2.3rem;
			font-weight: 800;
			letter-spacing: -0.5px;
			background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			margin: 0;
			line-height: 1.2;
		}
		.header-subtitle {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			padding: 8px 20px;
			background: rgba(255, 255, 255, 0.85);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border: 1px solid rgba(255, 255, 255, 0.9);
			border-radius: 30px;
			font-size: 0.95rem;
			font-weight: 600;
			color: #0369a1;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
		}
		.header-subtitle i {
			color: #0284c7;
		}

		/* Background Animation */
		body::before {
			content: '';
			position: fixed;
			top: -50%; left: -50%;
			width: 200%; height: 200%;
			background: radial-gradient(circle, rgba(56,189,248,0.12) 0%, rgba(255,255,255,0) 50%);
			animation: rotateBackground 30s linear infinite;
			z-index: -1;
		}
		@keyframes rotateBackground {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}

		/* Layout */
		.container {
			max-width: 1000px;
			margin: 40px auto;
			padding: 0 20px;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		/* Glassmorphism Card */
		.glass-card {
			background: var(--surface);
			backdrop-filter: blur(16px);
			-webkit-backdrop-filter: blur(16px);
			border: 1px solid rgba(255, 255, 255, 0.5);
			border-radius: var(--radius-lg);
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0,0,0,0.05);
			overflow: hidden;
			display: flex;
			flex-direction: column;
			transition: transform 0.3s ease, box-shadow 0.3s ease;
		}
		@media (min-width: 768px) {
			.glass-card { flex-direction: row; }
		}
		.glass-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
		}

		/* Panels */
		.main-panel {
			flex: 3;
			padding: 40px;
		}
		.info-panel {
			flex: 2;
			background: linear-gradient(145deg, var(--primary), #1e3a8a);
			color: white;
			padding: 40px;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			position: relative;
			overflow: hidden;
		}
		.info-panel::after {
			content: '';
			position: absolute;
			bottom: -30%; right: -20%;
			width: 250px; height: 250px;
			background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
			border-radius: 50%;
		}

		/* Typography */
		h1 {
			font-size: 2.5rem;
			font-weight: 700;
			margin-bottom: 8px;
			background: linear-gradient(90deg, var(--primary), var(--accent));
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			letter-spacing: -1px;
		}
		h2 {
			font-size: 1.5rem;
			font-weight: 600;
			margin-bottom: 24px;
			color: var(--secondary);
		}
		h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 16px; }
		p { line-height: 1.6; margin-bottom: 16px; color: var(--text-muted); }
		.info-panel p { color: rgba(255,255,255,0.9); font-size: 0.95rem; }
		.info-panel b { color: #fff; font-weight: 600; }
		.info-panel i { color: rgba(255,255,255,0.8); }

		/* Form Elements */
		.input-group {
			margin-bottom: 24px;
			position: relative;
		}
		label {
			display: block;
			font-size: 0.875rem;
			font-weight: 500;
			margin-bottom: 8px;
			color: var(--text-main);
		}
		input[type="text"], input[type="number"] {
			width: 100%;
			padding: 16px 20px;
			font-size: 1.1rem;
			font-family: 'Inter', sans-serif;
			background: rgba(255, 255, 255, 0.9);
			border: 2px solid var(--border);
			border-radius: var(--radius-md);
			color: var(--text-main);
			transition: all 0.3s ease;
			outline: none;
			box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
			/* Remove spinner */
			-moz-appearance: textfield;
		}
		input[type="text"]::-webkit-outer-spin-button,
		input[type="text"]::-webkit-inner-spin-button,
		input[type="number"]::-webkit-outer-spin-button,
		input[type="number"]::-webkit-inner-spin-button {
			-webkit-appearance: none; margin: 0;
		}
		input[type="text"]:focus, input[type="number"]:focus {
			border-color: var(--primary);
			box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
			background: #fff;
		}

		/* Buttons */
		.btn {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			padding: 16px 32px;
			font-size: 1rem;
			font-weight: 600;
			border: none;
			border-radius: var(--radius-md);
			cursor: pointer;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			text-decoration: none;
			gap: 8px;
			width: 100%;
		}
		.btn-primary {
			background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);
			color: white;
			box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
		}
		.btn-primary:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 16px rgba(37, 99, 235, 0.4);
		}
		.btn-primary:active { transform: translateY(0); }

		.btn-outline {
			background: rgba(255,255,255,0.1);
			color: white;
			border: 1px solid rgba(255,255,255,0.3);
			backdrop-filter: blur(4px);
		}
		.btn-outline:hover {
			background: rgba(255,255,255,0.2);
			border-color: white;
		}

		/* Loader */
		.loader-overlay {
			position: fixed; top: 0; left: 0; width: 100%; height: 100%;
			background: rgba(255, 255, 255, 0.8);
			backdrop-filter: blur(8px);
			display: none; justify-content: center; align-items: center;
			z-index: 1000;
			opacity: 0; transition: opacity 0.3s ease;
		}
		.loader-overlay.active { display: flex; opacity: 1; }
		.spinner {
			width: 50px; height: 50px;
			border: 4px solid rgba(37, 99, 235, 0.2);
			border-top-color: var(--primary);
			border-radius: 50%;
			animation: spin 1s linear infinite;
		}
		@keyframes spin { 100% { transform: rotate(360deg); } }

		/* Results Table */
		#result-container { margin-top: 32px; animation: slideUp 0.4s ease forwards; }
		@keyframes slideUp {
			from { opacity: 0; transform: translateY(20px); }
			to { opacity: 1; transform: translateY(0); }
		}
		.table-responsive { overflow-x: auto; border-radius: var(--radius-sm); border: 1px solid var(--border); background: #fff; }
		.table { width: 100%; border-collapse: collapse; text-align: left; }
		.table th, .table td { padding: 16px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
		.table tr:last-child td { border-bottom: none; }
		.table tr.bg-light td { background: #f1f5f9; color: var(--secondary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
		.table tr.table-warning td { background: #fef3c7; color: #92400e; font-size: 1.1rem; }
		.table td:first-child { color: var(--text-muted); width: 40%; }
		.table td:last-child { font-weight: 500; color: var(--text-main); }

		/* Alerts */
		.alert { padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 500; }
		.alert i { font-size: 1.2rem; margin-right: 8px; vertical-align: middle; }
		.alert-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
		.alert-success { background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; }
		.alert-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

		/* Error Message for Input */
		.error-text { color: var(--danger); font-size: 0.85rem; margin-top: 8px; display: none; font-weight: 500; }
		.input-error input { border-color: var(--danger); background: #fef2f2; }
		.input-error .error-text { display: block; animation: shake 0.4s ease; }
		@keyframes shake {
			0%, 100% { transform: translateX(0); }
			25% { transform: translateX(-5px); }
			75% { transform: translateX(5px); }
		}

		/* Footer */
		footer { text-align: center; padding: 24px; color: var(--text-muted); font-size: 0.875rem; margin-top: auto; }

		/* Status Colors */
		.text-success { color: var(--success); }
		.text-danger { color: var(--danger); font-weight: 700; }
		.text-warning { color: var(--warning); }
		.text-primary { color: var(--primary); }

		/* Billing Result Card Formatting */
		.billing-result-card {
			background: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: var(--radius-md);
			padding: 24px;
			box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
		}
		.billing-title {
			font-size: 1.15rem;
			font-weight: 700;
			color: var(--secondary);
			display: flex;
			align-items: center;
			gap: 10px;
			margin-bottom: 20px;
			padding-bottom: 12px;
			border-bottom: 2px solid #f1f5f9;
		}
		.billing-title i { color: var(--primary); }

		.customer-info-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 14px;
			background: #f8fafc;
			padding: 16px;
			border-radius: var(--radius-sm);
			border: 1px solid #e2e8f0;
			margin-bottom: 20px;
		}
		.info-item {
			display: flex;
			flex-direction: column;
			gap: 3px;
		}
		.info-label {
			font-size: 0.75rem;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			color: var(--text-muted);
		}
		.info-value {
			font-size: 0.95rem;
			font-weight: 600;
			color: var(--text-main);
		}

		.billing-table-wrapper {
			overflow-x: auto;
			margin-bottom: 20px;
			border-radius: var(--radius-sm);
			border: 1px solid #e2e8f0;
		}
		.billing-table {
			width: 100%;
			border-collapse: collapse;
		}
		.billing-table th {
			background: #f1f5f9;
			color: var(--text-muted);
			font-size: 0.8rem;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			padding: 12px 16px;
			border-bottom: 1px solid #e2e8f0;
		}
		.billing-table td {
			padding: 14px 16px;
			border-bottom: 1px solid #f1f5f9;
			font-size: 0.95rem;
		}
		.tagihan-amount {
			color: var(--danger);
			font-weight: 700;
		}
		.billing-table tr.total-row {
			background: #fff1f2;
		}
		.billing-table tr.total-row td {
			border-top: 2px solid #fecdd3;
			border-bottom: none;
			padding: 16px;
		}
		.total-label {
			font-weight: 700;
			color: #9f1239;
			font-size: 1rem;
		}
		.total-amount {
			color: #e11d48;
			font-weight: 800;
			font-size: 1.2rem;
		}

		.payment-info-box {
			background: #f8fafc;
			border: 1px solid #e2e8f0;
			border-radius: var(--radius-sm);
			padding: 16px;
			margin-top: 20px;
		}
		.payment-info-header {
			font-weight: 700;
			font-size: 0.875rem;
			color: var(--secondary);
			margin-bottom: 10px;
			display: flex;
			align-items: center;
			gap: 8px;
		}
		.payment-info-header i { color: var(--primary); }
		.payment-group {
			font-size: 0.85rem;
			margin-bottom: 6px;
			line-height: 1.4;
			display: flex;
			flex-wrap: wrap;
			gap: 4px;
		}
		.payment-group:last-child { margin-bottom: 0; }
		.pay-cat {
			font-weight: 700;
			color: #334155;
			min-width: 100px;
		}
		.pay-list {
			color: #64748b;
			flex: 1;
		}

		.timestamp-footer {
			margin-top: 16px;
			padding-top: 12px;
			border-top: 1px solid #f1f5f9;
			font-size: 0.8rem;
			color: var(--text-muted);
			display: flex;
			align-items: center;
			gap: 6px;
		}
	</style>
</head>
<body>
	<div class="water-bg-overlay"></div>

	<div class="loader-overlay" id="ajax_loader">
		<div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
			<div class="spinner"></div>
			<div style="font-weight: 600; color: var(--primary); font-size: 1.1rem;">Memproses Data...</div>
		</div>
	</div>

	<div class="container">
		<div class="header-container">
			<div class="header-badge">
				<div class="water-drop-icon">
					<i class="fas fa-droplet"></i>
				</div>
			</div>
			<h1 class="header-title">PERUMDAM Tirta Perwira</h1>
			<div class="header-subtitle">
				Layanan Pengecekan Tagihan Rekening Pelanggan
			</div>
		</div>

		<div class="glass-card">
			<div class="main-panel">
				<h2>Cek Tagihan Rekening Air Anda</h2>
				<form id="billingForm" method="POST">
					@csrf
					<div class="input-group" id="inputGroup">
						<label for="nolangg">Nomor Pelanggan (8 Digit Angka)</label>
						<input type="text" inputmode="numeric" pattern="\d{8}" maxlength="8" name="nolangg" id="nolangg" placeholder="Contoh: 12345678" required>
						<div class="error-text"><i class="fas fa-exclamation-circle"></i> Nomor Pelanggan harus persis 8 digit angka.</div>
					</div>
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-search"></i> Cek Sekarang
					</button>
				</form>
				<div id="result-container"></div>
				<button type="button" class="btn btn-primary" id="btnReset" style="display: none; margin-top: 20px;">
					<i class="fas fa-redo"></i> Cek Tagihan Rekening Lainnya
				</button>
			</div>

			<div class="info-panel">
				<div>
					<h3 style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-info-circle"></i> Informasi Penting</h3>
					<p>Jika terjadi selisih, harap melakukan konfirmasi melalui layanan pengaduan kami.</p>
					<div style="background: rgba(0,0,0,0.15); padding: 16px; border-radius: var(--radius-sm); margin-top: 20px;">
						<p style="margin-bottom: 8px;">Jumlah tagihan yang tertera belum termasuk <b>Biaya Penanganan Piutang Pelanggan</b> dan <b>Biaya Penyambungan Kembali</b>.</p>
						<i style="font-size: 0.85rem; opacity: 0.8;">(Berlaku jika mengalami penunggakan lebih dari 3 bulan.)</i>
					</div>
				</div>
				<a href="https://pengaduan.pdampurbalingga.co.id" class="btn btn-outline" style="margin-top: 32px;">
					<i class="fas fa-headset"></i> Pengaduan Online
				</a>
			</div>
		</div>

	</div>

	<footer>
		&copy; 2026 Subbagian Teknologi Informasi Perumdam Tirta Perwira Kabupaten Purbalingga
	</footer>

	<script>
		$(document).ready(function() {
			const inputEl = $('#nolangg');
			const inputGroup = $('#inputGroup');
			const billingForm = $('#billingForm');
			const resultContainer = $('#result-container');
			const btnReset = $('#btnReset');

			// Restrict input to 8 digits and numbers only
			inputEl.on('input', function() {
				let val = $(this).val().replace(/\D/g, '');
				if (val.length > 8) {
					val = val.slice(0, 8);
				}
				$(this).val(val);

				if (val.length > 0 && val.length !== 8) {
					inputGroup.addClass('input-error');
				} else {
					inputGroup.removeClass('input-error');
				}
			});

			billingForm.on('submit', function(e) {
				e.preventDefault();
				const customerNumber = inputEl.val();
				const token = $('input[name="_token"]').val();

				if (customerNumber.length !== 8) {
					inputGroup.addClass('input-error');
					return;
				}

				$('#ajax_loader').addClass('active');
				resultContainer.html('');

				$.ajax({
					type: 'POST',
					url: '{{ url("/check-billing") }}',
					data: { _token: token, nolangg: customerNumber },
					dataType: 'json',
					success: function(response) {
						setTimeout(() => { // slight delay for smooth animation
							$('#ajax_loader').removeClass('active');
							if (response.status === 'error') {
								resultContainer.html(response.message || `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Terjadi kesalahan.</div>`);
							} else {
								// Success message structure is already formatted in the service
								resultContainer.html(response.message);
							}
							billingForm.hide();
							btnReset.show();
						}, 300);
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$('#ajax_loader').removeClass('active');
						const errorMsg = jqXHR.responseJSON?.message ||
										 textStatus ||
										 'Terjadi kesalahan koneksi ke server';
						resultContainer.html(
							`<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${errorMsg}</div>`
						);
						billingForm.hide();
						btnReset.show();
					}
				});
			});

			btnReset.on('click', function() {
				resultContainer.html('');
				btnReset.hide();
				billingForm.show();
				inputEl.val('').focus();
				inputGroup.removeClass('input-error');
			});
		});
	</script>
</body>
</html>
