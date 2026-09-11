<?php
include '../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Coming Soon - Qieos</title>
    <?php include '../script/headscript.php'; ?>

</head>

<body>
    <?php include 'components/sidebar.php'; ?>

    <main class="content">
    <?php include 'components/navbar.php'; ?>

        <div class="container-fluid px-0 mt-4 mb-5">

            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/coming-soon.css">

            <div class="coming-wrapper">

                <div class="coming-card text-center">

                    <div class="rocket">
                        <i class="fas fa-rocket"></i>
                    </div>

                    <div class="coming-badge">
                        COMING SOON
                    </div>

                    <div class="coming-title">
                        Fitur Sedang Dikembangkan
                    </div>

                    <div class="coming-subtitle">
                        Kami sedang mempersiapkan fitur baru dengan tampilan yang lebih modern,
                        performa yang lebih cepat, dan pengalaman pengguna yang lebih nyaman.
                        Nantikan pembaruan selanjutnya pada sistem <strong>Qieos</strong>.
                    </div>

                    <div class="row g-4">

                        <div class="col-lg-4">

                            <div class="feature-box">

                                <div class="feature-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>

                                <div class="feature-title">
                                    Faster Performance
                                </div>

                                <div class="feature-desc">
                                    Optimalisasi performa agar proses menjadi lebih cepat dan ringan.
                                </div>

                            </div>

                        </div>

                        <div class="col-lg-4">

                            <div class="feature-box">

                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>

                                <div class="feature-title">
                                    Secure System
                                </div>

                                <div class="feature-desc">
                                    Keamanan data ditingkatkan agar seluruh transaksi tetap aman.
                                </div>

                            </div>

                        </div>

                        <div class="col-lg-4">

                            <div class="feature-box">

                                <div class="feature-icon">
                                    <i class="fas fa-gem"></i>
                                </div>

                                <div class="feature-title">
                                    Premium Experience
                                </div>

                                <div class="feature-desc">
                                    Desain baru yang lebih elegan, profesional, dan nyaman digunakan.
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="coming-footer">

                        <i class="fas fa-code me-2 text-primary"></i>

                        Crafted with ❤️ by <strong>Qieos</strong>

                    </div>

                </div>

            </div>

        </div>
    </main>

<?php include '../script/footscript.php'; ?>

</body>
</html>