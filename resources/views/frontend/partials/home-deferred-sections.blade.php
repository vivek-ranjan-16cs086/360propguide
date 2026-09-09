
<!-- 360 tool section-->
<section class="propertties-section mt-3">
    <div class="container my-5 bg-white">
        <div class="headline">
            <h2 class="sectionHeader">360propguide Tools</h2>
            <p>The Right Tools, The Right Results, Every Time</p>
        </div>
        <div class="swiper mySwiper5">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <a href="{{route('budget-get')}}"> 
                        <div class="toll-card">
                            <div class="toll-image">
                                <div class="toll-bg">
                                    <img src="{{url('frontend/tools/BUDGET CLC.webp')}}" alt="budget calculator" loading="lazy">
                                </div>
                            </div>
                            <div class="highlight">
                                <h3 class="serviceCardTitle">Budget Calculator</h3>
                                <p>Check your affordability
                                    range for buying home</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="swiper-slide">
                    <a href="{{route('emi-calculator')}}">
                        <div class="toll-card">
                            <div class="toll-image">
                                <div class="toll-bg">
                                    <img src="{{url('frontend/tools/EMI CLC-01.webp')}}" alt="emi calculator" loading="lazy">
                                </div>
                            </div>
                            <div class="highlight">
                                <h3 class="serviceCardTitle">EMI Calculator</h3>
                                <p>Calculate your home loan EMI</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="swiper-slide">
                    <a href="{{ route('area-calculator') }}">
                        <div class="toll-card">
                            <div class="toll-image">
                                <div class="toll-bg">
                                    <img src="{{url('frontend/tools/Area Converter.webp')}}" alt="budget calculator" loading="lazy">
                                </div>
                            </div>
                            <div class="highlight">
                                <h3 class="serviceCardTitle">Area converter</h3>
                                <p>Convert land measurement units</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="swiper-slide">
                    <a href="{{route('loan-calulator')}}">
                        <div class="toll-card">
                            <div class="toll-image">
                                <div class="toll-bg">
                                    <img src="{{url('frontend/tools/Loan Eligibility.webp')}}" alt="budget calculator" loading="lazy">
                                </div>
                            </div>
                            <div class="highlight">
                                <h3 class="serviceCardTitle">Loan Eligibility</h3>
                                <p>Check your loan eligibility for buying home</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="swiper-slide">
                    <a href="{{route('ability-get')}}">
                        <div class="toll-card">
                            <div class="toll-image">
                                <div class="toll-bg">
                                    <img src="{{url('frontend/tools/Buy Ability.webp')}}" alt="budget calculator" loading="lazy">
                                </div>
                            </div>
                            <div class="highlight">
                                <h3 class="serviceCardTitle">Buy Ability</h3>
                                <p>find homes you can afford</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>


    </div>
</section>
<!-- Post Property section -->
<section class="container post-property-section my-5" id="postPropertySection">
    <div class="post-property-wrap">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h3 class="sectionHeader mb-2">Post Property</h3>
                <p class="mb-0">List your property with <b>360 PropGuide</b> and connect with serious buyers and tenants faster.</p>
            </div>
            <a href="{{ route('frontend.login') }}" class="btn customBtn px-4">Post Your Property</a>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('frontend.login') }}" class="d-block h-100">
                    <div class="post-property-card h-100">
                        <div class="post-property-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
                        <h4 class="post-property-title mb-2">Create Listing</h4>
                        <p class="mb-0">Add your property details, location, price, and key highlights in minutes.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('frontend.login') }}" class="d-block h-100">
                    <div class="post-property-card h-100">
                        <div class="post-property-icon"><i class="fa-solid fa-image"></i></div>
                        <h4 class="post-property-title mb-2">Upload Photos</h4>
                        <p class="mb-0">Showcase your property with quality images to improve trust and engagement.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('frontend.login') }}" class="d-block h-100">
                    <div class="post-property-card h-100">
                        <div class="post-property-icon"><i class="fa-solid fa-bullhorn"></i></div>
                        <h4 class="post-property-title mb-2">Get More Reach</h4>
                        <p class="mb-0">Reach genuine prospects through our active real estate audience across Delhi NCR.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
<!-- Post Property section ends here -->

 <!-- youtube videos -->
