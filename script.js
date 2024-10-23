// script.js
document.addEventListener('DOMContentLoaded', function() {
    // Slider functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;

    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }

    // Add event listeners for slider arrows
    document.querySelector('.next-arrow').addEventListener('click', nextSlide);
    document.querySelector('.prev-arrow').addEventListener('click', prevSlide);

    // Auto advance slides
    setInterval(nextSlide, 5000);

    // Drops section scroll functionality
    const dropsContainer = document.querySelector('.drops-grid');
    const scrollAmount = 300;

    document.querySelector('.drops-section .prev').addEventListener('click', () => {
        dropsContainer.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    });

    document.querySelector('.drops-section .next').addEventListener('click', () => {
        dropsContainer.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            // Add to cart logic here
            console.log(`Added product ${productId} to cart`);
            updateCartCount();
        });
    });

    function updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        // Update cart count logic here
    }

    // Search functionality
    const searchIcon = document.querySelector('.search-icon');
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'search-input';
    searchInput.style.display = 'none';

    searchIcon.after(searchInput);

    searchIcon.addEventListener('click', () => {
        searchInput.style.display = searchInput.style.display === 'none' ? 'block' : 'none';
        if (searchInput.style.display === 'block') {
            searchInput.focus();
        }
    });

    // Mobile menu toggle
    const mobileMenuButton = document.createElement('button');
    mobileMenuButton.className = 'mobile-menu-toggle';
    mobileMenuButton.innerHTML = '<i class="fas fa-bars"></i>';
    
    document.querySelector('.nav-icons').before(mobileMenuButton);

    mobileMenuButton.addEventListener('click', () => {
        document.querySelector('.nav-links').classList.toggle('show');
    });
});