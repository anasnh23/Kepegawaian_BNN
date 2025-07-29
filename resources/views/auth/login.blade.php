<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#0d47a1">
    <title>Login - SIAP BNN</title>

    <link rel="icon" href="{{ asset('adminlte/dist/img/bnn.jpg') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            margin: 0;
            background: linear-gradient(to right, #102b4c, #0c1f35);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.06);
            padding: 2.5rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .login-box img {
            width: 80px;
            display: block;
            margin: 0 auto 1rem;
        }

        .login-box h1 {
            font-size: 24px;
            text-align: center;
            color: #ffffff;
            margin-bottom: 0.2rem;
            letter-spacing: 1px;
        }

        .login-box h5 {
            text-align: center;
            font-weight: 300;
            color: #dddddd;
            margin-bottom: 1.8rem;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 10px 15px;
            margin-bottom: 1.3rem;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .input-wrapper:focus-within {
            border-color: #ffffff;
            box-shadow: 0 0 5px #00c3ff;
        }

        .input-wrapper i {
            margin-right: 10px;
            color: #ffffff;
        }

        .input-wrapper input {
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: 15px;
            flex: 1;
        }

        .input-wrapper input::placeholder {
            color: #bbb;
        }

        .form-links {
            text-align: right;
            margin-bottom: 1.5rem;
        }

        .form-links a {
            font-size: 14px;
            color: #bbdefb;
            text-decoration: none;
        }

        .form-links a:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            background-color: #1565c0;
            color: white;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .btn-login:hover {
            background-color: #1e88e5;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <img src="{{ asset('adminlte/dist/img/bnn.jpg') }}" alt="BNN Logo">
        <h1>SIAP BNN</h1>
        <h5>Masuk dengan akun Anda</h5>

        <form id="formLogin" method="POST">
            @csrf

            <div class="input-wrapper">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-links">
                <a href="#">Lupa Password?</a>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>

    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('#formLogin').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.post("{{ url('/login') }}", formData)
                    .done(function (res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => window.location.href = res.redirect, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message
                            });
                        }
                    })
                    .fail(function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: xhr.responseJSON?.message || 'Server tidak merespon.'
                        });
                    });
            });
        });
    </script>
</body>

</html>