<section class="container blogSection">
    <div class="blog-card-container my-5">
        <h3 class="sectionHeader  text-danger">360 Video Insights</h3>
        <p class="">360 PropGuide: <b>Real Estate Company</b> Insights for Smart Property Investments</p>
        <div class="swiper mySwiper3">
            <div class="swiper-wrapper  my-3">

                @foreach($youtubeVideo as $video)
                <div class="swiper-slide">
                    <a href="https://www.youtube.com/watch?v={{$video->video_id}}" target="_blank">
                        <div class="youtube-slider card border-0">
                            <div class="position-relative">
                            <img src="{{ $video->thumbnail }}"
                            class="position-relative youtube-thumbnail" alt="360 PropGuide" width="350" height="150" loading="lazy">
                            <div class="duration">
                            {{ formatDuration($video->duration) }}
                            </div>
                            </div>

                            <div class="fw-bold mt-2 youtube-title">{{ $video->title }}</div>
                            <div class="text-secondary">{{ formatViews($video->views) }} views •
                            {{ formatYoutubeDate($video->published_time) }}</div>
                        </div>
                    </a>
                </div>
                @endforeach


            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

<!-- Services section  -->

<section class="container" id="servicesSection">
    <h3 class="sectionHeader">Consumer Based Services</h3>
    <p class="">360 PropGuide: Transforming your property journey with expert <b>Real Estate Services</b></p>
    <div class="pb-5 mx-auto mt-5">
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-icon">1</div>
                <div class="timeline-content">
                    <div class="timeline-icon-bg">
                        <i class="fa-solid fa-handshake fs-4"></i>
                
                    </div>
                    <div>
                        <div class="timeline-title mb-3 h4 serviceCardTitle">Consultancy</div>
                        <p><b>At 360 PropGuide</b>, we offer complete real estate consultancy across Delhi NCR, covering buying, selling, and renting of both residential and commercial properties. Whether you are exploring options in Noida or Greater Noida, our team ensures you get clear, data-backed guidance within your budget. <b>As a leading real estate company</b> in Noida, our focus is to simplify complex decisions and help you move forward with confidence in a constantly evolving market.

                        </p>
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon">2</div>
                <div class="timeline-content">
                    <div class="timeline-icon-bg">
                        <i class="fa-solid fa-hand-holding-dollar fs-4"></i>
                    </div>
                    <div>
                        <div class="mb-3 h4 serviceCardTitle">Home Loan Help</div>
                        <p>Finding the right home loan can significantly impact your overall investment. At 360 PropGuide, we help you secure the best loan options with competitive interest rates through our strong network with major banks across Delhi NCR. Whether you're purchasing a flat, plot, or villa in Noida or Greater Noida, our team works closely with you to make financing smooth and stress-free—just like a reliable <b>real estate agent in Noida</b> guiding you at every step.
                      </p>
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon">3</div>
                <div class="timeline-content">
                    <div class="timeline-icon-bg">
                        <i class="fa-solid fa-pen-ruler fs-4"></i>
                    </div>
                    <div>
                        <div class="mb-3 h4 serviceCardTitle">Interior Design Collaboration</div>
                        <p>Your property becomes truly valuable when it reflects your lifestyle. Through 360 PropGuide, you get access to experienced interior design professionals who specialize in transforming spaces into personalized, functional environments. Whether it’s a modern apartment in Noida or a villa in Greater Noida West, our collaborations ensure thoughtful design execution—something you would expect from seasoned <b>real estate experts in Noida</b> who understand both space and value.
                        </p>
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon">4</div>
                <div class="timeline-content">
                    <div class="timeline-icon-bg">
                        <i class="fa-solid fa-scale-balanced fs-4"></i>
                    </div>
                    <div>
                        <div class="mb-3 h4 serviceCardTitle">Real Estate Legal Services</div>
                        <p>Real estate transactions require strong legal clarity to avoid future risks. At 360 PropGuide, we assist you with documentation, verification, and compliance to ensure your investment remains secure. From builder agreements to registry guidance, every step is handled with precision and transparency—just like a trusted <b>real estate firm in Noida</b> that prioritizes your long-term safety over short-term deals.
                        </p>
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon">5</div>
                <div class="timeline-content">
                    <div class="timeline-icon-bg">
                        <i class="fa-solid fa-person-digging fs-4"></i>
                    </div>
                    <div>
                        <div class="mb-3 h4 serviceCardTitle">Expert Construction Services</div>
                        <p>We also provide end-to-end construction solutions designed for quality, efficiency, and reliability. From planning and design to execution and final delivery, our turnkey services cater to residential as well as commercial projects. Whether you are building your dream home or a business space, we ensure timely and high-quality results—backed by the professionalism of a dependable <b>real estate broker in Noida</b> who understands execution on ground level.
                        </p> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="services">
    <div class="container py-5">
        <h2 class="sectionHeader">Developer Based Services</h2>
        <p class="mt-3 mb-5">All in solutions for <b>real estate development</b></p>
        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-3 aos-init aos-animate">
                <div class="service-card shadow p-3 d-flex flex-column align-items-center">
                    <div class="anim-layer"></div>
                    <i class="fa-solid fa-ruler-combined display-5 mb-3"></i>
                    <div class="mb-3 h4 serviceCardTitle">Planning</div>
                    <p class="text-center mb-0">Site location is pulling together with the flows of the world and local markets to become a prospective location.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 aos-init aos-animate">
                <div class="service-card shadow p-3 d-flex flex-column align-items-center">
                    <div class="anim-layer"></div>
                    <i class="fa-solid fa-chart-simple display-5 mb-3"></i>
                    <div class="mb-3 h4 serviceCardTitle">Marketing</div>
                    <p class="text-center mb-0">Depending on where your project is located, a unified promotion of your project in the digital and offline space will be conducted.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 aos-init aos-animate">
                <div class="service-card shadow p-3 d-flex flex-column align-items-center">
                    <div class="anim-layer"></div>
                    <i class="fa-solid fa-user-tie display-5 mb-3"></i>
                    <div class="mb-3 h4 serviceCardTitle">Sales</div>
                    <p class="text-center mb-0">From lead creation to deal closure, we offer comprehensive sales management for a smooth process.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 aos-init aos-animate">
                <div class="service-card shadow p-3 d-flex flex-column align-items-center">
                    <div class="anim-layer"></div>
                    <i class="fa-solid fa-key display-5 mb-3"></i>
					
                    <div class="mb-3 h4 serviceCardTitle">Leasing</div>
                    <p class="text-center mb-0">Leasing is fine tuned so that good tenants are obtained and good levels of occupancy are maintained.</p>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- Knowledge Base -->

