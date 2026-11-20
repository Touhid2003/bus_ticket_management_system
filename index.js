document.addEventListener('DOMContentLoaded', function () {

    const carouselElement = document.getElementById('busGoCarousel');

    if (carouselElement) {
        new bootstrap.Carousel(carouselElement, {
            interval: 4000,
            ride: 'carousel',
            pause: false,
            wrap: true,
            touch: true
        });
    }

});
