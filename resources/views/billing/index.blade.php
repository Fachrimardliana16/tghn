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

		/* Background Animation */
		body::before {
			content: '';
			position: fixed;
			top: -50%; left: -50%;
			width: 200%; height: 200%;
			background: radial-gradient(circle, rgba(56,189,248,0.1) 0%, rgba(255,255,255,0) 50%);
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
		.text-danger { color: var(--danger); }
		.text-warning { color: var(--warning); }
		.text-primary { color: var(--primary); }
	</style>
</head>
<body>

	<div class="loader-overlay" id="ajax_loader">
		<div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
			<div class="spinner"></div>
			<div style="font-weight: 600; color: var(--primary); font-size: 1.1rem;">Memproses Data...</div>
		</div>
	</div>

	<div class="container">
		<div style="text-align: center; margin-bottom: 40px;">
			<h1>PERUMDAM Tirta Perwira</h1>
			<p style="font-size: 1.1rem; color: var(--text-main);">Layanan Pengecekan Tagihan Rekening Pelanggan</p>
		</div>

		<div class="glass-card">
			<div class="main-panel">
				<h2>Cek Tagihan Anda</h2>
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

			$('#billingForm').on('submit', function(e) {
				e.preventDefault();
				const customerNumber = inputEl.val();
				const token = $('input[name="_token"]').val();

				if (customerNumber.length !== 8) {
					inputGroup.addClass('input-error');
					return;
				}

				$('#ajax_loader').addClass('active');
				$('#result-container').html('');

				$.ajax({
					type: 'POST',
					url: '{{ url("/check-billing") }}',
					data: { _token: token, nolangg: customerNumber },
					dataType: 'json',
					success: function(response) {
						setTimeout(() => { // slight delay for smooth animation
							$('#ajax_loader').removeClass('active');
							if (response.status === 'error') {
								$('#result-container').html(response.message || `<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Terjadi kesalahan.</div>`);
							} else {
								// Success message structure is already formatted in the service
								$('#result-container').html(response.message);
							}
						}, 300);
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$('#ajax_loader').removeClass('active');
						const errorMsg = jqXHR.responseJSON?.message ||
										 textStatus ||
										 'Terjadi kesalahan koneksi ke server';
						$('#result-container').html(
							`<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${errorMsg}</div>`
						);
					}
				});
			});
		});
	</script>
</body>
</html>
