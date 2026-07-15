<?php 
    include '../script/connection.php';
?>

<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Primary Meta Tags -->
        <title>Lupa Password - Qieos</title>
        
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
                                class="signin-inner my-3 my-lg-0 bg-white shadow border-0 rounded p-4 p-lg-5 w-100 fmxw-500"
                            >
                                <h1 class="h3">Lupa Password?</h1>
                                <p class="mb-4">
                                    Jangan khawatir! Cukup ketik username Anda dan kami
                                    akan mengarahkan ke halaman untuk mereset password Anda.
                                </p>

                                <?php
                                
                                    if(isset($_GET['error'])){
                                        $error = $_GET['error'];
                                        if($error == 'invalid'){
                                            echo '<div class="alert alert-danger mt-3">Username tidak ditemukan.</div>';
                                        }
                                    }
                                ?>

                                <form action="forgot-password-action.php" method="POST">
                                    <!-- Form -->
                                    <div class="mb-4">
                                        <label for="username">Username Anda</label>
                                        <div class="input-group">
                                            <input
                                                type="username"
                                                class="form-control"
                                                id="username"
                                                placeholder="Masukkan username"
                                                name="username"
                                                required
                                                autofocus
                                            />
                                        </div>
                                    </div>
                                    <!-- End of Form -->
                                    <div class="d-grid">
                                        <button
                                            type="submit"
                                            class="btn btn-gray-800"
                                        >
                                            Reset Password
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
