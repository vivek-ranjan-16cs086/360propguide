document.addEventListener("DOMContentLoaded", () => {
    const video = document.getElementById("myVideo");
    const image = document.getElementById("heroImage");

    setTimeout(() => {
        // Set source dynamically (THIS is key)
        if (!video.querySelector("source").src) {
            const source = document.createElement("source");
            source.src = video.dataset.src; // from data-src
            source.type = "video/mp4";
            video.appendChild(source);
        }

        video.load(); // start loading now
		
		setTimeout(() => {
			video.style.display = "block";
			video.play().catch(() => {}); // avoid autoplay error
			image.style.display = "none";
		}, 1000);

    }, 3500);
});


    $('#scrollDownBtn').click(function() {
        $('html, body').animate({
            scrollTop: $(window).scrollTop() + $(window).height()
        }, 800); // Adjust the duration (800ms) as needed
    });

    var swiper = new Swiper(".mySwiper2", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            576: {
                slidesPerView: 1.5,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3.5,
            },
            1400: {
                slidesPerView: 4.5,
            },
        },
    });
    var swiper = new Swiper(".mySwiper3", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            992: {
                slidesPerView: 4,
            },
        },
    });



    var swiper = new Swiper(".mySwiper4", {
        spaceBetween: 30,
        slidesPerView: 1,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        breakpoints: {
            320: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 4,
            },
            992: {
                slidesPerView: 5,
            },
        },
    });
    var swiper = new Swiper(".mySwiper5", {
        slidesPerView: "auto",
        spaceBetween: 20,
        loop: false, // stop infinite loop

        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },

        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 4,
            }
        }
    });	
     