<section class="container blogSection" id="blogSection">
    <div class="blog-card-container">
        <h3 class="sectionHeader ">360 Knowledge Base</h3>
        <p class="">Expert insights for <b>Delhi NCR properties</b> for smarter investments</p>
        <div class="swiper mySwiper3">
            <div class="swiper-wrapper">
               @if(!empty($pageData['blogs']) && count($pageData['blogs']) > 0)
    @foreach($pageData['blogs'] as $blog)
        
        <div class="swiper-slide">
            <a href="{{ route('blogs.details', $blog->slug) }}">
                <div class="itemSlider card">
                    <img src="{{ url('storage/' . $blog->featured_image) }}"
                         class="position-relative" alt="360 PropGuide" width="350" height="150" loading="lazy"> 
                    <div class="projectDescp">
                        <p class="title ">{{ $blog->title }}</p> 
                        <p class="text-secondary">
                            <i class="fa-solid fa-calendar-days"></i> {{ formatDate($blog->created_at) }} 
                        </p>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
@endif

            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>


<!-- Knowledge Base -->

 <!--<section class="container blogSection">
    <div class="blog-card-container my-5">
        <h3 class="sectionHeader">Facebook Feeds</h3>
        <p class="">Smart strategies to grow your wealth with <b>360 PropGuide</b></p>
        <div class="swiper mySwiper3">
            <div class="swiper-wrapper  my-3">
               @if(!empty($feeds['data']) && count($feeds['data']) > 0)
                    @foreach(array_slice($feeds['data'],0,5) as $feed)
                        <div class="swiper-slide">
                        <a href="{{$feed['permalink_url']}}" target="_blank">
                                <div class="itemSlider card">
                                    <img src="{{ $feed['full_picture'] }}"
                                         class="position-relative feed-img" alt="360 PropGuide" width="350" height="150" loading="lazy">
                                    
                                </div>
                        </a>
                        </div>
                    @endforeach
                @endif

            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>-->