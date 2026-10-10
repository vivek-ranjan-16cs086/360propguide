@extends('frontend.layouts.app')

@section('title', isset($title) && $title ? $title : 'Properties for Sale in Noida & Greater Noida | 360 PropGuide')

@section('description', isset($description) && $description ? $description : 'Browse 100+ verified properties in Noida, Greater Noida West & Ghaziabad. 2, 3 & 4 BHK apartments for sale. Talk to experts — call +91 9643-020-020.')

@section('keywords', isset($keywords) && $keywords ? $keywords : 'properties, flats, apartments, real estate')

@section('canonical', url()->current())

@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/libraries/nouislider.min.css')}}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ asset('frontend/css/listing.css') }}?v={{ @filemtime(public_path('frontend/css/listing.css')) ?: time() }}">
<link rel="stylesheet" href="{{ asset('frontend/css/properties-listing.css') }}?v={{ @filemtime(public_path('frontend/css/properties-listing.css')) ?: time() }}">
<link rel="stylesheet" href="{{ asset('frontend/css/properties-v2.css') }}?v={{ @filemtime(public_path('frontend/css/properties-v2.css')) ?: time() }}">
<style>
/* ===== Card layout: image | details | price panel (screenshot wala design) ===== */
#property-list:not(.pl-grid) .pl-card { grid-template-columns: 290px minmax(0, 1fr) 240px; grid-template-areas: "media body buy"; }
#property-list:not(.pl-grid) .pl-media { min-height: 230px; }
#property-list:not(.pl-grid) .pl-buy {
  grid-column: auto; flex-direction: column; align-items: stretch; flex-wrap: nowrap; justify-content: space-between; gap: 16px;
  padding: 20px; border-left: 1px solid var(--line); border-top: 0; background: #fafbfc;
}
#property-list:not(.pl-grid) .pl-buy > div:first-child { display: block; }
#property-list:not(.pl-grid) .pl-price { font-size: 1.5rem; }
#property-list:not(.pl-grid) .pl-cta { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; width: auto; margin-left: 0; }
#property-list:not(.pl-grid) .pl-ghost { grid-column: 1 / -1; order: -1; flex: none; padding: 10px 8px; }

/* specs strip: upar-neeche patli line */
.pl-specs { position: relative; padding: 4px 0; }
.pl-specs::before, .pl-specs::after { content: ""; position: absolute; left: 14px; right: 0; border-top: 1px solid var(--line); }
.pl-specs::before { top: 0; }
.pl-specs::after { bottom: 0; }
.pl-grid .pl-specs::before, .pl-grid .pl-specs::after { left: 10px; }

/* tablet: price panel neeche ek row me */
@media (max-width: 1199.98px) {
  #property-list:not(.pl-grid) .pl-card { grid-template-columns: 260px minmax(0, 1fr); grid-template-areas: "media body" "buy buy"; }
  #property-list:not(.pl-grid) .pl-buy { flex-direction: row; align-items: center; flex-wrap: wrap; border-left: 0; border-top: 1px solid var(--line); }
  #property-list:not(.pl-grid) .pl-buy > div:first-child { display: flex; align-items: baseline; gap: 6px 14px; flex-wrap: wrap; }
  #property-list:not(.pl-grid) .pl-cta { display: flex; margin-left: auto; }
  #property-list:not(.pl-grid) .pl-ghost { order: 0; grid-column: auto; padding: 10px 18px; }
}
/* mobile: sab ek ke neeche */
@media (max-width: 767.98px) {
  #property-list:not(.pl-grid) .pl-card { grid-template-columns: 1fr; grid-template-areas: "media" "body" "buy"; }
  #property-list:not(.pl-grid) .pl-media { min-height: 0; aspect-ratio: 16 / 10; }
  #property-list:not(.pl-grid) .pl-buy { flex-direction: column; align-items: stretch; padding: 16px; }
  #property-list:not(.pl-grid) .pl-buy > div:first-child { display: block; }
  #property-list:not(.pl-grid) .pl-cta { display: grid; grid-template-columns: 1fr 1fr; margin-left: 0; }
  #property-list:not(.pl-grid) .pl-ghost { grid-column: 1 / -1; order: -1; padding: 10px 8px; }
}
</style>
@endSection

@section('content')
@php
    /* DB column names — apne table ke hisaab se yahan edit karo (JS me PL_KEYS me bhi same rakhna) */
    $KEYS = [
        'area'       => ['carpet_area', 'super_area', 'builtup_area', 'area'],
        'builder'    => ['builder', 'builder_name', 'developer'],
        'possession' => ['possession_date', 'possession'],
        'rera'       => ['rera_number', 'rera_no', 'rera'],
        'baths'      => ['bathrooms', 'bathroom'],
        'floor'      => ['floor', 'floor_no'],
    ];
    $fp = function ($v) {
        $v = (float) $v;
        if ($v >= 1e7) return '₹' . rtrim(rtrim(number_format($v / 1e7, 2), '0'), '.') . ' Cr';
        if ($v >= 1e5) return '₹' . rtrim(rtrim(number_format($v / 1e5, 2), '0'), '.') . ' Lakh';
        return '₹' . number_format($v);
    };
    $emiOf = function ($price) {
        $p = $price * 0.8; $r = 0.085 / 12; $n = 240;
        return $p > 0 ? $p * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1) : 0;
    };
