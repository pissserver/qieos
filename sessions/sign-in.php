<style>
    .login-title{
        font-size: 34px;
        font-weight: 700;
        letter-spacing: -0.8px;
        color:#1e293b;
        margin-bottom:8px;
    }

    .login-subtitle{
        font-size:15px;
        font-weight:400;
        color:#64748b;
        margin-bottom:0;
        letter-spacing:0.3px;
    }

    .login-title-wrap{
        position:relative;
        display:inline-block;
    }

    .login-title-wrap::after{
        content:'';
        width:60px;
        height:4px;
        border-radius:50px;
        background:linear-gradient(90deg,#60a5fa,#facc15);
        display:block;
        margin:14px auto 0;
    }
</style>

<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Login - Qieos</title>

        <?php include '../script/headscript.php'; ?>
    </head>

    <body>
        <!-- NOTICE: You can use the _analytics.html partial to include production code specific code & trackers -->

        <main>
            <!-- Section -->
            <section
                class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center"
            >
                <div class="container">
                    <div
                        class="row justify-content-center form-bg-image"
                        data-background-lg="../assets/img/illustrations/signin.svg"
                    >
                        <div
                            class="col-12 d-flex align-items-center justify-content-center"
                        >
                            <div
                                class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500"
                            >
                                    <div class="text-center">
                                        <img src="../assets/img/brand/qieos.png"
                                            alt="Qieos Logo"
                                            width="240"
                                            class="d-block mx-auto mb-4">

                                        <div class="login-title-wrap">
                                            <h1 class="login-title">Selamat Datang</h1>
                                            <p class="login-subtitle">Login akun Qieos anda</p>
                                        </div>
                                    </div>

                                <?php
                                    if(isset($_GET['success'])){
                                        $success = $_GET['success'];
                                        if($success == 'register'){
                                            echo '<div class="alert alert-success mt-3">Berhasil membuat akun, silakan login.</div>';
                                        } elseif($success == 'reset'){
                                            echo '<div class="alert alert-success mt-3">Berhasil mereset password, silakan login.</div>';
                                        } elseif($success == 'logout'){
                                            echo '<div class="alert alert-success mt-3">Berhasil logout, login kembali untuk masuk.</div>';
                                        }
                                    } elseif(isset($_GET['error'])){
                                        $error = $_GET['error'];
                                        if($error == 'empty'){
                                            echo '<div class="alert alert-danger mt-3">Semua field harus diisi.</div>';
                                        } elseif($error == 'username'){
                                            echo '<div class="alert alert-danger mt-3">Username tidak ditemukan.</div>';
                                        } elseif($error == 'password'){
                                            echo '<div class="alert alert-danger mt-3">Password salah.</div>';
                                        }
                                    }
                                ?>

                                <form action="sign-in-action.php" method="POST" class="mt-4">
                                    <!-- Form -->
                                    <div class="form-group mb-4">
                                        <label for="username">Username</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text"
                                                id="basic-addon1"
                                            >
                                                <svg
                                                    class="icon icon-xs text-gray-600"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                                                    ></path>
                                                    <path
                                                        d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                                                    ></path>
                                                </svg>
                                            </span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Username"
                                                id="username"
                                                name="username"
                                                value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : ''; ?>"
                                                autofocus
                                                required
                                            />
                                        </div>
                                    </div>
                                    <!-- End of Form -->
                                    <div class="form-group">
                                        <!-- Form -->
                                        <div class="form-group mb-4">
                                            <label for="password"
                                                >Password</label
                                            >
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text"
                                                    id="basic-addon2"
                                                >
                                                    <svg
                                                        class="icon icon-xs text-gray-600"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                            clip-rule="evenodd"
                                                        ></path>
                                                    </svg>
                                                </span>
                                                <input
                                                    type="password"
                                                    placeholder="Password"
                                                    class="form-control"
                                                    id="password"
                                                    name="password"
                                                    required
                                                />
                                                <span class="input-group-text">
                                                    <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End of Form -->
                                        <div
                                            class="d-flex justify-content-between align-items-top mb-4"
                                        >
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo (isset($_COOKIE['username'])) ? 'checked' : ''; ?> />
                                                <label class="form-check-label mb-0" for="remember">Ingat saya</label>
                                            </div>
                                            <div>
                                                <a
                                                    href="forgot-password.php"
                                                    class="small text-right"
                                                    >Lupa password?</a
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button
                                            type="submit"
                                            class="btn btn-gray-800"
                                        >
                                            Login
                                        </button>
                                    </div>
                                </form>
                                
                                <div
                                    class="d-flex justify-content-center align-items-center mt-4"
                                >
                                    <span class="fw-normal">
                                        Belum terdaftar?
                                        <a href="sign-up.php" class="fw-bold"
                                            >Buat akun</a
                                        >
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <?php include '../script/footscript.php'; ?>

        <script>
            const togglePassword = document.querySelector("#togglePassword");
            const password = document.querySelector("#password");

            togglePassword.addEventListener("click", function () {
                const type = password.getAttribute("type") === "password" ? "text" : "password";
                password.setAttribute("type", type);

                // Ganti ikon
                this.classList.toggle("fa-eye");
                this.classList.toggle("fa-eye-slash");
            });
        </script>
    </body>
</html>
