<section class="hero">
    <h1>Welcome to Cris Inn Resort</h1>
    <p>Your perfect getaway destination located in the Island Garden City of Samal. Enjoy a relaxing stay with beautiful beachfront views and luxury amenities.</p>
</section>

<section class="carousel">
    <div class="carousel-inner">
        <img src="images/11.jpg" alt="Image 1">
        <img src="images/22.jpg" alt="Image 2">
        <img src="images/33.jpg" alt="Image 3">
    </div>
</section>

<script>
    let currentIndex = 0;
    const images = document.querySelectorAll('.carousel-inner img');
    const totalImages = images.length;

    function showNextImage() {
        currentIndex = (currentIndex + 1) % totalImages;
        const offset = -currentIndex * 100; // Calculate the offset for each image
        document.querySelector('.carousel-inner').style.transform = `translateX(${offset}%)`;
    }

    // Automatically change images every 3 seconds
    setInterval(showNextImage, 3000);
</script>