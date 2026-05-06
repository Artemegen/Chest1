</main>
<footer class="footer">
    <div class="container footer-inner">
        <p>© 2025 gazopttorg.ru — Продажа газового оборудования</p>
    </div>
</footer>
<script>
    // бургер-меню
    const burger = document.getElementById('burgerBtn');
    const navMenu = document.getElementById('navMenu');
    if(burger) {
        burger.addEventListener('click', () => navMenu.classList.toggle('active'));
    }
    // плавный скролл
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) target.scrollIntoView({behavior: 'smooth'});
        });
    });
</script>
</body>
</html>