<header id="mainHeader" class='w-full fixed top-0 left-0 bg-card border-b border-border-base shadow-sm flex align-center justify-center py-5 z-50 transition-transform duration-300'>
    <img src="{{ $logoPath }}" class='h-10' alt="Logo">
</header>


<script>
    let lastScroll = 0;
    const header = document.getElementById('mainHeader');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll <= 0) {
            // At the very top → always show
            header.classList.remove('-translate-y-full');
            return;
        }

        if (currentScroll > lastScroll) {
            // Scrolling down → hide header (slide up)
            header.classList.add('-translate-y-full');
        } else {
            // Scrolling up → show header (slide down)
            header.classList.remove('-translate-y-full');
        }

        lastScroll = currentScroll;
    });
</script>