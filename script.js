document.addEventListener('DOMContentLoaded', function () {
    // Slider functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;

    // Show the slide at the given index
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
    }

    // Show the next slide
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides; // Loop back to the first slide
        showSlide(currentSlide);
    }

    // Show the previous slide
    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides; // Loop back to the last slide
        showSlide(currentSlide);
    }

    // Add event listeners for slider arrows
    document.querySelector('.next-arrow').addEventListener('click', nextSlide);
    document.querySelector('.prev-arrow').addEventListener('click', prevSlide);

    // Auto advance slides every 5 seconds
    setInterval(nextSlide, 5000);

    // Scrolling functionality for drops-section
    const dropsContainer = document.querySelector('.drops-container');
    const scrollAmount = 300; // Adjust scroll distance

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
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            console.log(`Added product ${productId} to cart`);
            updateCartCount();
        });
    });

    function updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        // Placeholder for cart count update logic
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

    // Drops grid scrolling
    const dropsGrid = document.querySelector('.drops-grid');
    const prevButton = document.querySelector('.prev-button');
    const nextButton = document.querySelector('.next-button');
    const cardWidth = 300 + 16; // card width + gap

    prevButton.addEventListener('click', () => {
        dropsGrid.scrollLeft -= cardWidth;
    });

    nextButton.addEventListener('click', () => {
        dropsGrid.scrollLeft += cardWidth;
    });

    // Best sellers grid mouse drag scrolling
    const grid = document.querySelector('.best-sellers-grid');
    let isDown = false;
    let startX;
    let scrollLeft;

    grid.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - grid.offsetLeft;
        scrollLeft = grid.scrollLeft;
    });

    grid.addEventListener('mouseleave', () => {
        isDown = false;
    });

    grid.addEventListener('mouseup', () => {
        isDown = false;
    });

    grid.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - grid.offsetLeft;
        const walk = (x - startX) * 2;
        grid.scrollLeft = scrollLeft - walk;
    });
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', () => {
            const categoryTitle = card.querySelector('.category-title').textContent;
            console.log(`Category clicked: ${categoryTitle}`);
            // Add your navigation or other functionality here
        });
    });

    // Optional: Add loading animation for images
    document.querySelectorAll('.category-image').forEach(img => {
        img.addEventListener('load', () => {
            img.style.opacity = '1';
        });
        
        img.addEventListener('error', () => {
            img.src = 'path/to/placeholder-image.jpg'; // Add fallback image path
        });
    });
});
