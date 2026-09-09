@php
    $limit = 10;
    $projects = $projects ?? (object) [];

    /*
    |--------------------------------------------------------------------------
    | Current Project Location
    |--------------------------------------------------------------------------
    */
    $currentLocation = strtolower(trim($projects->location ?? ''));
    $currentCity = strtolower(trim($projects->cities ?? ''));
    $currentLocationSlug = str_replace(' ', '-', $currentLocation);
    $legacyLocationSlug = 'flats-in-' . str_replace(' ', '-', $currentCity) . '-' . $currentLocationSlug;

    /*
    |--------------------------------------------------------------------------
    | Filter General Links
    |--------------------------------------------------------------------------
    | General link ke text/url mein current project ki location/city honi chahiye.
    */
    $filteredGeneralLinks = collect($generalLinks ?? [])->filter(function ($link) use ($currentLocation, $currentCity) {

        $text = strtolower(trim($link['text'] ?? ''));
        $url  = strtolower(trim($link['url'] ?? ''));

        return
            ($currentLocation && str_contains($text, $currentLocation)) ||
            ($currentCity && str_contains($text, $currentCity)) ||
            ($currentLocation && str_contains($url, str_replace(' ', '-', $currentLocation))) ||
            ($currentCity && str_contains($url, str_replace(' ', '-', $currentCity)));

    });

    $filteredGeneralLinks = $filteredGeneralLinks->reject(function ($link) use ($currentLocation, $currentLocationSlug) {
        $url = strtolower(trim($link['url'] ?? '', '/'));

        return $currentLocation
            && preg_match('/^\d+-bhk-flats-in-/', $url)
            && str_contains($url, $currentLocationSlug);
    });

    $filteredGeneralLinks = $filteredGeneralLinks->reject(function ($link) use ($legacyLocationSlug) {
        return $legacyLocationSlug !== 'flats-in--' &&
            strtolower(trim($link['url'] ?? '', '/')) === $legacyLocationSlug;
    });

    if (($projectType ?? null) === 'shop') {
        $filteredGeneralLinks = $filteredGeneralLinks->filter(function ($link) {
            return str_contains(strtolower($link['text'] ?? ''), 'shop');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Custom Links
    |--------------------------------------------------------------------------
    | CustomLinks table mein location/city column nahi hai.
    | Isliye name/title/slug ke basis par current location match kar rahe hain.
    */
    $filteredCustomLinks = collect($customLinks ?? [])->filter(function ($link) use ($currentLocation, $currentCity) {

        $name  = strtolower(trim($link->name ?? ''));
        $title = strtolower(trim($link->title ?? ''));
        $slug  = strtolower(trim($link->slug ?? ''));

        $locationSlug = str_replace(' ', '-', $currentLocation);
        $citySlug     = str_replace(' ', '-', $currentCity);

        return
            ($currentLocation && str_contains($name, $currentLocation)) ||
            ($currentLocation && str_contains($title, $currentLocation)) ||
            ($currentLocation && str_contains($slug, $locationSlug)) ||
            ($currentCity && str_contains($name, $currentCity)) ||
            ($currentCity && str_contains($title, $currentCity)) ||
            ($currentCity && str_contains($slug, $citySlug));

    });

    $filteredCustomLinks = $filteredCustomLinks->reject(function ($link) use ($currentLocation, $currentLocationSlug) {
        $slug = strtolower(trim($link->slug ?? '', '/'));

        return $currentLocation
            && preg_match('/^\d+-bhk-flats-in-/', $slug)
            && str_contains($slug, $currentLocationSlug);
    });

    $filteredCustomLinks = $filteredCustomLinks->reject(function ($link) use ($legacyLocationSlug) {
        return $legacyLocationSlug !== 'flats-in--' &&
            strtolower(trim($link->slug ?? '', '/')) === $legacyLocationSlug;
    });

    if (($projectType ?? null) === 'shop') {
        $filteredCustomLinks = $filteredCustomLinks->filter(function ($link) {
            return str_contains(strtolower($link->name ?? $link->title ?? ''), 'shop');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Merge General + Custom Links
    |--------------------------------------------------------------------------
    */
    $customExploreLinks = $filteredCustomLinks->map(function ($link) {
        return [
            'text' => $link->name ?? $link->title,
            'title' => $link->title,
            'url' => trim($link->slug ?? '', '/'),
        ];
    });

    $exploreLinks = $filteredGeneralLinks
        ->concat($customExploreLinks)
        ->filter(fn($link) => !empty($link['url']))
        ->unique(fn($link) => strtolower(trim(parse_url($link['url'], PHP_URL_PATH) ?? $link['url'], '/')))
        ->values();

@endphp

@php
    $configurationLinks = collect($configurationLinks ?? [])
        ->unique('url')
        ->values();
@endphp


{{-- ===================================================================== --}}
{{-- === EXPLORE MORE / GENERAL LINKS ==================================== --}}
{{-- ===================================================================== --}}
@if (!$onProperties && ($exploreLinks->count() || $filteredCustomLinks->count()))

    <div class="custom-link-section container my-4 commonLinks">

        <h5 class="sectionHeader">
            Explore More {{ strtoupper($projects->cities) }}
        </h5>

        <div class="custom-links-card">

            <div class="scroll-box">

                <div class="row g-2" id="general-container" data-page="1">

                    {{-- ===================================================== --}}
                    {{-- GENERAL LINKS                                         --}}
                    {{-- ===================================================== --}}

                    @foreach ($exploreLinks as $link)

                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 general-item">

                            <a href="{{ url($link['url']) }}"
                               class="custom-link-item">

                                <i class="fa-solid fa-location-arrow link-icon"></i>

                                <span class="link-text">
                                    {{ $link['text'] }}
                                </span>

                            </a>

                        </div>

                    @endforeach


                    {{-- ===================================================== --}}
                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- VIEW MORE BUTTON                                      --}}
        {{-- ===================================================== --}}

        @if ($exploreLinks->count() > 8)

            <div class="custom-links-footer text-center">

                <button class="btn load-more-btn custom-load-btn"
                        data-target="general">

                    <i class="fa-solid fa-plus me-1"></i>

                    View More Links

                </button>

            </div>

        @endif

    </div>

@endif




{{-- ===================================================================== --}}
{{-- === RELATED CITY LINKS ============================================== --}}
{{-- ===================================================================== --}}
@if (!empty($relatedCityLinks))

    <div class="custom-link-section container my-4 commonLinks">

        <h5 class="sectionHeader">
            Explore Other Related Links
        </h5>

        <div class="custom-links-card">

            <div class="scroll-box related-city-scroll">

                <div class="row g-2" id="related-city-container" data-page="1">

                    @foreach ($relatedCityLinks as $cityGroup)

                        <div class="row g-2 related-city-row">

                            @foreach ($cityGroup['links'] as $link)

                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 general-item">

                                    <a href="{{ url($link['url']) }}"
                                       class="custom-link-item">

                                        <i class="fa-solid fa-location-arrow link-icon"></i>

                                        <span class="link-text">
                                            {{ $link['text'] }}
                                        </span>

                                    </a>

                                </div>

                            @endforeach

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

        @php
            $relatedCityLinkCount = collect($relatedCityLinks)
                ->sum(fn($cityGroup) => count($cityGroup['links']));
        @endphp

      @if ($relatedCityLinkCount > 12)

    <div class="custom-links-footer text-center">
        <button class="btn load-more-btn custom-load-btn"
                data-target="related-city">
            <i class="fa-solid fa-plus me-1"></i>
            View More Links
        </button>
    </div>

@endif

    </div>

@endif



{{-- ===================================================================== --}}
{{-- === BHK ============================================================= --}}
{{-- ===================================================================== --}}
@if (!$onProperties && $configurationLinks->count())

<div class="custom-link-section container my-4 commonLinks">

    <h5 class="sectionHeader">
        Explore by Configuration
    </h5>

    <div class="custom-links-card">

        <div class="scroll-box">

            <div class="row g-2"
                 id="configuration-container"
                 data-page="1">

                @foreach ($configurationLinks as $link)

                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 bhk-item">

                        <a href="{{ url($link['url']) }}"
                           class="custom-link-item">

                            <i class="fa-solid fa-location-arrow link-icon"></i>

                            <span class="link-text">
                                {{ $link['text'] }}
                            </span>

                        </a>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endif


{{-- ===================================================================== --}}
{{-- === PROPERTY ======================================================== --}}
{{-- ===================================================================== --}}

@if (!$onProjects && count($propertyLinks))

<div class="custom-link-section container my-4 commonLinks">

    <h5 class="sectionHeader">
        Explore Properties
    </h5>

    <div class="custom-links-card">

        <div class="scroll-box">

            <div class="row g-2"
                 id="property-container"
                 data-page="1">

                @foreach ($propertyLinks as $plink)

                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 property-item">

                        <a href="{{ url($plink['url']) }}"
                           class="custom-link-item">

                            <i class="fa-solid fa-location-arrow link-icon"></i>

                            <span class="link-text">
                                {{ $plink['text'] }}
                            </span>

                        </a>

                    </div>

                @endforeach

            </div>

        </div>


        @if($propertyLinksTotal > count($propertyLinks))

            <div class="custom-links-footer text-center">

                <button class="btn load-more-btn custom-load-btn"
                        data-target="property">

                    <i class="fa-solid fa-plus me-1"></i>

                    View More Links

                </button>

            </div>

        @endif

    </div>

</div>

@endif