<!doctype html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Buat Akun - Qieos</title>
        
        <?php include "../script/headscript.php"; ?>
        
        <link rel="stylesheet" href="../assets/css/auth-premium.css?v=<?php echo filemtime('../assets/css/auth-premium.css'); ?>">
    </head>

    <body class="auth-container">
        <!-- One universe, everywhere -->
        <?php include 'components/space-bg.php'; ?>

        <main class="auth-card-wrapper">
            <div class="auth-card">
                <!-- Logo -->
                <a href="../index.php">
                    <img src="../assets/img/brand/qieos.png" 
                         alt="Qieos Logo" 
                         class="auth-logo">
                </a>
                
                <!-- Title -->
                <div class="auth-title-wrap">
                    <h1 class="auth-title">Mulai Petualangan</h1>
                    <p class="auth-subtitle">Daftarkan akun Qieos Anda</p>
                    <div class="auth-title-underline"></div>
                </div>

                <!-- Error Messages -->
                <?php
                    if(isset($_GET["error"])){
                        if($_GET["error"] == "empty"){
                            echo '<div class="auth-alert auth-alert-danger"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Semua field harus diisi.</div>';
                        }
                        if($_GET["error"] == "password"){
                            echo '<div class="auth-alert auth-alert-danger"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Password dan konfirmasi tidak sama.</div>';
                        }
                        if($_GET["error"] == "username"){
                            echo '<div class="auth-alert auth-alert-danger"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Username sudah terdaftar.</div>';
                        }
                    }
                ?>

                <form action="sign-up-action.php" class="auth-form" method="POST" id="signUpForm">
                    <!-- Name Field -->
                    <div class="auth-form-group">
                        <label for="name" class="auth-label">Nama Lengkap</label>
                        <div class="auth-input-group">
                            <div class="auth-input-icon">
                                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 15a6 6 0 1112 0v1H4v-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   placeholder="Masukkan nama lengkap" 
                                   class="auth-input" 
                                   id="name" 
                                   name="name" 
                                   required
                                   autocomplete="name">
                        </div>
                    </div>

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
                                   placeholder="Pilih username unik" 
                                   class="auth-input" 
                                   id="username" 
                                   name="username" 
                                   required
                                   autocomplete="username">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="auth-form-group">
                        <label for="password" class="auth-label">Password</label>
                        <div class="auth-input-group">
                            <div class="auth-input-icon">
                                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input type="password" 
                                   placeholder="Buat password yang kuat" 
                                   class="auth-input" 
                                   id="password" 
                                   name="password" 
                                   required
                                   autocomplete="new-password">
                            <button type="button" class="auth-password-toggle" id="togglePassword1" aria-label="Toggle Password Visibility">
                                <svg class="eye-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-fill" id="strengthFill"></div>
                        </div>
                        <p class="small text-muted mt-1">Minimal 8 karakter dengan huruf, angka, dan simbol</p>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="auth-form-group">
                        <label for="confirm_password" class="auth-label">Konfirmasi Password</label>
                        <div class="auth-input-group">
                            <div class="auth-input-icon">
                                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input type="password" 
                                   placeholder="Ulangi password Anda" 
                                   class="auth-input" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required
                                   autocomplete="new-password">
                            <button type="button" class="auth-password-toggle" id="togglePassword2" aria-label="Toggle Password Visibility">
                                <svg class="eye-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="auth-form-group" style="margin-bottom: 1.5rem;">
                        <label class="auth-checkbox-wrapper" for="terms">
                            <input class="auth-checkbox" type="checkbox" name="terms" id="terms">
                            <span style="font-size: 0.825rem;">Saya setuju dengan <a href="#" class="auth-link">Syarat & Ketentuan</a></span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="auth-btn auth-btn-primary" id="submitBtn" disabled>
                        <span>Buat Akun</span>
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </button>
                </form>
                
                <!-- Footer -->
                <div class="auth-footer">
                    <p class="auth-footer-text">
                        Sudah punya akun? 
                        <a href="sign-in.php" class="auth-link">Login Sekarang</a>
                    </p>
                </div>
            </div>
        </main>

        <?php include "../script/footscript.php"; ?>

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

                // Terms Checkbox enable/disable submit button
                const termsCheckbox = document.getElementById('terms');
                const submitBtn = document.getElementById('submitBtn');

                if(termsCheckbox && submitBtn) {
                    termsCheckbox.addEventListener('change', function() {
                        submitBtn.disabled = !this.checked;
                    });
                }

                // Toggle Icons
                const toggleIconSVG = `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
                const toggleIconHide = `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.763c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.092-4.092a3 3 0 11-4.243-4.243m4.242 4.242L3 3l18 18"></path></svg>`;
                
                function setupToggle(btnId, inputId) {
                    const btn = document.getElementById(btnId);
                    const input = document.getElementById(inputId);
                    if(btn && input) {
                        btn.addEventListener('click', function() {
                            const isPassword = input.getAttribute('type') === 'password';
                            input.setAttribute('type', isPassword ? 'text' : 'password');
                            this.innerHTML = isPassword ? toggleIconHide : toggleIconSVG;
                        });
                    }
                }
                
                setupToggle('togglePassword1', 'password');
                setupToggle('togglePassword2', 'confirm_password');

                // Password Strength
                const passwordInput = document.getElementById('password');
                const strengthFill = document.getElementById('strengthFill');
                
                if(passwordInput && strengthFill) {
                    passwordInput.addEventListener('input', function() {
                        const val = this.value;
                        let strength = 0;
                        if(val.length >= 8) strength++;
                        if(/[A-Z]/.test(val)) strength++;
                        if(/[a-z]/.test(val)) strength++;
                        if(/[0-9]/.test(val)) strength++;
                        if(/[^A-Za-z0-9]/.test(val)) strength++;
                        
                        const width = ['0%', '20%', '40%', '60%', '80%', '100%'][strength];
                        const color = ['#cbd5e1', '#ef4444', '#f59e0b', '#fbbf24', '#10b981', '#059669'][strength];
                        
                        strengthFill.style.width = width;
                        strengthFill.style.background = color;
                    });
                }

                // Confirm Match
                const confirmInput = document.getElementById('confirm_password');
                if(passwordInput && confirmInput) {
                    confirmInput.addEventListener('input', function() {
                        if(this.value !== '' && this.value !== passwordInput.value) {
                            this.style.borderColor = '#ef4444';
                            this.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
                        } else if(this.value === passwordInput.value && this.value !== '') {
                            this.style.borderColor = '#10b981';
                            this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
                        } else {
                            this.style.borderColor = '';
                            this.style.boxShadow = '';
                        }
                    });
                }

                // Submit State
                const form = document.getElementById('signUpForm');
                
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
