<?php 
    include '../script/connection.php';
?>

<!doctype html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Lupa Password - Qieos</title>
        
        <?php include '../script/headscript.php'; ?>
        
        <link rel="stylesheet" href="../assets/css/auth-premium.css?v=<?php echo filemtime('../assets/css/auth-premium.css'); ?>">
    </head>

    <body class="auth-container">
        <!-- One universe, everywhere -->
        <?php include 'components/space-bg.php'; ?>

        <main class="auth-card-wrapper">
            <div class="auth-card">
                <!-- Back Link -->
                <a href="sign-in.php" class="auth-back-link">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke login
                </a>

                <!-- Logo -->
                <a href="../index.php">
                    <img src="../assets/img/brand/qieos.png" 
                         alt="Qieos Logo" 
                         class="auth-logo">
                </a>
                
                <!-- Title -->
                <div class="auth-title-wrap">
                    <h1 class="auth-title">Lupa Password?</h1>
                    <p class="auth-subtitle">Kami bantu Anda memulihkan akses</p>
                    <div class="auth-title-underline"></div>
                </div>

                <!-- Error Messages -->
                <?php
                    if(isset($_GET['error'])){
                        $error = $_GET['error'];
                        if($error == 'invalid'){
                            echo '<div class="auth-alert auth-alert-danger"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Username tidak ditemukan di sistem kami.</div>';
                        }
                    }
                ?>

                <form action="forgot-password-action.php" method="POST" class="auth-form" id="forgotForm">
                    <!-- Username Field -->
                    <div class="auth-form-group">
                        <label for="username" class="auth-label">Username</label>
                        <div class="auth-input-group">
                            <div class="auth-input-icon">
                                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   placeholder="Masukkan username Anda" 
                                   class="auth-input" 
                                   id="username" 
                                   name="username" 
                                   required
                                   autofocus
                                   autocomplete="username">
                        </div>
                    </div>

                    <p style="font-size: 0.825rem; color: #64748b; margin: 0 0 1.5rem; line-height: 1.5;">
                        Kami akan mengarahkan Anda ke halaman untuk membuat password baru.
                    </p>

                    <!-- Submit Button -->
                    <button type="submit" class="auth-btn auth-btn-primary" id="submitBtn">
                        <span>Lanjutkan</span>
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </button>
                </form>
                
                <!-- Footer -->
                <div class="auth-footer">
                    <p class="auth-footer-text">
                        Ingat password Anda? 
                        <a href="sign-in.php" class="auth-link">Login Sekarang</a>
                    </p>
                </div>
            </div>
        </main>

        <?php include '../script/footscript.php'; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Particles
                const container = document.querySelector('.auth-container');
                for(let i = 0; i < 15; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.animationDelay = (Math.random() * 8) + 's';
                    particle.style.animationDuration = (6 + Math.random() * 6) + 's';
                    container.appendChild(particle);
                }

                // Submit State
                const form = document.getElementById('forgotForm');
                const submitBtn = document.getElementById('submitBtn');
                
                if(form && submitBtn) {
                    form.addEventListener('submit', function() {
                        submitBtn.classList.add('auth-btn-loading');
                        submitBtn.innerHTML = `<div class="auth-loading-spinner"></div><span>Memproses...</span>`;
                    });
                }
            });
        </script>
    </body>
</html>