@endphp
<div class="pl" id="plRoot">

    {{-- ===== Compact hero ===== --}}
    <div class="pl-crumb container"><a href="{{ url('/') }}">Home</a><span>›</span><b>Properties</b></div>
    <section class="pl-hero">
        <div class="container pl-hero-in">
            <div class="pl-hero-txt">
                <h1 class="pl-h1" id="results-title">{{ isset($dynamicTitle) ? $dynamicTitle : 'All Properties' }}</h1>
                <span class="pl-count" id="results-count">{{ isset($totalResults) ? $totalResults : ($properties->total() ?? 0) }} Results</span>
            </div>
            <div class="pl-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search-input" name="search" placeholder="Search project, locality or builder" autocomplete="off">
            </div>
        </div>
    </section>

    {{-- ===== Quick filters + sort ===== --}}
    <div class="container pl-bar">
        <div class="pl-bar-in">
            <div class="pl-sorts">
                <span class="pl-bar-label">Sort by</span>
                <button type="button" class="pl-chip sort-link is-on" data-filter="">Relevance</button>
                <button type="button" class="pl-chip sort-link" data-filter="LowToHigh">Price: Low to High</button>
                <button type="button" class="pl-chip sort-link" data-filter="HighToLow">Price: High to Low</button>
                <button type="button" class="pl-chip sort-link" data-filter="NewestFirst">Newest First</button>
            </div>
            <div class="pl-view">
                <button type="button" class="pl-vbtn is-on" data-view="list" aria-label="List view"><i class="fa-solid fa-list"></i></button>
                <button type="button" class="pl-vbtn" data-view="grid" aria-label="Grid view"><i class="fa-solid fa-table-cells-large"></i></button>
            </div>
            <button type="button" class="pl-saved" id="plSavedPill" hidden data-bs-toggle="modal" data-bs-target="#contactModalPopup">
                <i class="fa-solid fa-heart"></i> <span id="plSavedCount">0</span> shortlisted · Enquire
            </button>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-3 mb-5 d-none d-lg-block">
                <aside class="filters-sidebar" id="propertyFilters">
                    <div id="desktopFilterContent">
                        @include('frontend.partials._property-filters')
                    </div>
                </aside>
            </div>

            <div class="col-lg-9 ps-lg-4">
                <div class="pl-active" id="plActive" style="display:none"></div>

                @if(isset($links_description) && $links_description)
                    <div class="pl-about" id="plAbout">
                        <div class="pl-clip">{!! $links_description !!}</div>
                        <button type="button" id="plAboutBtn">Read more</button>
                    </div>
                @endif

                <div id="property-list">
                    @if(isset($properties) && $properties->count() > 0)
                        @foreach($properties as $property)
                            @php
                                $pick = function (...$ks) use ($property) {
                                    foreach ($ks as $k) { $v = $property->{$k} ?? null; if ($v !== null && $v !== '') return $v; }
                                    return null;
                                };
                                $url = route('property.details', $property->slug);
                                $imgs = array_slice(array_values((array) ($property->galleries ?? [])), 0, 6);
                                $total = count((array) ($property->galleries ?? []));
                                $isRent = strtolower($property->listing_type) === 'rent';
                                $rawSt = (string) $property->construction_status;
                                $ready = (bool) preg_match('/ready|complet/i', $rawSt);
                                $stLabel = $ready ? 'Ready to Move' : str_replace('_', ' ', ucfirst($rawSt));
                                $area = $pick(...$KEYS['area']);
                                $areaTxt = $area ? (is_numeric($area) ? number_format((float) $area) . ' sq ft' : $area) : null;
                                $builder = $pick(...$KEYS['builder']);
                                $rera = $pick(...$KEYS['rera']);
                                $baths = $pick(...$KEYS['baths']);
                                $floor = $pick(...$KEYS['floor']);
                                $poss = $pick(...$KEYS['possession']);
                                if ($poss) { try { $poss = \Carbon\Carbon::parse($poss)->format('M Y'); } catch (\Throwable $e) {} }
                                $psf = ($area && is_numeric($area) && $area > 0 && !$isRent) ? round($property->total_price / $area) : null;
                                $emi = (!$isRent && $property->total_price > 0) ? round($emiOf($property->total_price)) : null;
                                $wa = 'https://wa.me/919643020020?text=' . rawurlencode('Hi, I am interested in ' . $property->title . ' - ' . $url);
                                $ago = (optional($property->created_at)->diffInDays(now()) ?? 9999) <= 90 ? optional($property->created_at)->diffForHumans() : null;
                                $isNew = (optional($property->created_at)->diffInDays(now()) ?? 9999) <= 14;
                                $desc = $pick('short_description', 'description', 'overview');
                                $desc = $desc ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $desc))), 140) : null;
                                $amRaw = $pick('amenities', 'highlights');
                                if (is_string($amRaw)) { $j = json_decode($amRaw, true); $amRaw = is_array($j) ? $j : explode(',', $amRaw); }
                                if ($amRaw instanceof \Illuminate\Support\Collection) $amRaw = $amRaw->all();
                                $amen = collect(is_array($amRaw) ? $amRaw : [])
                                    ->map(fn($x) => is_array($x) ? ($x['name'] ?? $x['title'] ?? '') : (string) $x)
                                    ->map(fn($x) => ucwords(trim(str_replace('_', ' ', $x))))->filter(fn($x) => $x !== '' && !is_numeric($x))->values()->all();
                                $slides = count($imgs) + ($total > count($imgs) ? 1 : 0);
                                $specs = [];
                                if ($property->configuration) $specs[] = ['Configuration', str_ireplace('bhk', 'BHK', str_replace('_', ' ', ucfirst($property->configuration)))];
                                if ($areaTxt) $specs[] = ['Area', $areaTxt];
                                if ($property->furnishing_types) $specs[] = ['Furnishing', str_replace('_', ' ', ucfirst($property->furnishing_types))];
                                if ($baths) $specs[] = ['Bathrooms', $baths];
                                if ($floor) $specs[] = ['Floor', $floor];
                            @endphp
                            <article class="pl-card" data-key="{{ $property->slug }}">
                                <div class="pl-media">
                                    <div class="pl-track">
                                        @forelse($imgs as $i => $g)
                                            <a href="{{ $url }}" aria-label="{{ $property->title }}"><img loading="{{ $i ? 'lazy' : 'eager' }}" src="/storage/{{ $g }}" alt="{{ $property->title }}{{ $i ? ' - photo ' . ($i + 1) : '' }}" onerror="this.onerror=null;this.src='/images/no-image.jpg'"></a>
                                        @empty
                                            <a href="{{ $url }}"><img src="/images/no-image.jpg" alt="{{ $property->title }}"></a>
                                        @endforelse
                                        @if($total > count($imgs))<a class="pl-moreph" href="{{ $url }}"><i class="fa-regular fa-images"></i> View all {{ $total }} photos</a>@endif
                                    </div>
                                    @if($slides > 1)
                                        <button type="button" class="pl-nav prev" aria-label="Previous photo"><i class="fa-solid fa-chevron-left"></i></button>
                                        <button type="button" class="pl-nav next" aria-label="Next photo"><i class="fa-solid fa-chevron-right"></i></button>
                                        <span class="pl-pics"><i class="fa-regular fa-images"></i> <em>1</em>/{{ $slides }}</span>
                                    @endif
                                    @if($property->listing_type)<span class="pl-tag">For {{ str_replace('_', ' ', ucfirst($property->listing_type)) }}</span>@endif
                                    @if($isNew)<span class="pl-new">New</span>@endif
                                    <button type="button" class="pl-save" aria-label="Shortlist" data-save="{{ $property->slug }}"><i class="fa-regular fa-heart"></i></button>
                                </div>

                                <div class="pl-body">
                                    <div class="pl-top">
                                        <span class="pl-type">{{ str_replace('_', ' ', ucfirst($property->property_type)) }}@if($ago) · Listed {{ $ago }}@endif</span>
                                        <button type="button" class="pl-share" data-url="{{ $url }}" data-title="{{ $property->title }}"><i class="fa-solid fa-share-nodes"></i> Share</button>
                                    </div>
                                    <div>
                                        <h2 class="pl-title"><a href="{{ $url }}">{{ $property->title }}</a></h2>
                                        @if($builder)<p class="pl-by">by <b>{{ $builder }}</b></p>@endif
                                        <p class="pl-loc"><i class="fa-solid fa-location-dot"></i>{{ $property->city }}</p>
                                    </div>
                                    @if(count($specs))
                                        <div class="pl-specs">@foreach($specs as [$lb, $vl])<div><small>{{ $lb }}</small><b>{{ $vl }}</b></div>@endforeach</div>
                                    @endif
                                    @if($desc)<p class="pl-desc">{{ $desc }}</p>@endif
                                    @if(count($amen))
                                        <div class="pl-amen">@foreach(array_slice($amen, 0, 4) as $am)<span>{{ $am }}</span>@endforeach @if(count($amen) > 4)<span class="more">+{{ count($amen) - 4 }} more</span>@endif</div>
                                    @endif
                                    <div class="pl-meta">
                                        @if($rawSt)<span class="pl-pill {{ $ready ? 'ok' : 'warn' }}"><i class="fa-solid {{ $ready ? 'fa-key' : 'fa-helmet-safety' }}"></i>{{ $stLabel }}{{ (!$ready && $poss) ? ' · Possession ' . $poss : '' }}</span>@endif
                                        @if($rera)<span class="pl-pill rera"><i class="fa-solid fa-shield-halved"></i>RERA</span>@endif
                                        
                                    </div>
                                </div>

                                <div class="pl-buy">
                                    <div>
                                        <div class="pl-price">{{ $fp($property->total_price) }}</div>
                                        <div class="pl-sub">{{ $isRent ? 'per month' : 'total price' }}</div>
                                        @if($psf)<div class="pl-psf">₹{{ number_format($psf) }} / sq ft</div>@endif
                                        @if($emi)<div class="pl-emi" title="80% loan, 8.5% interest, 20 years (approx.)">EMI ~ <b>₹{{ number_format($emi) }}</b>/mo*</div>@endif
                                        @if($property->price_details)<a class="pl-more" href="{{ $url }}">+ See other charges</a>@endif
                                    </div>
                                    <div class="pl-cta">
                                        <a href="tel:+919643020020" class="pl-btn pl-call"><i class="fa-solid fa-phone"></i> Call</a>
                                        <a href="{{ $wa }}" target="_blank" rel="noopener" class="pl-btn pl-wa"><i class="fa-brands fa-whatsapp"></i> Chat</a>
                                        <button type="button" class="pl-btn pl-ghost check-availability" data-title="{{ $property->title }}" data-bs-toggle="modal" data-bs-target="#contactModalPopup"><i class="fa-solid fa-calendar-check"></i> Check availability</button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @else
                        <div class="pl-empty">
                            <i class="fa-solid fa-house-circle-xmark"></i>
                            <h4>No properties found</h4>
                            <p class="text-muted">Try removing a few filters.</p>
                        </div>
                    @endif
                </div>

                <div class="text-center my-4">
                    <button type="button" class="pl-more-btn" id="plMore" hidden>Show more properties</button>
                    <p class="pl-end" id="plEnd" hidden>You've viewed all properties.</p>
                </div>
                @if(isset($properties) && method_exists($properties, 'hasMorePages') && $properties->hasMorePages())
                    <noscript><a class="pl-seo" href="{{ $properties->nextPageUrl() }}">Next page</a></noscript>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Mobile dock ===== --}}
    <div class="pl-dock d-lg-none">
        <button type="button" id="filterToggle"><i class="fa-solid fa-sliders"></i> Filters</button>
        <select id="filter" aria-label="Sort">
            <option value="">Sort: Relevance</option>
            <option value="LowToHigh">Price: Low to High</option>
            <option value="HighToLow">Price: High to Low</option>
            <option value="NewestFirst">Newest First</option>
        </select>
    </div>
