@php
    $selected = $selected ?? [];
    $possessionOptions = [
        'new_launch' => 'New Launch',
        'under_construction' => 'Under Construction',
        'ready_to_move' => 'Ready To Move',
        'within_a_year' => 'Within A Year',
    ];
    $propertyTypes = ['1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK', '6 BHK', 'Plots', 'Shops', 'Studio Apartments'];
    $selectedLocations = $selected['location'] ?? [];
    $selectedLocalities = $selected['locality'] ?? [];
    $selectedTypes = $selected['type'] ?? [];
    $selectedPossession = $selected['possession'] ?? [];
    $selectedDevelopers = array_map('strval', $selected['developer'] ?? []);
@endphp

<div class="filter-box">
    <div class="filter-header">
        <span class="filter-header__title"><i class="fa-solid fa-sliders" aria-hidden="true"></i> Filters</span>
        <a class="filter-header__reset" href="{{ route('projects') }}">Reset</a>
    </div>

    <div class="filter-accordion">
        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Location</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="filter-checkbox-list">
                    @foreach ($locations as $location)
                        @php $id = 'filter-location-' . \Illuminate\Support\Str::slug($location); @endphp
                        <label class="filter-custom-checkbox" for="{{ $id }}">
                            <input type="checkbox" id="{{ $id }}" name="location[]" value="{{ $location }}"
                                   {{ in_array($location, $selectedLocations, true) ? 'checked' : '' }}>
                            <span class="custom-check-box"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            <span class="custom-check-text">{{ $location }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </details>

        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Locality</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="filter-search-box">
                    <i class="fa-solid fa-magnifying-glass filter-search-icon" aria-hidden="true"></i>
                    <input type="search" class="filter-search-input" data-filter-search="localityList" placeholder="Search locality" autocomplete="off">
                </div>
                <div class="filter-checkbox-list filter-limited" id="localityList">
                    @foreach ($locality as $index => $localityName)
                        @php
                            $id = 'filter-locality-' . \Illuminate\Support\Str::slug($localityName) . '-' . $index;
                            $parentCity = $localityCityMap[$localityName] ?? '';
                        @endphp
                        <label class="filter-custom-checkbox filter-limited__item" for="{{ $id }}"
                               data-city="{{ $parentCity }}"
                               data-filter-label="{{ strtolower($localityName) }}">
                            <input type="checkbox" id="{{ $id }}" name="locality[]" value="{{ $localityName }}"
                                   {{ in_array($localityName, $selectedLocalities, true) ? 'checked' : '' }}>
                            <span class="custom-check-box"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            <span class="custom-check-text">{{ $localityName }}</span>
                        </label>
                    @endforeach
                </div>
                @if(count($locality) > 8)
                    <button type="button" class="filter-more-btn" data-toggle-more="localityList">Show more</button>
                @endif
            </div>
        </details>

        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Property type</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="property-type">
                    @foreach ($propertyTypes as $type)
                        @php $id = 'filter-type-' . \Illuminate\Support\Str::slug($type); @endphp
                        <input class="property-filter" type="checkbox" id="{{ $id }}" name="type[]" value="{{ $type }}"
                               {{ in_array($type, $selectedTypes, true) ? 'checked' : '' }}>
                        <label class="property-btn" for="{{ $id }}">{{ $type }}</label>
                    @endforeach
                </div>
            </div>
        </details>

        <details class="filter-section" open>
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Possession</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="filter-checkbox-list">
                    @foreach ($possessionOptions as $value => $label)
                        @php $id = 'filter-possession-' . $value; @endphp
                        <label class="filter-custom-checkbox" for="{{ $id }}">
                            <input type="checkbox" id="{{ $id }}" name="possession[]" value="{{ $value }}"
                                   {{ in_array($value, $selectedPossession, true) ? 'checked' : '' }}>
                            <span class="custom-check-box"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            <span class="custom-check-text">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </details>

        <details class="filter-section">
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Budget</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="budget-fields">
                    <label>
                        <span>Min (₹)</span>
                        <input type="number" name="min_price" min="0" step="100000"
                               value="{{ $selected['min_price'] ?? '' }}" placeholder="{{ $minPrice }}">
                    </label>
                    <label>
                        <span>Max (₹)</span>
                        <input type="number" name="max_price" min="0" step="100000"
                               value="{{ $selected['max_price'] ?? '' }}" placeholder="{{ $maxPrice }}">
                    </label>
                </div>
                <button type="submit" class="filter-apply-btn">Apply budget</button>
            </div>
        </details>

        <details class="filter-section">
            <summary class="filter-section__trigger">
                <span class="filter-section__title">Developer</span>
                <i class="fa-solid fa-chevron-down filter-section__chevron" aria-hidden="true"></i>
            </summary>
            <div class="filter-section__body">
                <div class="filter-search-box">
                    <i class="fa-solid fa-magnifying-glass filter-search-icon" aria-hidden="true"></i>
                    <input type="search" class="filter-search-input" data-filter-search="developerList" placeholder="Search developer" autocomplete="off">
                </div>
                <div class="filter-checkbox-list filter-limited" id="developerList">
                    @foreach ($developers as $developer)
                        @php $id = 'filter-developer-' . $developer->id; @endphp
                        <label class="filter-custom-checkbox filter-limited__item" for="{{ $id }}"
                               data-filter-label="{{ strtolower($developer->developer_name) }}">
                            <input type="checkbox" id="{{ $id }}" name="developer[]" value="{{ $developer->id }}"
                                   {{ in_array((string) $developer->id, $selectedDevelopers, true) ? 'checked' : '' }}>
                            <span class="custom-check-box"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            <span class="custom-check-text">{{ $developer->developer_name }}</span>
                        </label>
                    @endforeach
                </div>
                @if($developers->count() > 8)
                    <button type="button" class="filter-more-btn" data-toggle-more="developerList">Show more</button>
                @endif
            </div>
        </details>
    </div>
</div>
