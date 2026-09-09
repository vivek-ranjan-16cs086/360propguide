document.addEventListener("DOMContentLoaded", function () {
    // Hero Video Loader with Poster fallback
    const video = document.getElementById("myVideo");
    const poster = document.getElementById("heroPoster");

    if (video && poster) {
        let videoLoaded = false;

        function loadHeroVideo() {
            if (videoLoaded) return;
            videoLoaded = true;

            const isMobile = window.matchMedia("(max-width: 767px)").matches;
            const videoSource = isMobile
                ? video.dataset.mobileSrc
                : video.dataset.desktopSrc;

            if (!videoSource) return;

            video.src = videoSource;
            video.load();

            video.addEventListener(
                "canplay",
                function () {
                    video.classList.add("is-loaded");
                    video
                        .play()
                        .then(function () {
                            poster.classList.add("is-hidden");
                        })
                        .catch(function (error) {
                            console.warn("Hero video autoplay notice:", error);
                        });
                },
                { once: true },
            );
        }

        loadHeroVideo();
    }

    // Scroll Down Button Smooth Scroll
    const scrollDownBtn = document.getElementById("scrollDownBtn");
    if (scrollDownBtn) {
        scrollDownBtn.addEventListener("click", function () {
            const targetSection = document.getElementById("propertiesSection");
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            } else {
                window.scrollBy({
                    top: window.innerHeight * 0.85,
                    behavior: "smooth",
                });
            }
        });
    }

    // Keyword Search on Enter Key
    const keywordInput = document.getElementById("keyword");
    if (keywordInput) {
        keywordInput.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("keybutton")?.click();
            }
        });
    }

    // Swiper 2: Popular Projects
    document.querySelectorAll(".mySwiper2").forEach(function (container) {
        const projectSwiper = new Swiper(container, {
            slidesPerView: 2,
            spaceBetween: 12,
            watchOverflow: true,
            observer: true,
            loop: true,
            autoplay: { delay: 3000 },
            disableOnInteraction: false,
            observeParents: true,
            resizeObserver: true,
            roundLengths: true,

            pagination: {
                el: container.querySelector(".swiper-pagination"),
                clickable: true,
            },

            navigation: {
                nextEl: container.querySelector(".swiper-button-next"),
                prevEl: container.querySelector(".swiper-button-prev"),
            },

            breakpoints: {
                // Small mobile
                320: {
                    slidesPerView: 1.5,
                    spaceBetween: 10,
                },

                // Larger mobile
                576: {
                    slidesPerView: 1.5,
                    spaceBetween: 14,
                },

                // Tablet
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },

                // Small desktop
                992: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },

                // Desktop
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                },

                // Large desktop
                1400: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                },
            },
        });

        // Project images load lazily. Recalculate once they have dimensions so
        // Swiper keeps correct slide widths on both mobile and desktop.
        container.querySelectorAll("img").forEach(function (image) {
            if (image.complete) {
                return;
            }

            image.addEventListener("load", function () {
                projectSwiper.update();
            });
        });
    });

    // Swiper 3: Video Insights
    document.querySelectorAll(".mySwiper3").forEach(function (container) {
        new Swiper(container, {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: { delay: 3000 },
            disableOnInteraction: false,
            pagination: {
                el: container.querySelector(".swiper-pagination"),
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector(".swiper-button-next"),
                prevEl: container.querySelector(".swiper-button-prev"),
            },
            breakpoints: {
                // Small mobile
                320: {
                    slidesPerView: 1.5,
                    spaceBetween: 10,
                },

                // Larger mobile
                576: {
                    slidesPerView: 1.5,
                    spaceBetween: 14,
                },

                // Tablet
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },

                // Small desktop
                992: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },

                // Desktop
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                },

                // Large desktop
                1400: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                },
            },
        });
    });

    // Swiper Blogs: Compact Knowledge Base Cards
    document.querySelectorAll(".mySwiperBlogs").forEach(function (container) {
        new Swiper(container, {
            slidesPerView: 1.2,
            spaceBetween: 16,
            loop: true,
            autoplay: { delay: 3000 },
            disableOnInteraction: false,
            pagination: {
                el: container.querySelector(".swiper-pagination"),
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector(".swiper-button-next"),
                prevEl: container.querySelector(".swiper-button-prev"),
            },
            breakpoints: {
                576: { slidesPerView: 2, spaceBetween: 14 },
                768: { slidesPerView: 2.5, spaceBetween: 16 },
                992: { slidesPerView: 4, spaceBetween: 16 },
                1200: { slidesPerView: 4, spaceBetween: 18 },
            },
        });
    });

    // Swiper Feeds: Social Updates
    document.querySelectorAll(".mySwiperFeeds").forEach(function (container) {
        new Swiper(container, {
            slidesPerView: 1.2,
            spaceBetween: 16,
            pagination: {
                el: container.querySelector(".swiper-pagination"),
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector(".swiper-button-next"),
                prevEl: container.querySelector(".swiper-button-prev"),
            },
            breakpoints: {
                576: { slidesPerView: 2, spaceBetween: 14 },
                768: { slidesPerView: 2.5, spaceBetween: 16 },
                992: { slidesPerView: 4, spaceBetween: 16 },
                1200: { slidesPerView: 4, spaceBetween: 18 },
            },
        });
    });

    // Swiper 4: YouTube Shorts
    document.querySelectorAll(".mySwiper4").forEach(function (container) {
        new Swiper(container, {
            spaceBetween: 18,
            slidesPerView: "auto",
            loop: true,
            autoplay: { delay: 3000 },
            disableOnInteraction: false,
            pagination: {
                el: container.querySelector(".swiper-pagination"),
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector(".swiper-button-next"),
                prevEl: container.querySelector(".swiper-button-prev"),
            },
            breakpoints: {
                320: { slidesPerView: 1.6, spaceBetween: 14 },
                576: { slidesPerView: 2.5, spaceBetween: 16 },
                768: { slidesPerView: 3.5, spaceBetween: 18 },
                992: { slidesPerView: 4.5, spaceBetween: 20 },
                1200: { slidesPerView: 5, spaceBetween: 20 },
            },
        });
    });

    // Swiper 5: Financial Tools
    document.querySelectorAll(".mySwiper5").forEach(function (container) {
        new Swiper(container, {
            slidesPerView: 1.2,
            spaceBetween: 20,
            loop: false,
            autoplay: false,
            navigation: {
                nextEl: container.querySelector(".swiper-button-next"),
                prevEl: container.querySelector(".swiper-button-prev"),
            },
            breakpoints: {
                0: {
                    slidesPerView: 1.2,
                    spaceBetween: 14,
                },

                576: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },

                768: {
                    slidesPerView: 2.8,
                    spaceBetween: 18,
                },

                992: {
                    slidesPerView: 3.8,
                    spaceBetween: 20,
                },

                1200: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                },
            },
        });
    });
});