</div>

<!-- Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilter">
    <div class="offcanvas-header d-flex justify-content-between align-items-center">
        <h5 class="offcanvas-title">Filters</h5>
        <button type="button" class="btn p-0 border-0 bg-transparent fw-medium" data-bs-dismiss="offcanvas">Cancel</button>
    </div>
    <div class="offcanvas-body" id="mobileFilterContent"></div>
    <div class="offcanvas-footer border-top p-3 d-flex justify-content-end">
        <button type="button" class="btn p-2 rounded border-0 fw-medium" data-bs-dismiss="offcanvas">Show All Results</button>
    </div>
</div>
@endSection

@section('customJS')
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
    var filtersData = {};
    var plState = {
        cur: {{ isset($properties) && method_exists($properties, 'currentPage') ? (int) $properties->currentPage() : 1 }},
        last: {{ isset($properties) && method_exists($properties, 'lastPage') ? (int) $properties->lastPage() : 1 }}
    };
    /* DB keys — Blade ke $KEYS jaise hi rakho */
    var PL_KEYS = {
        area: ['carpet_area', 'super_area', 'builtup_area', 'area'],
        builder: ['builder', 'builder_name', 'developer'],
        possession: ['possession_date', 'possession'],
        rera: ['rera_number', 'rera_no', 'rera'],
        baths: ['bathrooms', 'bathroom'],
        floor: ['floor', 'floor_no']
    };

    /* ---------- helpers ---------- */
    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (m) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
        });
    }
    function formatText(t) {
        if (!t) return '';
        return String(t).replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); }).replace(/bhk/ig, 'BHK');
    }
    function formatPrice(v) {
        v = Number(v) || 0;
        if (v >= 10000000) return '₹' + (v / 10000000).toFixed(2).replace(/\.?0+$/, '') + ' Cr';
        if (v >= 100000) return '₹' + (v / 100000).toFixed(2).replace(/\.?0+$/, '') + ' Lakh';
        return '₹' + v.toLocaleString('en-IN');
    }
    function pick(p, keys) {
        for (var i = 0; i < keys.length; i++) { var v = p[keys[i]]; if (v !== undefined && v !== null && v !== '') return v; }
        return null;
    }
    function plEmi(price) {
        var P = price * 0.8, r = 0.085 / 12, n = 240;
        return P > 0 ? P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1) : 0;
    }

    /* ---------- shortlist ---------- */
    function plGetSaved() { try { return JSON.parse(localStorage.getItem('pl_saved') || '[]'); } catch (e) { return []; } }
    function plSetSaved(a) { try { localStorage.setItem('pl_saved', JSON.stringify(a)); } catch (e) {} }
    function plSyncSaved() {
        var saved = plGetSaved();
        $('.pl-save').each(function () {
            var on = saved.indexOf(String($(this).data('save'))) > -1;
            $(this).toggleClass('is-saved', on).find('i').attr('class', on ? 'fa-solid fa-heart' : 'fa-regular fa-heart');
        });
        $('#plSavedCount').text(saved.length);
        $('#plSavedPill').prop('hidden', saved.length === 0);
    }
    $(document).on('click', '.pl-save', function (e) {
        e.preventDefault();
        var k = String($(this).data('save')), s = plGetSaved(), i = s.indexOf(k);
        if (i > -1) s.splice(i, 1); else s.push(k);
        plSetSaved(s); plSyncSaved();
    });

    /* ---------- image slider arrows ---------- */
    $(document).on('click', '.pl-nav', function (e) {
        e.preventDefault();
        var t = $(this).siblings('.pl-track')[0];
        t.scrollBy({left: ($(this).hasClass('next') ? 1 : -1) * t.clientWidth, behavior: 'smooth'});
    });

    /* ---------- reveal animation ---------- */
    var plIO = ('IntersectionObserver' in window) ? new IntersectionObserver(function (en) {
        en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('in'); plIO.unobserve(x.target); } });
    }, {threshold: .08}) : null;
    function plReveal() {
        $('#property-list .pl-card:not(.in)').each(function (i) {
            this.style.transitionDelay = Math.min(i % 6, 5) * 70 + 'ms';
            if (plIO) plIO.observe(this); else this.classList.add('in');
        });
        plSyncSaved();
    }

    /* ---------- card template (AJAX) ---------- */
    function plAgo(d) {
        var t = new Date(d); if (isNaN(t)) return '';
        var s = (Date.now() - t) / 1000, m = [[31536000, 'year'], [2592000, 'month'], [604800, 'week'], [86400, 'day'], [3600, 'hour'], [60, 'minute']];
        for (var i = 0; i < m.length; i++) if (s >= m[i][0]) { var n = Math.floor(s / m[i][0]); return n + ' ' + m[i][1] + (n > 1 ? 's' : '') + ' ago'; }
        return 'just now';
    }
    function plList(v) {
        if (!v) return [];
        if (typeof v === 'string') { try { var j = JSON.parse(v); v = Array.isArray(j) ? j : v.split(','); } catch (e) { v = v.split(','); } }
        if (!Array.isArray(v)) return [];
        return v.map(function (x) { return (x && typeof x === 'object') ? (x.name || x.title || '') : String(x); })
            .map(function (x) { return $.trim(formatText(x)); }).filter(function (x) { return x && isNaN(x); });
    }
    function plCard(p) {
        var g = Array.isArray(p.galleries) ? p.galleries : [];
        var imgs = g.slice(0, 6), slides = imgs.length + (g.length > imgs.length ? 1 : 0);
        var url = "{{ url('properties') }}/" + p.slug;
        var title = escapeHtml(p.title);
        var rent = String(p.listing_type).toLowerCase() === 'rent';
        var raw = String(p.construction_status || '');
        var ready = /ready|complet/i.test(raw);
        var stLabel = ready ? 'Ready to Move' : formatText(raw);
        var area = pick(p, PL_KEYS.area), builder = pick(p, PL_KEYS.builder), rera = pick(p, PL_KEYS.rera);
        var baths = pick(p, PL_KEYS.baths), floor = pick(p, PL_KEYS.floor), poss = pick(p, PL_KEYS.possession);
        if (poss) { var d = new Date(poss); if (!isNaN(d)) poss = d.toLocaleDateString('en-IN', {month: 'short', year: 'numeric'}); }
        var areaTxt = area ? (isNaN(area) ? area : Number(area).toLocaleString('en-IN') + ' sq ft') : null;
        var psf = (area && !isNaN(area) && Number(area) > 0 && !rent) ? Math.round(p.total_price / area) : null;
        var emi = (!rent && p.total_price > 0) ? Math.round(plEmi(p.total_price)) : null;
        var wa = 'https://wa.me/919643020020?text=' + encodeURIComponent('Hi, I am interested in ' + p.title + ' - ' + url);
        var isNew = p.created_at && (Date.now() - new Date(p.created_at)) < 14 * 864e5;
        var ago = (p.created_at && (Date.now() - new Date(p.created_at)) < 90 * 864e5) ? plAgo(p.created_at) : '';
        var desc = pick(p, ['short_description', 'description', 'overview']);
        desc = desc ? String(desc).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim() : '';
        if (desc.length > 140) desc = desc.slice(0, 139) + '…';
        var amen = plList(pick(p, ['amenities', 'highlights']));
        var specs = [['Configuration', p.configuration ? formatText(p.configuration) : null], ['Area', areaTxt],
                     ['Furnishing', p.furnishing_types ? formatText(p.furnishing_types) : null], ['Bathrooms', baths], ['Floor', floor]]
            .filter(function (x) { return x[1]; })
            .map(function (x) { return '<div><small>' + x[0] + '</small><b>' + escapeHtml(x[1]) + '</b></div>'; }).join('');
        var track = imgs.length ? imgs.map(function (x, i) {
            return '<a href="' + url + '" aria-label="' + title + '"><img loading="' + (i ? 'lazy' : 'eager') + '" src="/storage/' + x + '" alt="' + title + '" onerror="this.onerror=null;this.src=\'/images/no-image.jpg\'"></a>';
        }).join('') : '<a href="' + url + '"><img src="/images/no-image.jpg" alt="' + title + '"></a>';
        if (g.length > imgs.length) track += '<a class="pl-moreph" href="' + url + '"><i class="fa-regular fa-images"></i> View all ' + g.length + ' photos</a>';
        return `
<article class="pl-card" data-key="${escapeHtml(p.slug)}">
  <div class="pl-media">
    <div class="pl-track">${track}</div>
    ${slides > 1 ? '<button type="button" class="pl-nav prev" aria-label="Previous photo"><i class="fa-solid fa-chevron-left"></i></button><button type="button" class="pl-nav next" aria-label="Next photo"><i class="fa-solid fa-chevron-right"></i></button><span class="pl-pics"><i class="fa-regular fa-images"></i> <em>1</em>/' + slides + '</span>' : ''}
    ${p.listing_type ? '<span class="pl-tag">For ' + escapeHtml(formatText(p.listing_type)) + '</span>' : ''}
    ${isNew ? '<span class="pl-new">New</span>' : ''}
    <button type="button" class="pl-save" aria-label="Shortlist" data-save="${escapeHtml(p.slug)}"><i class="fa-regular fa-heart"></i></button>
  </div>
  <div class="pl-body">
    <div class="pl-top">
      <span class="pl-type">${escapeHtml(formatText(p.property_type))}${ago ? ' · Listed ' + ago : ''}</span>
      <button type="button" class="pl-share" data-url="${url}" data-title="${title}"><i class="fa-solid fa-share-nodes"></i> Share</button>
    </div>
    <div>
      <h2 class="pl-title"><a href="${url}">${title}</a></h2>
      ${builder ? '<p class="pl-by">by <b>' + escapeHtml(builder) + '</b></p>' : ''}
      <p class="pl-loc"><i class="fa-solid fa-location-dot"></i>${escapeHtml(p.city)}</p>
    </div>
    ${specs ? '<div class="pl-specs">' + specs + '</div>' : ''}
    ${desc ? '<p class="pl-desc">' + escapeHtml(desc) + '</p>' : ''}
    ${amen.length ? '<div class="pl-amen">' + amen.slice(0, 4).map(function (a) { return '<span>' + escapeHtml(a) + '</span>'; }).join('') + (amen.length > 4 ? '<span class="more">+' + (amen.length - 4) + ' more</span>' : '') + '</div>' : ''}
    <div class="pl-meta">
      ${raw ? '<span class="pl-pill ' + (ready ? 'ok' : 'warn') + '"><i class="fa-solid ' + (ready ? 'fa-key' : 'fa-helmet-safety') + '"></i>' + escapeHtml(stLabel) + ((!ready && poss) ? ' · Possession ' + escapeHtml(poss) : '') + '</span>' : ''}
      ${rera ? '<span class="pl-pill rera"><i class="fa-solid fa-shield-halved"></i>RERA</span>' : ''}
      
    </div>
  </div>
  <div class="pl-buy">
    <div>
      <div class="pl-price">${formatPrice(p.total_price)}</div>
      <div class="pl-sub">${rent ? 'per month' : 'total price'}</div>
      ${psf ? '<div class="pl-psf">₹' + psf.toLocaleString('en-IN') + ' / sq ft</div>' : ''}
      ${emi ? '<div class="pl-emi" title="80% loan, 8.5% interest, 20 years (approx.)">EMI ~ <b>₹' + emi.toLocaleString('en-IN') + '</b>/mo*</div>' : ''}
      ${p.price_details ? '<a class="pl-more" href="' + url + '">+ See other charges</a>' : ''}
    </div>
    <div class="pl-cta">
      <a href="tel:+919643020020" class="pl-btn pl-call"><i class="fa-solid fa-phone"></i> Call</a>
      <a href="${wa}" target="_blank" rel="noopener" class="pl-btn pl-wa"><i class="fa-brands fa-whatsapp"></i> Chat</a>
      <button type="button" class="pl-btn pl-ghost check-availability" data-title="${title}" data-bs-toggle="modal" data-bs-target="#contactModalPopup"><i class="fa-solid fa-calendar-check"></i> Check availability</button>
    </div>
  </div>
</article>`;
    }

    function plSkeleton(n) {
        var s = '';
        for (var i = 0; i < n; i++) s += '<article class="pl-card pl-skel in"><div class="pl-media"></div><div class="pl-body"><i></i><i></i><i></i></div><div class="pl-buy"><i></i></div></article>';
        return s;
    }
    function plEmpty() {
        return '<div class="pl-empty"><i class="fa-solid fa-house-circle-xmark"></i><h4>No properties found</h4><p class="text-muted">Try removing a few filters.</p><button type="button" class="pl-chip pl-reset-btn">Reset filters</button></div>';
    }
    $(document).on('click', '.pl-reset-btn, .pl-clear', function () { $('#resetFilters').trigger('click'); });

    function plMoreUI() {
        $('#plMore').prop('hidden', plState.cur >= plState.last).prop('disabled', false).text('Show more properties');
        $('#plEnd').prop('hidden', plState.last < 2 || plState.cur < plState.last);
    }
    $('#plMore').on('click', function () {
        $(this).prop('disabled', true).text('Loading…');
        filtersData['pageId'] = plState.cur + 1;
        applyFilters(filtersData, true);
    });

    /* ---------- active filter chips + quick chip sync ---------- */
    function plChips() {
        var h = '', $c = $('#filter-form input[type=checkbox]:checked');
        $c.each(function (i) {
            var id = this.id, lab = (id ? $('label[for="' + id + '"]').text() : '') || $(this).closest('label').text() || this.value;
            lab = $.trim(lab).replace(/\s+/g, ' ');
            h += '<button type="button" class="pl-fchip" data-idx="' + i + '">' + escapeHtml(lab) + ' <i class="fa-solid fa-xmark"></i></button>';
        });
        var q = $('#search-input').val().trim();
        if (q) h += '<button type="button" class="pl-fchip" data-search="1">“' + escapeHtml(q) + '” <i class="fa-solid fa-xmark"></i></button>';
        if (h) h += '<button type="button" class="pl-clear">Clear all</button>';
        $('#plActive').html(h).toggle(!!h);
    }
    $(document).on('click', '.pl-fchip', function () {
        if ($(this).data('search')) { $('#search-input').val('').trigger('change'); return; }
        $('#filter-form input[type=checkbox]:checked').eq($(this).data('idx')).prop('checked', false).trigger('change');
    });
    /* ---------- list / grid view ---------- */
    function plView(v) {
        $('#property-list').toggleClass('pl-grid', v === 'grid');
        $('.pl-vbtn').removeClass('is-on').filter('[data-view="' + v + '"]').addClass('is-on');
        try { localStorage.setItem('pl_view', v); } catch (e) {}
    }
    $(document).on('click', '.pl-vbtn', function () { plView($(this).data('view')); });

    /* ---------- share + photo counter ---------- */
    $(document).on('click', '.pl-share', function (e) {
        e.preventDefault();
        var $b = $(this), d = {title: $b.data('title'), url: $b.data('url')};
        if (navigator.share) { navigator.share(d).catch(function () {}); }
        else if (navigator.clipboard) {
            navigator.clipboard.writeText(d.url).then(function () {
                var h = $b.html(); $b.html('<i class="fa-solid fa-check"></i> Copied');
                setTimeout(function () { $b.html(h); }, 1500);
            });
        }
    });
    document.addEventListener('scroll', function (e) {
        var t = e.target;
        if (!t.classList || !t.classList.contains('pl-track')) return;
        var em = t.parentNode.querySelector('.pl-pics em');
        if (em) em.textContent = Math.round(t.scrollLeft / t.clientWidth) + 1;
    }, true);

    /* ---------- AJAX ---------- */
    function applyFilters(fd, append) {
        var pageNumber = (fd && fd['pageId'] !== undefined) ? fd['pageId'] : 1;
        if (!append) $('#property-list').html(plSkeleton(3));

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            url: "{{ route('property.filters', [], false) }}?page=" + pageNumber,
            method: 'POST',
            data: JSON.stringify({'filters': fd}),
            success: function (response) {
                var items = (response && response.data && Array.isArray(response.data.data)) ? response.data.data : [];
                if (!append) $('#results-title').text((response && response.dynamicTitle) ? response.dynamicTitle : 'All Properties');

                if (!response || response.status === false || items.length === 0) {
                    if (append) { plMoreUI(); return; }
                    $('#property-list').html(plEmpty());
                    $('#results-count').text('0 Results');
                    plState = {cur: 1, last: 1}; plMoreUI(); plChips();
                    return;
                }
                var html = items.map(plCard).join('');
                if (append) $('#property-list').append(html); else $('#property-list').html(html);
                $('#results-count').text((response.totalResults || items.length) + ' Results');
                var pg = response.pagination || {};
                plState = {cur: pg.current_page || 1, last: pg.last_page || 1};
                plMoreUI(); plReveal(); plChips();
            },
            error: function () {
                if (append) { plMoreUI(); return; }
                $('#property-list').html(plEmpty());
                $('#results-count').text('0 Results');
                plState = {cur: 1, last: 1}; plMoreUI(); plChips();
            }
        });
    }

    /* ---------- filter updaters (partial inhe call karta hai) ---------- */
    function plMake(key, box) {
        return function (apply) {
            if (apply === undefined) apply = true;
            filtersData[key] = $(box + ' .property-filter:checked').map(function () { return $(this).val(); }).get();
            filtersData['pageId'] = 1;
            if (apply) applyFilters(filtersData);
        };
    }
    var updateListingType = plMake('listingType', '#listingTypeFilter');
    var updatePropertyType = plMake('propertyType', '#propertyTypeFilter');
    var updateLocation = plMake('location', '#locationFilter');
    var updateConfiguration = plMake('configuration', '#configurationFilter');
    var updateConstructionStatus = plMake('constructionStatus', '#constructionStatusFilter');
    var updateFurnishingType = plMake('furnishingType', '#furnishingTypeFilter');

    function applyQueryParams() {
        var params = new URLSearchParams(window.location.search);
        if (params.has('configuration')) {
            var confs = params.get('configuration').split(',');
            confs.forEach(function (c) { $(".configuration-filter[value='" + c + "']").prop('checked', true); });
            filtersData['configuration'] = confs;
        }
        var locs = params.getAll('location[]').concat(params.getAll('location'))
            .flatMap(function (v) { return String(v).split(','); })
            .map(function (v) { return v.trim(); }).filter(Boolean);
        if (locs.length > 0) {
            locs.forEach(function (l) {
                $('.location-filter').filter(function () { return $(this).val().toLowerCase().trim() === l.toLowerCase().trim(); }).prop('checked', true);
            });
            filtersData['location'] = locs;
        }
        if (params.has('listingType')) {
            var lt = params.get('listingType');
            $('#' + lt.toLowerCase()).prop('checked', true);
            filtersData['listingType'] = [lt];
        }
        if (params.has('propertyType')) {
            var pt = params.get('propertyType');
            $(".property-filter[value='" + pt + "']").prop('checked', true);
            filtersData['propertyType'] = [pt];
        }
        if (params.has('keyword') || params.has('q') || params.has('search')) {
            var kw = (params.get('keyword') || params.get('q') || params.get('search') || '').trim();
            if (kw) { $('#search-input').val(kw); filtersData['search'] = kw; }
        }
        if (Object.keys(filtersData).length > 0) {
            filtersData['pageId'] = 1;
            applyFilters(filtersData);
        }
    }

    /* ---------- DOM ready ---------- */
    $(document).ready(function () {
        $('#plRoot').addClass('pl-js');
        plMoreUI();
        plReveal();
        try { plView(localStorage.getItem('pl_view') || 'list'); } catch (e) {}

        /* filter form: sidebar <-> offcanvas (pehle move, phir filters apply) */
        var filterForm = document.querySelector('#filter-form');
        var mobileC = document.querySelector('#mobileFilterContent');
        var desktopC = document.querySelector('#desktopFilterContent');
        function moveFilters() {
            if (!filterForm || !mobileC || !desktopC) return;
            if (window.innerWidth < 992) { if (!mobileC.contains(filterForm)) mobileC.appendChild(filterForm); }
            else if (!desktopC.contains(filterForm)) desktopC.appendChild(filterForm);
            if (window.jQuery && $('#slider-range').data('ui-slider')) $('#slider-range').slider('refresh');
        }
        moveFilters();
        window.addEventListener('resize', moveFilters);

        /* sidebar: jab tak pura nahi dikhta tab tak page ke saath scroll, bottom dikhte hi fix (sticky) */
      /* sidebar: pehle page ke saath scroll, pura dikhte hi fix */
var plSide = document.getElementById('propertyFilters');
function plStickySidebar() {
    if (!plSide || !plSide.offsetParent) return;
    var TOP = 80, GAP = 20;                  // TOP = site header ki height
    var h = plSide.offsetHeight, vh = window.innerHeight;
    plSide.style.top = (h + TOP + GAP > vh ? vh - h - GAP : TOP) + 'px';
}
plStickySidebar();
window.addEventListener('resize', plStickySidebar);
window.addEventListener('load', plStickySidebar);
if (window.ResizeObserver && plSide) new ResizeObserver(plStickySidebar).observe(plSide);
        var ft = document.getElementById('filterToggle');
        if (ft) ft.addEventListener('click', function () { new bootstrap.Offcanvas(document.getElementById('mobileFilter')).show(); });

        var serverInitialFilters = {!! isset($initialFilters) ? json_encode($initialFilters) : 'null' !!};
        if (serverInitialFilters) {
            if (serverInitialFilters.configuration) {
                serverInitialFilters.configuration.forEach(function (c) { $(".configuration-filter[value='" + c + "']").prop('checked', true); });
                filtersData['configuration'] = serverInitialFilters.configuration;
            }
            if (serverInitialFilters.location) {
                serverInitialFilters.location.forEach(function (l) {
                    $('.location-filter').filter(function () { return $(this).val().toLowerCase().trim() === l.toLowerCase().trim(); }).prop('checked', true);
                });
                filtersData['location'] = serverInitialFilters.location;
            }
            if (serverInitialFilters.propertyType) {
                serverInitialFilters.propertyType.forEach(function (pt) { $(".property-filter[value='" + pt + "']").prop('checked', true); });
                filtersData['propertyType'] = serverInitialFilters.propertyType;
            }
            if (serverInitialFilters.listingType) {
                serverInitialFilters.listingType.forEach(function (lt) {
                    var input = $('#listingTypeFilter .property-filter').filter(function () {
                        return String($(this).val()).toLowerCase() === String(lt).toLowerCase();
                    });
                    input.prop('checked', true);
                    if (input.length) filtersData['listingType'] = (filtersData['listingType'] || []).concat(input.val());
                });
            }
            filtersData['pageId'] = 1;
            applyFilters(filtersData);
        } else {
            applyQueryParams();
            updateListingType(false);
            updatePropertyType(false);
            updateConfiguration(false);
            updateLocation(false);
        }
        plChips();

        $('#plAboutBtn').on('click', function () {
            var box = $('#plAbout').toggleClass('open');
            $(this).text(box.hasClass('open') ? 'Show less' : 'Read more');
        });
    });

    /* ---------- enquiry modal: property ka naam jaye (hidden field: property_name) ---------- */
    document.addEventListener('show.bs.modal', function (e) {
        if (e.target.id !== 'contactModalPopup') return;
        var btn = e.relatedTarget, t = '';
        if (btn && btn.id === 'plSavedPill') t = 'Shortlist: ' + plGetSaved().join(', ');
        else if (btn) t = btn.getAttribute('data-title') || '';
        var $m = $(e.target);
        $m.find('.pl-enq').remove();
        $m.find('input[name=property_name]').remove();
        if (!t) return;
        $m.find('.modal-body').first().prepend('<div class="pl-enq"><i class="fa-solid fa-building"></i> Enquiring for: <b>' + escapeHtml(t) + '</b></div>');
        $m.find('form').first().append($('<input type="hidden" name="property_name">').val(t));
    });

    /* ---------- budget slider ---------- */
    var sliderMin = {{ (int) ($minPrice ?? 0) }};
    var sliderMax = {{ (int) ($maxPrice ?? 0) }};
    if (sliderMax <= sliderMin) sliderMax = sliderMin + 100000;

    $('#slider-range').slider({
        range: true, min: sliderMin, max: sliderMax, values: [sliderMin, sliderMax],
        slide: function (e, ui) {
            $('#amount-min').text(formatPrice(ui.values[0]));
            $('#amount-max').text(formatPrice(ui.values[1]));
            filtersData['budget'] = {'min': ui.values[0], 'max': ui.values[1]};
        },
        stop: function (e, ui) {
            filtersData['budget'] = {'min': ui.values[0], 'max': ui.values[1]};
            filtersData['pageId'] = 1;
            applyFilters(filtersData);
        }
    });
    $('#amount-min').text(formatPrice($('#slider-range').slider('values', 0)));
    $('#amount-max').text(formatPrice($('#slider-range').slider('values', 1)));

    /* ---------- sort ---------- */
    function plSort(val) {
        if (val) filtersData['sorting'] = val; else delete filtersData['sorting'];
        filtersData['pageId'] = 1;
        $('.sort-link').removeClass('is-on').filter('[data-filter="' + val + '"]').addClass('is-on');
        $('#filter').val(val);
        applyFilters(filtersData);
    }
    $('.sort-link').on('click', function () { plSort($(this).attr('data-filter')); });
    $('#filter').on('change', function () { plSort(this.value); });

    /* ---------- filter panel extras ---------- */
    $(document).on('input', '[data-filter-search]', function () {
        var q = $(this).val().trim().toLowerCase();
        var list = document.getElementById($(this).attr('data-filter-search'));
        if (!list) return;
        list.querySelectorAll('[data-filter-label]').forEach(function (row) {
            row.hidden = q !== '' && (row.getAttribute('data-filter-label') || '').indexOf(q) === -1;
        });
    });
    $(document).on('click', '[data-toggle-more]', function () {
        var list = document.getElementById($(this).attr('data-toggle-more'));
        if (!list) return;
        list.classList.toggle('is-expanded');
        $(this).text(list.classList.contains('is-expanded') ? 'Show less' : 'Show more');
    });
    $(document).on('click', '#resetFilters', function (e) {
        e.preventDefault();
        $('#filter-form input[type="checkbox"]').prop('checked', false);
        if ($('#slider-range').data('ui-slider')) {
            var min = $('#slider-range').slider('option', 'min'), max = $('#slider-range').slider('option', 'max');
            $('#slider-range').slider('values', [min, max]);
            $('#amount-min').text(formatPrice(min));
            $('#amount-max').text(formatPrice(max));
        }
        $('#search-input').val('');
        $('.sort-link').removeClass('is-on').filter('[data-filter=""]').addClass('is-on');
        $('#filter').val('');
        filtersData = {pageId: 1};
        applyFilters(filtersData);
        if (window.history && window.history.replaceState) window.history.replaceState({}, '', '{{ url("properties") }}');
    });

    /* ---------- search (debounced) ---------- */
    var propertySearchTimer = null;
    $('#search-input').on('keyup change', function () {
        var v = $(this).val().trim();
        filtersData['pageId'] = 1;
        if (v !== '') filtersData['search'] = v; else delete filtersData['search'];
        clearTimeout(propertySearchTimer);
        propertySearchTimer = setTimeout(function () { applyFilters(filtersData); }, 400);
    });
</script>
@endsection