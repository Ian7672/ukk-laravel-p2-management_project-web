<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - Solver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark light;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #312e81 50%, #1e1b4b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #e2e8f0;
        }

        .error-wrapper {
            max-width: 560px;
            width: 100%;
            background: rgba(15, 23, 42, 0.82);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 2.5rem 2.75rem;
            box-shadow:
                0 24px 60px rgba(17, 24, 39, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            text-align: center;
        }

        .error-icon {
            width: 88px;
            height: 88px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.28), rgba(129, 140, 248, 0.32));
            border: 1px solid rgba(129, 140, 248, 0.35);
        }

        .error-icon i {
            font-size: 2.6rem;
            color: #bfdbfe;
        }

        .error-title {
            font-size: clamp(2.25rem, 5vw, 2.75rem);
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #c4b5fd, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .error-description {
            font-size: 1rem;
            line-height: 1.7;
            color: rgba(226, 232, 240, 0.82);
            margin-bottom: 2rem;
        }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.85rem;
        }

        .btn-gradient {
            padding: 0.7rem 1.65rem;
            font-weight: 600;
            border-radius: 14px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.92), rgba(59, 130, 246, 0.92));
            color: #ffffff;
            box-shadow: 0 18px 38px rgba(30, 64, 175, 0.4);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 48px rgba(30, 64, 175, 0.45);
            color: #ffffff;
        }

        .btn-outline {
            padding: 0.7rem 1.65rem;
            font-weight: 600;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            background: rgba(15, 23, 42, 0.55);
            color: rgba(226, 232, 240, 0.9);
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.25s ease;
        }

        .btn-outline:hover {
            border-color: rgba(129, 140, 248, 0.55);
            background: rgba(79, 70, 229, 0.25);
            color: #ede9fe;
        }

        @media (max-width: 576px) {
            body {
                padding: 1.25rem;
            }

            .error-wrapper {
                padding: 2rem 1.75rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .error-actions a,
            .error-actions button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="error-icon">
            <i class="bi bi-compass"></i>
        </div>
        <h1 class="error-title">404</h1>
        <p class="error-description">
            Maaf, halaman yang Anda cari tidak ditemukan atau mungkin sudah dipindahkan.
            Silakan periksa kembali alamat yang dimasukkan atau kembali ke halaman utama.
        </p>
        <div class="error-actions">
            <button type="button" class="btn-outline" onclick="window.history.back()">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </button>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-gradient">
                    <i class="bi bi-speedometer2"></i>
                    Ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-gradient">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Ke Halaman Login
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
