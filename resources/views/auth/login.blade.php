<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SISWA-TER</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --biru-tua: #1a3a6b;
            --coklat-tua: #4a2519;
            --emas: #b8860b;
            --emas-muda: #fef3c7;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--biru-tua) 0%, #1e3a5f 40%, var(--coklat-tua) 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            position: relative; overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute; inset: 0;
            /* Lapisan hias ini elemen berposisi, sehingga tercetak di atas
               kotak login yang statis dan memblokir seluruh klik. Dua baris
               di bawah membuatnya tembus klik dan tetap di lapisan bawah. */
            pointer-events: none;
            z-index: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .login-box { position: relative; z-index: 1; }

        .login-box {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
        }
        .login-left {
            background: linear-gradient(180deg, var(--biru-tua) 0%, var(--coklat-tua) 100%);
            padding: 3rem 2.5rem;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center; flex: 1;
        }
        .login-logo {
            width: 90px; height: 90px;
            background: var(--emas);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: var(--biru-tua);
            margin-bottom: 1.5rem;
            box-shadow: 0 0 0 8px rgba(184,134,11,.2);
        }
        .login-left h2 { color: #fff; font-weight: 800; font-size: 1.8rem; margin: 0 0 .5rem; }
        .login-left p { color: rgba(255,255,255,.7); font-size: .85rem; }
        .login-left .divider {
            width: 50px; height: 3px; background: var(--emas);
            border-radius: 2px; margin: 1rem auto;
        }
        .info-badge {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            color: #fff; border-radius: 8px;
            padding: .5rem 1rem; font-size: .75rem; margin-top: .5rem;
        }
        .login-right {
            padding: 3rem 2.5rem;
            flex: 1;
            display: flex; flex-direction: column; justify-content: center;
        }
        .login-right h4 { color: var(--biru-tua); font-weight: 700; }
        .form-control {
            border-radius: 10px; padding: .7rem 1rem;
            border: 1.5px solid #e5e7eb;
            transition: border-color .2s;
        }
        .form-control:focus { border-color: var(--biru-tua); box-shadow: 0 0 0 3px rgba(26,58,107,.1); }
        .input-group-text { border-radius: 10px 0 0 10px; background: #f8fafc; border: 1.5px solid #e5e7eb; }
        .input-group .form-control { border-radius: 0 10px 10px 0; border-left: 0; }
        .btn-login {
            background: linear-gradient(135deg, var(--biru-tua), #2563eb);
            color: #fff; border: none; border-radius: 10px;
            padding: .75rem; font-weight: 600; font-size: 1rem;
            transition: all .2s;
        }
        .btn-login:hover { background: linear-gradient(135deg, #2563eb, var(--biru-tua)); transform: translateY(-1px); color: #fff; }
        .demo-accounts { background: var(--emas-muda); border-radius: 10px; padding: 1rem; margin-top: 1rem; }
        .demo-accounts h6 { color: var(--emas); font-size: .75rem; font-weight: 700; text-transform: uppercase; }
        .demo-item { font-size: .78rem; color: #374151; padding: .15rem 0; }
        .demo-item span { color: var(--biru-tua); font-weight: 600; }
        @media (max-width: 640px) {
            .login-left { display: none; }
            .login-right { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <!-- LEFT PANEL -->
        <div class="login-left">
            <div class="login-logo"><i class="bi bi-archive-fill"></i></div>
            <h2>SISWA-TER</h2>
            <div class="divider"></div>
            <p>Sistem Warkah Terintegrasi</p>
            <p style="font-size:.8rem; margin-top:.5rem">Kantor Pertanahan<br><strong style="color:var(--emas)">Kementerian ATR/BPN</strong></p>
            <div class="info-badge mt-3">
                <i class="bi bi-shield-lock me-1"></i>
                Akses Terproteksi — Hanya Pengguna Terdaftar
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap justify-content-center" style="font-size:.72rem; color:rgba(255,255,255,.5)">
                <span><i class="bi bi-circle-fill me-1" style="color:#10b981"></i>PPS</span>
                <span><i class="bi bi-circle-fill me-1" style="color:#3b82f6"></i>PHPT</span>
                <span><i class="bi bi-circle-fill me-1" style="color:#f59e0b"></i>TU</span>
                <span><i class="bi bi-circle-fill me-1" style="color:#8b5cf6"></i>SP</span>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="login-right">
            <h4 class="mb-1">Selamat Datang</h4>
            <p class="text-muted mb-4" style="font-size:.85rem">Masuk ke akun Anda untuk melanjutkan</p>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-600" style="font-weight:600; font-size:.85rem; color:#374151">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill text-muted"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="nama@pertanahan.go.id"
                               value="{{ old('email') }}" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600; font-size:.85rem; color:#374151">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
                        <input type="password" name="password" id="pwd" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePwd()" style="border-radius:0 10px 10px 0">
                            <i class="bi bi-eye" id="pwd-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label text-muted" for="remember" style="font-size:.83rem">Ingat saya</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <!-- DEMO ACCOUNTS -->
            <div class="demo-accounts">
                <h6><i class="bi bi-info-circle me-1"></i>Akun Demo</h6>
                <div class="demo-item"><span>admin@siswater.id</span> — Administrator</div>
                <div class="demo-item"><span>pps@siswater.id</span> — Pengendalian & Sengketa</div>
                <div class="demo-item"><span>phpt@siswater.id</span> — Penetapan Hak & Pendaftaran</div>
                <div class="demo-item"><span>tu@siswater.id</span> — Tata Usaha</div>
                <div class="demo-item"><span>sp@siswater.id</span> — Survei dan Pengukuran</div>
                <div class="demo-item mt-1" style="color:#6b7280">Password semua: <span>password123</span></div>
            </div>

            <p class="text-center text-muted mt-3" style="font-size:.72rem">
                &copy; {{ date('Y') }} Kementerian ATR/BPN — SISWA-TER v1.0
            </p>
        </div>
    </div>

    <script>
        function togglePwd() {
            const f = document.getElementById('pwd');
            const i = document.getElementById('pwd-icon');
            f.type = f.type === 'password' ? 'text' : 'password';
            i.className = f.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
    </script>
</body>
</html>
