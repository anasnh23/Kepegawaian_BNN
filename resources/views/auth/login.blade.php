<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#0d47a1">
    <title>Login - SIAP BNN</title>

    <!-- Assets -->
    <link rel="icon" type="image/png" href="{{ asset('adminlte/dist/img/bnn.jpg') }}" sizes="32x32">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom Style -->
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-box {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
        }

        .login-box img {
            width: 90px;
            display: block;
            margin: 0 auto 1rem;
        }

        .login-box h1 {
            text-align: center;
            font-size: 1.9rem;
            color: #0d47a1;
            font-weight: bold;
        }

        .login-box h5 {
            text-align: center;
            color: #555;
            margin-bottom: 1.8rem;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 1.2rem;
            background: #fff;
        }

        .input-wrapper i {
            margin-right: 10px;
            color: #999;
            font-size: 16px;
        }

        .input-wrapper input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 15px;
            background: transparent;
        }

        .form-links {
            text-align: right;
            margin-bottom: 1.2rem;
        }

        .form-links a {
            font-size: 14px;
            color: #0d47a1;
            text-decoration: none;
        }

        .btn-login {
            background-color: #0d47a1;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s ease;
        }

        .btn-login:hover {
            background-color: #1565c0;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <img src="{{ asset('adminlte/dist/img/bnn.jpg') }}" alt="BNN Logo">
        <h1>SIAP-BNN</h1>
        <h5>Silakan login menggunakan akun Anda</h5>

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

    <!-- Scripts -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#formLogin').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ url('/login') }}",
                    type: "POST",
                    data: formData,
                    success: function (res) {
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
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat login.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
