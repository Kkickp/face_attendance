<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Sistem Presensi Wajah</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#0a0f1e;color:#f0f4ff;min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;}
        .bg-stars{position:fixed;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(99,102,241,0.12) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(139,92,246,0.1) 0%,transparent 60%);pointer-events:none;}
        .particle{position:absolute;width:2px;height:2px;border-radius:50%;background:rgba(99,102,241,0.6);animation:float linear infinite;}
        @keyframes float{0%{transform:translateY(100vh) translateX(0);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translateY(-10vh) translateX(var(--dx));opacity:0}}
        .login-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:24px;padding:2.5rem;width:100%;max-width:420px;backdrop-filter:blur(20px);box-shadow:0 20px 60px rgba(0,0,0,0.5);position:relative;z-index:10;}
        .logo{text-align:center;margin-bottom:2rem;}
        .logo-icon{width:64px;height:64px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 1rem;box-shadow:0 8px 24px rgba(99,102,241,0.4);}
        .logo h1{font-size:1.3rem;font-weight:700;}
        .logo p{font-size:0.8rem;color:#8892a4;margin-top:4px;}
        .form-group{margin-bottom:1.1rem;}
        .form-label{display:block;font-size:0.8rem;font-weight:500;color:#8892a4;margin-bottom:0.4rem;}
        .form-control{width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:0.7rem 1rem;color:#f0f4ff;font-family:'Inter',sans-serif;font-size:0.9rem;transition:all 0.2s;outline:none;}
        .form-control:focus{border-color:#6366f1;background:rgba(99,102,241,0.08);box-shadow:0 0 0 3px rgba(99,102,241,0.2);}
        .form-control::placeholder{color:#4a5568;}
        .btn-login{width:100%;background:linear-gradient(135deg,#6366f1,#7c3aed);color:white;border:none;border-radius:10px;padding:0.8rem;font-size:0.9rem;font-weight:600;cursor:pointer;transition:all 0.2s;margin-top:0.5rem;font-family:'Inter',sans-serif;}
        .btn-login:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(99,102,241,0.4);}
        .alert-error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#f87171;border-radius:10px;padding:0.7rem 1rem;font-size:0.82rem;margin-bottom:1rem;}
        .divider{text-align:center;color:#4a5568;font-size:0.78rem;margin-top:1.5rem;}
        .divider a{color:#818cf8;text-decoration:none;}
        .divider a:hover{text-decoration:underline;}
    </style>
</head>
<body>
<div class="bg-stars"></div>
<div id="particles"></div>

<div class="login-card">
    <div class="logo">
        <div class="logo-icon">🎓</div>
        <h1>Sistem Presensi Wajah</h1>
        <p>Masuk ke panel administrasi</p>
    </div>

    @if(session('status'))
        <div class="alert-error" style="background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.25);color:#34d399;">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Admin</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@presensi.com" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-login">🔐 Masuk ke Dashboard</button>
    </form>

    <div class="divider" style="margin-top:1.25rem;">
        <a href="{{ route('presensi.index') }}">← Kembali ke Halaman Presensi</a>
    </div>
</div>

<script>
const container = document.getElementById('particles');
for (let i = 0; i < 30; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.cssText = `left:${Math.random()*100}%;animation-duration:${8+Math.random()*15}s;animation-delay:${Math.random()*10}s;--dx:${(Math.random()-0.5)*200}px;`;
    container.appendChild(p);
}
</script>
</body>
</html>
