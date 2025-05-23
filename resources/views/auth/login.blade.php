<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Akademik Sekolah Negeri Polines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

<body>
    <div class="row login-container m-0">
        <!-- Order changes on mobile: login-form will be on top for small screens -->
        <div class="col-md-6 bg-primary-dark d-flex align-items-center justify-content-center order-md-1 order-2">
            <div class="login-form">
                <h4 class="text-center mb-4 fw-bold">SIGN IN</h4>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            id="inputEmailAddress" placeholder="Alamat Email" value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <span class="input-group-text password-toggle">
                            <i class="bi bi-eye"></i>
                        </span>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 row mt-4">
                        <div class="col-7">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label small" for="remember">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-5 text-end">
                            <a href="#" class="text-decoration-none small">Lupa Password?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">LOGIN</button>
                </form>
            </div>
        </div>

        <div class="col-md-6 p-0 position-relative building-image order-md-2 order-1"
            style="background-image: url('{{ asset('assets/images/Sma_N_16_Jakarta.jpg') }}');">
            <div class="overlay">
                <div class="logo-container mb-3">
                    <img src="{{ asset('assets/images/login-logo.png') }}" alt="Logo Polines" class="img-fluid">
                </div>
                <h3 class="text-white mb-2 school-title">Sistem Informasi Akademik</h3>
                <h3 class="text-white school-name">Sekolah Negeri Polines</h3>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.password-toggle').addEventListener('click',
                function() {
                    const passwordInput = document.querySelector('input[name="password"]');
                    const icon = this.querySelector('.bi');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    }
                });
        });
    </script>
</body>

</html>
