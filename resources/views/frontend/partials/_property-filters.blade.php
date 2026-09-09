@php
    $locations = $locations ?? [];
    $configurations = $configurations ?? [];
    $constructionStatuses = $constructionStatuses ?? [];
    $furnishingTypes = $furnishingTypes ?? [];
@endphp

<div class="filter-box recommended h-fit" id="filter-form">
    @csrf
    <input type="hidden" name="filters" value="filters" />
    <input type="hidden" id="min_price" name="min_price">
    <input type="hidden" id="max_price" name="max_price">

    <div class="filter-header">
        <span class="filter-header__title"><i class="fa-solid fa-sliders" aria-hidden="true"></i> Filters</span>
        <a href="{{ url('properties') }}" class="filter-header__reset" id="resetFilters">Reset</a>
    </div>

    <div class="filter-accordion">
        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Location</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body" id="locationFilter">
                @if(count($locations) > 8)
                    <div class="filter-search-box">
                        <i class="fa-solid fa-magnifying-glass filter-search-icon" aria-hidden="true"></i>
                        <input type="search" class="filter-search-input" data-filter-search="locationList" placeholder="Search location" autocomplete="off">
                    </div>
                @endif
                <div class="filter-checkbox-list {{ count($locations) > 8 ? 'filter-limited' : '' }}" id="locationList">
                    @foreach ($locations as $index => $location)
                        @php $id = 'location-' . $index; @endphp
                        <label class="filter-custom-checkbox {{ count($locations) > 8 ? 'filter-limited__item' : '' }}" for="{{ $id }}"
                               data-filter-label="{{ strtolower($location) }}">
                            <input onchange="updateLocation()" type="checkbox" class="property-filter location-filter"
                                   id="{{ $id }}" value="{{ $location }}">
                            <span class="custom-check-box"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            <span class="custom-check-text">{{ $location }}</span>
                        </label>
                    @endforeach
                </div>
                @if(count($locations) > 8)
                    <button type="button" class="filter-more-btn" data-toggle-more="locationList">Show more</button>
                @endif
            </div>
        </details>

        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Listing type</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="property-type" id="listingTypeFilter">
                    <input onchange="updateListingType()" type="checkbox" class="property-filter" id="sale" value="Sale">
                    <label for="sale" class="property-btn">Sale</label>
                    <input onchange="updateListingType()" type="checkbox" class="property-filter" id="rent" value="Rent">
                    <label for="rent" class="property-btn">Rent</label>
                </div>
            </div>
        </details>

        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Property type</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="property-type" id="propertyTypeFilter">
                    <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="apartment" value="apartment">
                    <label for="apartment" class="property-btn">Apartments</label>
                    <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="plot" value="Plot">
                    <label for="plot" class="property-btn">Plots</label>
                </div>
            </div>
        </details>

        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Configuration</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="property-type" id="configurationFilter">
                    @foreach ($configurations as $index => $configuration)
                        <input onchange="updateConfiguration()" type="checkbox"
                               class="property-filter configuration-filter" id="configuration-{{ $index }}"
                               value="{{ $configuration }}">
                        <label for="configuration-{{ $index }}" class="property-btn">{{ str_replace('_', ' ', strtoupper($configuration)) }}</label>
                    @endforeach
                </div>
            </div>
        </details>

        <details class="filter-section">
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Construction status</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="filter-checkbox-list" id="constructionStatusFilter">
                    @foreach($constructionStatuses as $index => $status)
                        @php $id = 'status-' . $index; @endphp
                        <label class="filter-custom-checkbox" for="{{ $id }}">
                            <input onchange="updateConstructionStatus()" type="checkbox"
                                   class="property-filter construction-status-filter" id="{{ $id }}"
                                   value="{{ $status }}">
                            <span class="custom-check-box"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            <span class="custom-check-text">{{ str_replace('_', ' ', ucwords($status)) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </details>

        <details class="filter-section">
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Furnishing</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="filter-checkbox-list" id="furnishingTypeFilter">
                    @foreach($furnishingTypes as $index => $type)
                        @php $id = 'furnishing-' . $index; @endphp
                        <label class="filter-custom-checkbox" for="{{ $id }}">
                            <input onchange="updateFurnishingType()" type="checkbox"
                                   class="property-filter furnishing-type-filter" id="{{ $id }}"
                                   value="{{ $type }}">
                            <span class="custom-check-box"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            <span class="custom-check-text">{{ str_replace('_', ' ', ucwords($type)) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </details>

        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Budget</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div id="resetAmount">
                    <div class="budget-display">
                        <span id="amount-min"></span>
                        <span class="budget-sep">–</span>
                        <span id="amount-max"></span>
                    </div>
                    <div id="slider-range"></div>
                </div>
            </div>
        </details>
    </div>
</div>
