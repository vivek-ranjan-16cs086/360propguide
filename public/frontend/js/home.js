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

    const heroSearch = document.querySelector(".hero-search.searchboxs");
    const heroTabs = document.querySelectorAll(".hero-tab");
    const heroTitle = document.querySelector(".hero-title");
    const keywordInput = document.getElementById("keyword");
    const bhkSelect = document.getElementById("heroBhk");
    const searchBtn = document.getElementById("keybutton");
    const heroLead = document.getElementById("heroLead");
    const cityLinks = document.querySelectorAll(".hero-cities a");
    const cityPicker = document.getElementById("heroCityPicker");
    const cityTrigger = document.getElementById("heroCityTrigger");
    const cityMenu = document.getElementById("heroCityMenu");
    const cityValue = document.getElementById("heroCityValue");
    const citySelect = document.getElementById("heroLocation");
    const cityLabel = document.querySelector(".hero-city-trigger__label");

    function currentCityLabel() {
        const value = citySelect?.value?.trim();
        return value || "Delhi NCR";
    }

    function titleForMode(mode, city) {
        if (mode === "properties") {
            return "Properties to buy in <em>" + city + "</em>";
        }
        if (mode === "commercial") {
            return "Commercial properties in <em>" + city + "</em>";
        }
        return "New projects to buy in <em>" + city + "</em>";
    }

    function leadForMode(mode) {
        if (!heroLead) {
            return "";
        }
        if (mode === "properties") {
            return heroLead.dataset.leadProperties || "";
        }
        if (mode === "commercial") {
            return heroLead.dataset.leadCommercial || "";
        }
        return heroLead.dataset.leadProjects || "";
    }

    const tabCopy = {
        projects: {
            placeholder: "Search for locality, landmark, project or builder",
        },
        properties: {
            placeholder: "Search for locality, landmark, society or builder",
        },
        commercial: {
            placeholder: "Search for locality, landmark, project or builder",
        },
    };

    function slugifyCity(value) {
        return String(value || "")
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
    }

    function applyHeroTab(mode) {
        if (!heroSearch || !tabCopy[mode]) {
            return;
        }

        heroTabs.forEach(function (tab) {
            const active = tab.dataset.mode === mode;
            tab.classList.toggle("is-active", active);
            tab.setAttribute("aria-selected", active ? "true" : "false");
        });

        heroSearch.dataset.searchMode = mode;

        const copy = tabCopy[mode];
        if (heroTitle) {
            heroTitle.innerHTML = titleForMode(mode, currentCityLabel());
        }
        if (heroLead) {
            heroLead.textContent = leadForMode(mode);
        }
        if (keywordInput) {
            keywordInput.placeholder = copy.placeholder;
        }
        if (cityLabel) {
            cityLabel.textContent = mode === "commercial" ? "Look in" : "Buy in";
        }
        if (bhkSelect) {
            bhkSelect.value = mode === "commercial" ? "Shops" : "";
        }

        cityLinks.forEach(function (link) {
            const city = link.dataset.city || link.textContent.trim();
            if (mode === "properties") {
                link.href = "/properties?location=" + encodeURIComponent(city);
            } else if (mode === "commercial") {
                link.href = "/shops-projects-in-" + slugifyCity(city);
            } else {
                link.href = "/flats-in-" + slugifyCity(city);
            }
        });
    }

    heroTabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
            applyHeroTab(tab.dataset.mode);
        });
    });

    if (searchBtn && heroSearch) {
        searchBtn.addEventListener(
            "click",
            function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();

                const mode = heroSearch.dataset.searchMode || "projects";
                const location = heroSearch.querySelector('select[name="location"]')?.value || "";
                const keyword = heroSearch.querySelector(".keyword")?.value.trim() || "";

                if (mode === "properties") {
                    const params = new URLSearchParams();
                    if (location) {
                        params.set("location", location);
                    }
                    if (keyword) {
                        params.set("keyword", keyword);
                    }
                    const query = params.toString();
                    window.location.href = "/properties" + (query ? "?" + query : "");
                    return;
                }

                if (mode === "projects") {
                    const searchLocation = location || keyword;
                    if (searchLocation) {
                        window.location.href = "/flats-in-" + slugifyCity(searchLocation);
                        return;
                    }
                }

                const params = new URLSearchParams();
                if (location) {
                    params.append("location[]", location);
                }
                if (keyword) {
                    params.set("q", keyword);
                    params.set("keyword", keyword);
                }
                if (mode === "commercial") {
                    params.append("type[]", "Shops");
                }

                const query = params.toString();
                window.location.href = "/projects" + (query ? "?" + query : "");
            },
            true,
        );
    }

    if (keywordInput) {
        keywordInput.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("keybutton")?.click();
            }
        });
    }

    function closeCityMenu() {
        if (!cityPicker || !cityTrigger || !cityMenu) {
            return;
        }
        cityPicker.classList.remove("is-open");
        cityPicker.closest(".hero-search-panel")?.classList.remove("is-city-open");
        cityTrigger.setAttribute("aria-expanded", "false");
        cityMenu.hidden = true;
    }

    function openCityMenu() {
        if (!cityPicker || !cityTrigger || !cityMenu) {
            return;
        }
        cityPicker.classList.add("is-open");
        cityPicker.closest(".hero-search-panel")?.classList.add("is-city-open");
        cityTrigger.setAttribute("aria-expanded", "true");
        cityMenu.hidden = false;
    }

    if (cityTrigger && cityPicker) {
        cityTrigger.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (cityPicker.classList.contains("is-open")) {
                closeCityMenu();
            } else {
                openCityMenu();
            }
        });

        cityPicker.querySelectorAll(".hero-city-option").forEach(function (option) {
            option.addEventListener("click", function () {
                const value = option.getAttribute("data-value") || "";
                const label = option.querySelector("span")?.textContent.trim() || "All cities";

                if (citySelect) {
                    citySelect.value = value;
                    citySelect.dispatchEvent(new Event("change", { bubbles: true }));
                }
                if (cityValue) {
                    cityValue.textContent = label;
                }

                cityPicker.querySelectorAll(".hero-city-option").forEach(function (item) {
                    item.classList.toggle("is-selected", item === option);
                });
                const activeTab = document.querySelector(".hero-tab.is-active");
                applyHeroTab(activeTab?.dataset.mode || "projects");
                closeCityMenu();
            });
        });
    }

    document.addEventListener("click", function (event) {
        if (cityPicker && !cityPicker.contains(event.target)) {
            closeCityMenu();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeCityMenu();
        }
    });

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
            slidesPerView: 1.15,
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
                576: { slidesPerView: 1.6, spaceBetween: 16 },
                768: { slidesPerView: 2, spaceBetween: 18 },
                992: { slidesPerView: 3, spaceBetween: 20 },
                1200: { slidesPerView: 3, spaceBetween: 22 },
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
