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

            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/coming-soon.css?v=<?php echo filemtime(__DIR__ . '/../css/pages/coming-soon.css'); ?>">

            <div class="coming-wrapper">

                <canvas id="particles" class="particles-canvas"></canvas>

                <div class="coming-card" id="comingCard">

                    <div class="coming-left">
                        <div class="rocket">
                            <i class="fas fa-rocket"></i>
                        </div>

                        <div class="coming-badge">Coming Soon</div>

                        <div class="coming-title">Fitur Sedang<br>Dikembangkan</div>

                        <div class="coming-subtitle">
                            Kami sedang mempersiapkan fitur baru dengan tampilan yang lebih modern,
                            performa yang lebih cepat, dan pengalaman pengguna yang lebih nyaman.
                            Nantikan pembaruan selanjutnya pada sistem <strong>Qieos</strong>.
                        </div>

                        <div class="coming-eta">
                            <i class="fas fa-calendar-alt"></i>
                            Diperkirakan rilis akhir tahun 2026
                        </div>

                        <div class="countdown" id="countdown" aria-label="Hitung mundur rilis">
                            <div class="cd-box"><span class="cd-num" id="cd-d">--</span><span class="cd-label">Hari</span></div>
                            <div class="cd-box"><span class="cd-num" id="cd-h">--</span><span class="cd-label">Jam</span></div>
                            <div class="cd-box"><span class="cd-num" id="cd-m">--</span><span class="cd-label">Menit</span></div>
                            <div class="cd-box"><span class="cd-num" id="cd-s">--</span><span class="cd-label">Detik</span></div>
                        </div>

                        <div class="progress-info">
                            <div class="progress-text">Progress Pengembangan</div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar" id="progressBar"></div>
                                <span class="progress-percent" id="progressPercent">0%</span>
                            </div>
                        </div>
                    </div>

                    <div class="coming-right">
                        <div class="feature-grid">

                            <div class="feature-box">
                                <div class="feature-header">
                                    <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                                    <div class="feature-title">Faster Performance</div>
                                </div>
                                <div class="feature-desc">Optimalisasi performa agar proses menjadi lebih cepat dan ringan.</div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-header">
                                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                                    <div class="feature-title">Secure System</div>
                                </div>
                                <div class="feature-desc">Keamanan data ditingkatkan agar seluruh transaksi tetap aman.</div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-header">
                                    <div class="feature-icon"><i class="fas fa-gem"></i></div>
                                    <div class="feature-title">Premium Experience</div>
                                </div>
                                <div class="feature-desc">Desain baru yang lebih elegan, profesional, dan nyaman digunakan.</div>
                            </div>

                        </div>

                        <div class="coming-footer">
                            <i class="fas fa-code me-2"></i> Crafted with ❤️ by <strong>Qieos</strong>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

<?php include '../script/footscript.php'; ?>

<script>
(function() {
    const targetDate = new Date('2026-12-31T23:59:59').getTime();
    const launchDate = new Date('2026-01-01T00:00:00').getTime();
    const now = Date.now();

    function updateCountdown() {
        const now = Date.now();
        const diff = targetDate - now;

        if (diff <= 0) {
            document.getElementById('countdown').innerHTML = '<div class="cd-box cd-done">Sudah Rilis!</div>';
            return;
        }

        const d = Math.floor(diff / (1000 * 60 * 60 * 24));
        const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const s = Math.floor((diff % (1000 * 60)) / 1000);

        document.getElementById('cd-d').textContent = String(d).padStart(2, '0');
        document.getElementById('cd-h').textContent = String(h).padStart(2, '0');
        document.getElementById('cd-m').textContent = String(m).padStart(2, '0');
        document.getElementById('cd-s').textContent = String(s).padStart(2, '0');
    }

    const totalTime = targetDate - launchDate;
    const elapsed = now - launchDate;
    const progress = Math.min(100, Math.max(0, (elapsed / totalTime) * 100));

    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    
    setTimeout(function() {
        progressBar.style.width = progress.toFixed(1) + '%';
        progressPercent.textContent = progress.toFixed(1) + '%';
    }, 800);

    updateCountdown();
    setInterval(updateCountdown, 1000);

    const canvas = document.getElementById('particles');
    const ctx = canvas.getContext('2d');
    let particles = [];
    let animationId;

    function resizeCanvas() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.vx = (Math.random() - 0.5) * 0.5;
            this.vy = (Math.random() - 0.5) * 0.5;
            this.radius = Math.random() * 2 + 1;
            this.opacity = Math.random() * 0.5 + 0.2;
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;

            if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
            if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(99, 102, 241, ${this.opacity})`;
            ctx.fill();
        }
    }

    function initParticles() {
        particles = [];
        const count = Math.min(50, Math.floor((canvas.width * canvas.height) / 15000));
        for (let i = 0; i < count; i++) {
            particles.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        particles.forEach(p => {
            p.update();
            p.draw();
        });

        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < 120) {
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = `rgba(99, 102, 241, ${0.15 * (1 - dist / 120)})`;
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }

        animationId = requestAnimationFrame(animate);
    }

    resizeCanvas();
    initParticles();
    animate();

    window.addEventListener('resize', function() {
        resizeCanvas();
        initParticles();
    });

    const card = document.getElementById('comingCard');
    card.addEventListener('mousemove', function(e) {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const rotateX = (y - centerY) / 30;
        const rotateY = (centerX - x) / 30;

        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });

    card.addEventListener('mouseleave', function() {
        card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
    });

    const featureBoxes = document.querySelectorAll('.feature-box');
    featureBoxes.forEach((box, i) => {
        box.addEventListener('mouseenter', function() {
            featureBoxes.forEach((b, j) => {
                if (i !== j) b.style.opacity = '0.6';
            });
        });

        box.addEventListener('mouseleave', function() {
            featureBoxes.forEach(b => b.style.opacity = '1');
        });
    });
})();
</script>

</body>
</html>