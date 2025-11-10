<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir - Solver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { color-scheme: dark light; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at top, rgba(59,130,246,0.12), transparent 45%),
                        radial-gradient(circle at bottom, rgba(139,92,246,0.18), transparent 55%),
                        linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #111827 100%);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .expired-wrapper {
            width: min(560px, 100%);
            background: rgba(15, 23, 42, 0.85);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow:
                0 28px 65px rgba(15, 23, 42, 0.55),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            padding: clamp(2rem, 4vw, 2.75rem);
            backdrop-filter: blur(18px);
        }

        .expired-icon {
            width: 92px;
            height: 92px;
            border-radius: 24px;
            margin: 0 auto 1.75rem;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(251,191,36,0.22), rgba(245,158,11,0.26));
            border: 1px solid rgba(251, 191, 36, 0.35);
            box-shadow: 0 18px 38px rgba(245, 158, 11, 0.35);
        }

        .expired-icon i {
            font-size: 2.8rem;
            color: #fde68a;
        }

        .expired-title {
            font-size: clamp(2.1rem, 5vw, 2.6rem);
            font-weight: 700;
            margin-bottom: 0.85rem;
            letter-spacing: -0.015em;
            background: linear-gradient(135deg, #fbbf24, #fb923c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .expired-description {
            font-size: 1rem;
            line-height: 1.7;
            color: rgba(226, 232, 240, 0.82);
            margin-bottom: 2.25rem;
        }

        .expired-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            justify-content: center;
        }

        .btn-primary-gradient,
        .btn-outline-neutral {
            border-radius: 14px;
            font-weight: 600;
            padding: 0.7rem 1.6rem;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            text-decoration: none;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.92), rgba(59, 130, 246, 0.92));
            color: #ffffff;
            border: none;
            box-shadow: 0 20px 42px rgba(59, 130, 246, 0.35);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 26px 54px rgba(59, 130, 246, 0.42);
            color: #ffffff;
        }

        .btn-outline-neutral {
            border: 1px solid rgba(148, 163, 184, 0.4);
            background: rgba(15, 23, 42, 0.55);
            color: rgba(226, 232, 240, 0.9);
        }

        .btn-outline-neutral:hover {
            border-color: rgba(129, 140, 248, 0.55);
            background: rgba(79, 70, 229, 0.22);
            color: #ede9fe;
        }

        .countdown {
            display: block;
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: rgba(226, 232, 240, 0.65);
        }

        @media (max-width: 576px) {
            body { padding: 1.5rem; }
            .expired-wrapper { padding: 2rem 1.6rem; }
            .expired-actions { flex-direction: column; }
            .btn-primary-gradient,
            .btn-outline-neutral {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="expired-wrapper">
        <div class="expired-icon">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <h1 class="expired-title">Sesi Berakhir</h1>
        <p class="expired-description">
            Sesi Anda telah kedaluwarsa atau permintaan terbaru tidak valid.
            Kami akan mengarahkan Anda kembali ke dashboard untuk melanjutkan pekerjaan.
        </p>
        <div class="expired-actions">
            <a href="{{ route('dashboard') }}" class="btn-primary-gradient">
                <i class="bi bi-speedometer2"></i>
                Kembali ke Dashboard
            </a>
            <a href="{{ route('login') }}" class="btn-outline-neutral">
                <i class="bi bi-box-arrow-in-right"></i>
                Login Ulang
            </a>
        </div>
        <small class="countdown">Mengalihkan otomatis dalam <span id="countdown">3</span> detik...</small>
    </div>

    <script>
        const targetUrl = "{{ route('dashboard') }}";
        let remaining = 3;
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(() => {
            remaining -= 1;
            countdownEl.textContent = remaining;
            if (remaining <= 0) {
                clearInterval(timer);
                window.location.replace(targetUrl);
            }
        }, 1000);
    </script>
</body>
</html>
