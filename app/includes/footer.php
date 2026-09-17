<?php
// Footer component
// This is included at the bottom of every page
?>
</main>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <p>&copy; 2026 OarNote</p>
            <p class="footer-subtext">Designed for rowers, coaches, and coxswains.</p>
        </div>
    </div>
</footer>

<script>
// Mobile menu toggle
document.getElementById('navbarToggle').addEventListener('click', function() {
    var menu = document.getElementById('navbarMenu');
    menu.classList.toggle('active');
    this.setAttribute('aria-expanded', menu.classList.contains('active'));
});

// Close menu when a link is clicked
document.querySelectorAll('.nav-link').forEach(function(link) {
    link.addEventListener('click', function() {
        document.getElementById('navbarMenu').classList.remove('active');
    });
});
</script>
</body>
</html>
