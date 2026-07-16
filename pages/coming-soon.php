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

            <style>

                .coming-wrapper{
                    min-height:calc(100vh - 180px);
                    display:flex;
                    justify-content:center;
                    align-items:center;
                }

                .coming-card{

                    position:relative;
                    overflow:hidden;

                    width:100%;
                    max-width:900px;

                    background:#fff;

                    border-radius:26px;

                    padding:70px 50px;

                    box-shadow:
                    0 20px 45px rgba(15,23,42,.08);

                }

                .coming-card::before{

                    content:"";

                    position:absolute;

                    width:350px;
                    height:350px;

                    background:linear-gradient(135deg,#6366f1,#4f46e5);

                    border-radius:50%;

                    top:-180px;
                    right:-140px;

                    opacity:.08;

                }

                .coming-card::after{

                    content:"";

                    position:absolute;

                    width:250px;
                    height:250px;

                    background:#06b6d4;

                    border-radius:50%;

                    left:-120px;
                    bottom:-120px;

                    opacity:.08;

                }

                .rocket{

                    width:120px;
                    height:120px;

                    margin:auto;

                    border-radius:30px;

                    display:flex;
                    align-items:center;
                    justify-content:center;

                    background:linear-gradient(135deg,#6366f1,#4f46e5);

                    color:#fff;

                    font-size:52px;

                    box-shadow:
                    0 15px 35px rgba(79,70,229,.35);

                    animation:float 4s ease-in-out infinite;

                }

                @keyframes float{

                    0%,100%{
                        transform:translateY(0px);
                    }

                    50%{
                        transform:translateY(-15px);
                    }

                }

                .coming-badge{

                    display:inline-block;

                    margin-top:30px;

                    padding:10px 20px;

                    border-radius:50px;

                    background:#eef2ff;

                    color:#4f46e5;

                    font-weight:700;

                    letter-spacing:1px;

                    font-size:13px;

                }

                .coming-title{

                    margin-top:25px;

                    font-size:42px;

                    font-weight:800;

                    color:#1e293b;

                }

                .coming-subtitle{

                    max-width:650px;

                    margin:18px auto 45px;

                    color:#64748b;

                    line-height:1.8;

                    font-size:17px;

                }

                .feature-box{

                    border:1px solid #e2e8f0;

                    border-radius:18px;

                    padding:28px 22px;

                    transition:.35s;

                    background:#fff;

                    height:100%;

                }

                .feature-box:hover{

                    transform:translateY(-8px);

                    box-shadow:0 15px 35px rgba(15,23,42,.08);

                    border-color:#6366f1;

                }

                .feature-icon{

                    width:65px;
                    height:65px;

                    margin:auto;

                    border-radius:18px;

                    display:flex;
                    align-items:center;
                    justify-content:center;

                    background:linear-gradient(135deg,#6366f1,#4f46e5);

                    color:#fff;

                    font-size:24px;

                    margin-bottom:20px;

                }

                .feature-title{

                    font-size:18px;

                    font-weight:700;

                    color:#1e293b;

                }

                .feature-desc{

                    color:#64748b;

                    font-size:14px;

                    margin-top:10px;

                    line-height:1.7;

                }

                .coming-footer{

                    margin-top:45px;

                    color:#94a3b8;

                    font-size:14px;

                }

                /* Mobile */
                @media (max-width: 768px) {

                    .coming-card{
                        padding:40px 25px;
                        border-radius:18px;
                    }

                    .rocket{
                        width:90px;
                        height:90px;
                        font-size:40px;
                    }

                    .coming-title{
                        font-size:30px;
                        line-height:1.25;
                        text-align:center;
                        word-break:break-word;
                    }

                    .coming-subtitle{
                        font-size:15px;
                        line-height:1.7;
                        margin:18px auto 35px;
                    }

                    .coming-badge{
                        font-size:12px;
                        padding:8px 16px;
                    }

                    .feature-title{
                        font-size:17px;
                    }

                    .feature-desc{
                        font-size:14px;
                    }

                }

                /* Extra Small Device */
                @media (max-width: 480px){

                    .coming-card{
                        padding:35px 20px;
                    }

                    .coming-title{
                        font-size:26px;
                        line-height:1.3;
                        padding:0 8px;
                    }

                    .coming-subtitle{
                        font-size:14px;
                    }

                }

            </style>

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