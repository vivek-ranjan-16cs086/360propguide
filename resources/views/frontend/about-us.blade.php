@extends('frontend.layouts.app')
@section('title', "360 PropGuide | Trusted Real Estate Broker in Delhi NCR")
@section('description',"360 PropGuide – your trusted real estate Broker in Noida, Delhi NCR. From property deals to home loans, we simplify buying in Delhi NCR and noida. Visit here")
@section('keywords', "Real Estate Services")
@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{url('./frontend/about-us.css')}}" />
@endSection
@section('content')
<!-- About Us Section -->
<section class="about-section mt-5">
    <div class="container">
        <div class="d-flex flex-column">
            <div class="text-center mb-0 section-title h1">
                Welcome to 360 PropGuide
            </div>
            <h1 class="text-center h6">your trusted real estate consultant in Noida and Delhi NCR!</h1>
            <div class="mt-sm-4 w-100 h-auto rounded-4 aboutBanner aboutBg pt-5">
                <h2 class="section-title mt-lg-5">About 360PropGuide</h2>
                <p class="col-xl-6 col-lg-7 col-md-8 col-sm-9 aboutUsText">
                    At 360PropGuide, we are more than just a property consultancy but we are your <b>trusted real estate consultant  in Noida</b>, helping you make safer and more profitable property decisions. In a market like Noida and Delhi NCR Information can be confusing and Marketing can be misleading. 
             
                </p>
				<p class="col-xl-6 col-lg-7 col-md-8 col-sm-9 aboutUsText">
                     Hence, the mission of 360PropGuide is simple: To bring clarity, transparency, and data-driven guidance to every buyer and investor. Being your trusted <b>real estate firm in Noida</b>, we look at what's happening in the market and do lots of research to help you find good opportunities at the right time.  
             
                </p>
				<p class="col-xl-6 col-lg-7 col-md-8 col-sm-9 aboutUsText">
                     Whether you are a first-time homebuyer, an end-user, or an investor, our approach focuses on long-term value rather than short-term hype. From project analysis and pricing trends to legal clarity and future growth potential, 360PropGuide ensures that every decision you make is backed by logic, not just emotion.
             
                </p>
            </div>
        </div>


        <div class="row py-5">
            <div class="col-lg-7 about-content">
                <h5 class="section-title">Our Mission</h5>
                <div>
                    <p>The mission of 360PropGuide is to simplify real estate for every buyer by providing clear insights, verified information, risk-aware guidance and complete decision clarity.</p>
                </div>

                <!-- Vison -->
                <div>
                    <h5 class="section-title">Our Vision</h5>
                    <p>
                        The vision of 360PropGuide is to become the real estate company in Noida known for honest advice, deep market intelligence and long-term client trust.
                    </p>
                </div>
                <div class="">
                    <h5 class="section-title">Our promise</h5>
                    <p>At 360PropGuide the team does not just help you buy a property 360PropGuide helps you understand it. Because in real estate the right decision is always more important, than the fastest decision.

                    </p>

                </div>
            </div>
            <div class="col-lg-5">
                <img src="{{asset('frontend/promise.jpg')}}"
                    class="vision-img" alt="vision" />
            </div>
            <!-- Investors -->

        </div>
    </div>
    <section class="why-us-section py-5 bg-white">
        <div class="container py-4">
            <!-- Section Header -->
            <div class="row mb-5 text-center">
                <div class="col-12">
                    <h2 class="section-title fw-bolder mb-0">Why <span class="highlight">360PropGuide?</span></h2>
                    <div class="title-underline mx-auto mt-2"></div>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- point 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 p-4 bg-white">
                        <h3 class="feature-no fw-bolder mb-2">01</h3>
                        <h4 class="feature-title fw-bold mb-3">Data-Driven Decision Making</h4>
                        <p class="feature-text text-muted mb-0">The team of 360PropGuide analyzes pricing trends, carpet
                            vs area impact, loading and real value so you do not fall into marketing traps.</p>
                    </div>
                </div>

                <!-- Point 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 p-4 bg-white">
                        <h3 class="feature-no fw-bolder mb-2">02</h3>
                        <h4 class="feature-title fw-bold mb-3">Local Market Expertise</h4>
                        <p class="feature-text text-muted mb-0">360PropGuide has knowledge of micro-markets like Noida
                            Expressway, Sector 150 Siddhartha Vihar and Yamuna Expressway which helps you understand
                            where the real growth is.</p>
                    </div>
                </div>

                <!-- Point 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 p-4 bg-white">
                        <h3 class="feature-no fw-bolder mb-2">03</h3>
                        <h4 class="feature-title fw-bold mb-3">Transparency First</h4>
                        <p class="feature-text text-muted mb-0">360PropGuide believes trust is the foundation of estate.
                            Every recommendation of 360PropGuide is backed by facts, not commissions.</p>
                    </div>
                </div>

                <!-- Point 4 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 p-4 bg-white">
                        <h3 class="feature-no fw-bolder mb-2">04</h3>
                        <h4 class="feature-title fw-bold mb-3">Investment-Focused Advisory</h4>
                        <p class="feature-text text-muted mb-0">360PropGuide takes inspiration from firms like Red
                            Kaizen Realty. 360PropGuide helps clients build long-term wealth through property
                            investments, not just short.</p>
                    </div>
                </div>

                <!-- Point 5 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 p-4 bg-white">
                        <h3 class="feature-no fw-bolder mb-2">05</h3>
                        <h4 class="feature-title fw-bold mb-3">End-to-End Support</h4>
                        <p class="feature-text text-muted mb-0">From your inquiry to final possession 360PropGuide stays
                            with you throughout the journey of 360PropGuide.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--  SECTION 2: WHAT WE DO? -->
    <section class="what-we-do-section py-5 bg-light">
        <div class="container py-4">
            <div class="row align-items-center">

                <!-- Left Content: Text and Checklist -->
                <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                    <h2 class="section-title fw-bolder mb-3">What <span class="highlight">We Do?</span></h2>
                    <p class="lead-text fw-medium mb-4">We provide end-to-end real estate advisory, not property
                        listings. 360PropGuide does the following:</p>

                    <ul class="custom-list list-unstyled">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill check-icon me-3 mt-1"></i>
                            <span class="fw-medium text-dark">Property selection based on your goal like end-use or
                                investment</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill check-icon me-3 mt-1"></i>
                            <span class="fw-medium text-dark">Data-backed market insights and price analysis</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill check-icon me-3 mt-1"></i>
                            <span class="fw-medium text-dark">Site visit planning and project comparison</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill check-icon me-3 mt-1"></i>
                            <span class="fw-medium text-dark">Legal clarity and documentation guidance</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill check-icon me-3 mt-1"></i>
                            <span class="fw-medium text-dark">Builder credibility and project risk assessment</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill check-icon me-3 mt-1"></i>
                            <span class="fw-medium text-dark">Post purchase support</span>
                        </li>
                    </ul>

                    <div class="mt-4 p-4 highlight-box shadow-sm bg-white rounded-2">
                        <p class="mb-0 fw-bold">Unlike traditional brokers, our firm does not just sell, but we guide,
                            evaluate, and protect your investment.</p>
                    </div>
                </div>

                <!-- Right Content: Image with Decorative Elements -->
                <div class="col-lg-6 ps-lg-4">
                    <div class="image-wrapper position-relative text-center">
                        <div class="dots-pattern dots-top-right position-absolute z-1"></div>
                        <div class="dots-pattern dots-bottom-left position-absolute z-1"></div>
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="What We Do - Real Estate Meeting"
                            class="img-fluid rounded-4 shadow-lg position-relative main-img z-2">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Descendents -->
    <div class="container mt-3">
        <h2 class="section-title">Our Partners</h2>
        <img src="{{asset('frontend/logos.png')}}" class="w-100" alt="developer logos 360 PropGuide">
    </div>
    

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-7 about-content">
                <h2 class="section-title">Who We Are?</h2>
                <p>
                   360PropGuide is built on the idea that the real estate decisions should not be random guesses but they should be informed strategies.</p>
					<p>We combine market expertise with deep research and on-ground insights to help you navigate the evolving real estate landscape.
					360PropGuide specializes in real estate advisory in Noida, Greater Noida, Ghaziabad, and the entire NCR region covering commercial and investment-grade properties. </p>
				</p>
            </div>
            <div class="col-lg-5">
                <img src="{{asset('frontend/apart.jpg')}}"
                    class="vision-img" alt="vision" />
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-5 order-2 order-lg-1">
                <img src="{{asset('frontend/team.jpg')}}"
                    class="vision-img" alt="vision" />
            </div>
            <div class="col-lg-7 order-1 order-lg-2 about-content">
                <h5 class="section-title">Our Team</h5>
                <h6>The Strength of 360 PropGuide</h6>
                <p>
                   Our employees are <b>360 Propguide</b>'s most valuable resource. Every member brings their special talents and commitment to every encounter, making them vital contributors to our goal of prioritizing our clients.
                </p>
                <p>We have diverse employees, including professionals from the industry, marketers with a love for the profession, and negotiators, all working hand in hand to ensure that our clients get an all-rounded and customized service delivery.
                </p>
                <p>
                   We boast of overseeing an organizational culture that supports creative thinking, learning, and professionalism. It also helps us to be prepared to meet the challenges that come with the new market.
                </p>
                <p>We make sure that our team is growing and developing. They expect and control the changes and tendencies of the market. This enables the team to provide you with thorough, current assistance in addition to advice.</p>
                <p>Meet the collective force behind <b>360 PropGuide</b>, dedicated to revolutionizing the <b>real estate industry</b> and achieving results. Their commitment and knowledge can assist you. They will make your property mission smooth, informed, and rewarding.</p>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-7 about-content">
                <h5 class="section-title">Our Passion</h5>
                <h6>More Than Just Bricks and Mortar</h6>
                <p>
                   At <b>360 Propguide</b>, it's more than simply a business. We are driven by our behaviors, and our interactions show this.
                </p>
                <p>Every property has a unique narrative to tell, and we assist you in matching that tale to your goals and way of life.
                </p>
                <p>
                   We go beyond simply closing the deals. We guide our clients via real estate insights to make sure they have a significant impact on their lives.</p>
                <p>See our passion in action as we strive to make your journey enjoyable and fulfilling at every turn.</p>
            </div>
            <div class="col-lg-5">
                <img src="{{asset('frontend/passion.jpg')}}"
                    class="vision-img" alt="vision" />
            </div>
        </div>
    </div>
    <!-- Careers -->
    <div class="container">

        <div class="about-careers row align-items-center justify-content-between py-3 py-lg-5">

            <div class="col-lg-7">
                <h4>Join our mission-driven team.</h4>
                <p>
                    We are looking for high-agency, mission-driven experts to help
                    build the future of business ownership.
                </p>
            </div>
            <div class="col-lg-5">
                <a href="{{url('careers')}}">

                    <button class="btn customBtn">Check Our Job Openings</button>
                </a>
            </div>
        </div>
    </div>
</section>
<script>
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
</script>
@endSection
