<?php
    session_start();
    include '../script/connection.php';
?>

<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Primary Meta Tags -->
        <title>Reset Password - Qieos</title>
        
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
                    <div class="row justify-content-center form-bg-image">
                        <p class="text-center">
                            <a
                                href="sign-in.php"
                                class="d-flex align-items-center justify-content-center"
                            >
                                <svg
                                    class="icon icon-xs me-2"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                Kembali ke halaman login
                            </a>
                        </p>
                        <div
                            class="col-12 d-flex align-items-center justify-content-center"
                        >
                            <div
                                class="bg-white shadow border-0 rounded p-4 p-lg-5 w-100 fmxw-500"
                            >
                                <h1 class="h3 mb-4">Reset password</h1>

                                <?php
                                    if(isset($_GET['error'])){
                                        if($_GET['error']=="empty"){
                                            echo '<div class="alert alert-danger mt-3">Semua field harus diisi.</div>';
                                        }

                                        if($_GET['error']=="password"){
                                            echo '<div class="alert alert-danger mt-3">Password dan konfirmasi tidak sama.</div>';
                                        }
                                    }
                                ?>

                                <form action="reset-password-action.php" method="POST">
                                    <!-- Form -->
                                    <div class="mb-4">
                                        <label for="username">Username Anda</label>
                                        <div class="input-group">
                                            <input
                                                type="username"
                                                class="form-control"
                                                id="username"
                                                value="<?php echo isset($_SESSION['reset_username']) ? $_SESSION['reset_username'] : ''; ?>"
                                                required
                                                disabled
                                            />
                                        </div>
                                    </div>
                                    <!-- End of Form -->
                                    <!-- Form -->
                                    <div class="form-group mb-4">
                                        <label for="password"
                                            >Password Baru</label
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
                                                placeholder="Password Baru"
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
                                            >Konfirmasi Password Baru</label
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
                                                placeholder="Konfirmasi Password Baru"
                                                class="form-control"
                                                id="confirm_password"
                                                name="confirm_password"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <!-- End of Form -->
                                    <div class="d-grid">
                                        <button
                                            type="submit"
                                            class="btn btn-gray-800"
                                        >
                                            Reset password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <?php include '../script/footscript.php'; ?>
    </body>
</html>
