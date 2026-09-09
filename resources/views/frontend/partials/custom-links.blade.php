@php
    $footerCustomLinks = collect($footerCustomLinks ?? []);
    $footerCustomLinksTotal = (int) ($footerCustomLinksTotal ?? $footerCustomLinks->count());
@endphp

@if ($footerCustomLinks->isNotEmpty())
    <div class="custom-link-section container my-4 commonLinks">
        <h5 class="sectionHeader">Explore More</h5>

        <div class="custom-links-card">
            <div class="scroll-box">
                <div class="row g-2" id="footer-container" data-page="1">
                    @foreach ($footerCustomLinks as $link)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 footer-item">
                            <a href="{{ url($link['url']) }}" class="custom-link-item">
                                <i class="fa-solid fa-location-arrow link-icon"></i>
                                <span class="link-text">{{ $link['text'] }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($footerCustomLinksTotal > $footerCustomLinks->count())
            <div class="custom-links-footer text-center">
                <button class="btn load-more-btn custom-load-btn" data-target="footer">
                    <i class="fa-solid fa-plus me-1"></i>
                    View More Links
                </button>
            </div>
        @endif
    </div>
@endif

@if (!($onProjects ?? false) && !empty($propertyLinks))
    <div class="custom-link-section container my-4 commonLinks">
        <h5 class="sectionHeader">Explore Properties</h5>

        <div class="custom-links-card">
            <div class="scroll-box">
                <div class="row g-2" id="property-container" data-page="1">
                    @foreach ($propertyLinks as $plink)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 property-item">
                            <a href="{{ url($plink['url']) }}" class="custom-link-item">
                                <i class="fa-solid fa-location-arrow link-icon"></i>
                                <span class="link-text">{{ $plink['text'] }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            @if (($propertyLinksTotal ?? 0) > count($propertyLinks))
                <div class="custom-links-footer text-center">
                    <button class="btn load-more-btn custom-load-btn" data-target="property">
                        <i class="fa-solid fa-plus me-1"></i>
                        View More Links
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif
