<?php
$activePage = $activePage ?? '';
$pageTitle = $pageTitle ?? 'Functional Chronic Wellness';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>

<body>
    <div class="glass-background"></div>

    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
                <img src="<?= e(asset('images/logo.webp')) ?>" alt="Functional Chronic Wellness Logo" style="height: 50px; border-radius: 50%;">
                <span>Functional Chronic Wellness</span>
            </a>
            <ul class="nav-links" id="primaryNav">
                <li><a href="/" class="<?= $activePage === 'home' ? 'active' : '' ?>">Home</a></li>
                <li><a href="/about" class="<?= $activePage === 'about' ? 'active' : '' ?>">About</a></li>
                <li><a href="/services" class="<?= $activePage === 'services' ? 'active' : '' ?>">Services & Programs</a></li>
                <li><a href="/resources" class="<?= $activePage === 'resources' ? 'active' : '' ?>">Resources</a></li>
                <li><a href="/blogs" class="<?= $activePage === 'blogs' ? 'active' : '' ?>">Blogs</a></li>
                <li><a href="/enquiry" class="<?= $activePage === 'enquiry' ? 'active' : '' ?>">Register for Enquiry</a></li>
                <li><a href="/contact" class="btn-primary <?= $activePage === 'contact' ? 'active' : '' ?>">Book Appointment</a></li>
            </ul>
            <button type="button" class="hamburger" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="primaryNav">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <main>
        <?php if (!empty($flashMessages)): ?>
            <section class="section" style="padding-bottom:0;">
                <div class="container" style="max-width: 960px;">
                    <?php foreach ($flashMessages as $flash): ?>
                        <?php $isError = ($flash['type'] ?? '') === 'error'; ?>
                        <div style="background: <?= $isError ? 'rgba(220, 53, 69, 0.1)' : 'rgba(45, 106, 79, 0.1)' ?>; color: <?= $isError ? '#dc3545' : 'var(--primary-color)' ?>; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; border: 1px solid <?= $isError ? '#dc3545' : 'var(--primary-color)' ?>;">
                            <i class="fas <?= $isError ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
                            <?= e((string) ($flash['message'] ?? '')) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php include $contentTemplate; ?>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Functional Chronic Wellness</h3>
                    <p>Root-Cause Healing for Chronic Conditions.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/services">Services</a></li>
                        <li><a href="/enquiry">Register for Enquiry</a></li>
                        <li><a href="/contact">Book Appointment</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Us</h4>
                    <p><?= e($contact['ADDRESS_LINE_1'] ?? '') ?></p>
                    <p><?= e($contact['ADDRESS_LINE_2'] ?? '') ?></p>
                    <p><?= e($contact['PHONE_DISPLAY'] ?? '') ?></p>
                </div>
                <div class="footer-section">
                    <h4>Follow Us</h4>
                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem; flex-wrap: wrap;">
                        <a href="<?= e($contact['INSTAGRAM'] ?? '#') ?>" target="_blank" rel="noopener" title="Instagram" style="color: white; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        <a href="<?= e($contact['LINKEDIN'] ?? '#') ?>" target="_blank" rel="noopener" title="LinkedIn" style="color: white; font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                        <a href="<?= e($contact['WHATSAPP_CHANNEL'] ?? '#') ?>" target="_blank" rel="noopener" title="WhatsApp Channel" style="color: white; font-size: 1.5rem;"><i class="fab fa-whatsapp"></i></a>
                        <a href="<?= e($contact['WHATSAPP_GROUP'] ?? '#') ?>" target="_blank" rel="noopener" title="WhatsApp Group" style="color: white; font-size: 1.5rem;"><i class="fas fa-users"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <p>&copy; <?= date('Y') ?> Functional Chronic Wellness. All rights reserved.</p>
                <div style="display: flex; gap: 0.5rem;">
                    <button onclick="openAdminModal()" style="background: transparent; border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.3rem 0.8rem; border-radius: 5px; cursor: pointer; font-size: 0.8rem; transition: all 0.3s;">DB Manager</button>
                </div>
            </div>
        </div>
    </footer>

    <div id="adminModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-card" style="background: white; padding: 2rem; width: 100%; max-width: 400px; text-align: center; position: relative;">
            <span onclick="closeAdminModal()" style="position: absolute; top: 1rem; right: 1rem; cursor: pointer; font-size: 1.5rem;">&times;</span>
            <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">DB Manager Verification</h3>
            <form id="adminVerifyForm" onsubmit="return verifyAdmin(event);" style="margin: 0;">
                <div style="margin-bottom: 1.5rem;">
                    <input type="password" id="adminPinInput" placeholder="Enter Admin PIN" required autocomplete="current-password" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem;">
                    <p id="adminError" style="color: red; font-size: 0.9rem; margin-top: 0.5rem; display: none;">Invalid PIN. Try again.</p>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem;">Verify & Enter</button>
            </form>
        </div>
    </div>

    <script src="<?= e(asset('js/script.js')) ?>"></script>
    <script>
        function openAdminModal() {
            document.getElementById('adminModal').style.display = 'flex';
            document.getElementById('adminError').style.display = 'none';
            document.getElementById('adminPinInput').focus();
        }

        function closeAdminModal() {
            document.getElementById('adminModal').style.display = 'none';
            document.getElementById('adminError').style.display = 'none';
            document.getElementById('adminPinInput').value = '';
        }

        async function verifyAdmin(event) {
            if (event) {
                event.preventDefault();
            }

            const pin = document.getElementById('adminPinInput').value.trim();
            if (pin === '') {
                document.getElementById('adminError').style.display = 'block';
                return false;
            }

            const formData = new FormData();
            formData.append('pin', pin);

            try {
                const response = await fetch('/admin/verify', {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    window.location.href = '/admin/contacts';
                    return false;
                }

                document.getElementById('adminError').style.display = 'block';
            } catch (error) {
                document.getElementById('adminError').style.display = 'block';
                console.error(error);
            }

            return false;
        }

        window.addEventListener('click', function (event) {
            const modal = document.getElementById('adminModal');
            if (event.target === modal) {
                closeAdminModal();
            }
        });
    </script>
</body>

</html>
