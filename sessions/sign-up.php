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

        <title>Buat Akun - Cartify</title>
        
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
                                        <p class="login-subtitle">Daftar akun Qieos anda</p>
                                    </div>
                                </div>

                                <?php
                                    if(isset($_GET['error'])){
                                        if($_GET['error']=="empty"){
                                            echo '<div class="alert alert-danger mt-3">Semua field harus diisi.</div>';
                                        }

                                        if($_GET['error']=="password"){
                                            echo '<div class="alert alert-danger mt-3">Password dan konfirmasi tidak sama.</div>';
                                        }

                                        if($_GET['error']=="username"){
                                            echo '<div class="alert alert-danger mt-3">Username sudah terdaftar.</div>';
                                        }
                                    }
                                ?>

                                <form action="sign-up-action.php" class="mt-4" method="POST">
                                    <!-- Form -->
                                    <div class="form-group mb-4">
                                        <label for="name">Nama Lengkap</label>
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
                                                        fill-rule="evenodd"
                                                        d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 15a6 6 0 1112 0v1H4v-1z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                            </span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Nama Lengkap"
                                                id="name"
                                                name="name"
                                                autofocus
                                                required
                                            />
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="username">Username Anda</label>
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
                                                        fill-rule="evenodd"
                                                        d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 15a6 6 0 1112 0v1H4v-1z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                            </span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Username"
                                                id="username"
                                                name="username"
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
                                            </div>
                                        </div>
                                        <!-- End of Form -->
                                        <!-- Form -->
                                        <div class="form-group mb-4">
                                            <label for="confirm_password"
                                                >Konfirmasi Password</label
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
                                                    placeholder="Konfirmasi Password"
                                                    class="form-control"
                                                    id="confirm_password"
                                                    name="confirm_password"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button
                                            type="submit"
                                            class="btn btn-gray-800"
                                        >
                                            Buat Akun
                                        </button>
                                    </div>
                                </form>
                                
                                <div
                                    class="d-flex justify-content-center align-items-center mt-4"
                                >
                                    <span class="fw-normal">
                                        Sudah punya akun?
                                        <a href="sign-in.php" class="fw-bold"
                                            >Login di sini</a
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
    </body>
</html>
