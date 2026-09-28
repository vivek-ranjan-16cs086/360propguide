@extends('frontend.layouts.app')

@section('title', 'EPIC by Jindalsons | Commercial Property in Siddharth Vihar')
@section('description', 'Explore EPIC by Jindalsons, a premium retail and dining destination in Siddharth Vihar, Ghaziabad.')
@section('canonical', route('commercial.epic'))
@section('skipPropertiesCss', true)
@section('skipAos', true)

@section('customCSS')
<link rel="stylesheet" href="{{ asset('frontend/css/home.css') }}?v={{ @filemtime(public_path('frontend/css/home.css')) ?: time() }}">
<link rel="stylesheet" href="{{ asset('frontend/css/commercial-details.css') }}?v={{ @filemtime(public_path('frontend/css/commercial-details.css')) ?: time() }}">
@endsection

@php
    $facts = [
        ['value' => '4,000.88', 'unit' => 'sq m', 'label' => 'Plot area · approx. 0.99 acre'],
        ['value' => '75', 'unit' => 'metres', 'label' => 'Project frontage'],
        ['value' => '50', 'unit' => 'metres', 'label' => 'Immediate road width'],
        ['value' => 'May 2031', 'unit' => '', 'label' => 'Declared RERA completion'],
    ];
    $floors = [
        ['name' => 'Lower Ground', 'type' => 'Retail & anchor spaces', 'detail' => 'Large-format anchor space supported by smaller shops.', 'rate' => '₹18,000'],
        ['name' => 'Ground', 'type' => 'Retail & anchor spaces', 'detail' => 'Street-level approach with selected double-height shops.', 'rate' => '₹32,000'],
        ['name' => 'First', 'type' => 'Retail & anchor spaces', 'detail' => 'Anchor and smaller shops around shared circulation.', 'rate' => '₹25,000'],
        ['name' => 'Second', 'type' => 'Retail & anchor spaces', 'detail' => 'Retail positioned near lift and escalator arrivals.', 'rate' => '₹18,000'],
        ['name' => 'Third', 'type' => 'F&B spaces', 'detail' => 'Dining-led layout with shared seating possibilities.', 'rate' => '₹18,000'],
    ];
    $consider = ['A substantial street presence', 'Anchor spaces alongside smaller shops', 'Dining has a distinct place'];
    $examine = ['Compare the full cost of the chosen unit', 'Plan around the delivery horizon', 'A finished shop needs additional investment'];
@endphp

@section('content')
<main class="epic-page">
    <section class="epic-hero" aria-labelledby="epic-title">
        <img class="epic-hero__image" src="{{ asset('assets/images/commercial/epic-gallery-1.jpeg') }}" alt="Artist impression of EPIC by Jindalsons commercial development">
        <div class="epic-hero__shade"></div>
        <div class="epic-shell epic-hero__inner">
            <nav class="epic-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('projects') }}">Projects</a><span>/</span><span>Commercial</span></nav>
            <button class="epic-gallery-link" id="open-epic-gallery" type="button">View 6 project images <span aria-hidden="true">&#8599;</span></button>
            <div class="epic-hero__content">
                <p class="epic-eyebrow epic-eyebrow--light">Siddharth Vihar, Ghaziabad</p>
                <h1 id="epic-title">EPIC</h1>
                <p class="epic-byline">by Jindalsons</p>
                <p class="epic-promise">Retail. Anchors. Dining.</p>
                <p class="epic-intro">A 75-metre frontage on a 50-metre road. Explore the floor, the format and the price that fit your plans.</p>
                <div class="epic-badges"><span>RERA registered</span><span>Retail + dining</span></div>
                <div class="epic-actions">
                    <a class="epic-btn epic-btn--primary" href="#spaces-prices">Compare floor prices <span aria-hidden="true">&#8595;</span></a>
                    <a class="epic-btn epic-btn--text" href="https://wa.me/919643020020?text=I%20need%20help%20choosing%20a%20space%20at%20EPIC" target="_blank" rel="noopener">Help me choose <span aria-hidden="true">&#8599;</span></a>
                </div>
            </div>
            <p class="epic-art-note">Artist impression &middot; Illustrative brands</p>
        </div>
    </section>

    <dialog class="epic-gallery-modal" id="epic-gallery-modal" aria-labelledby="epic-gallery-title">
        <div class="epic-gallery-modal__panel">
            <header><div><p class="epic-eyebrow">EPIC project gallery</p><h2 id="epic-gallery-title">Project perspectives</h2></div><button class="epic-gallery-close" type="button" aria-label="Close project gallery">&times;</button></header>
            <div class="epic-gallery-stage">
                @for($image = 1; $image <= 6; $image++)
                    <img class="epic-gallery-slide {{ $image === 1 ? 'is-active' : '' }}" src="{{ asset('assets/images/commercial/epic-gallery-'.$image.'.jpeg') }}" alt="EPIC by Jindalsons project artist impression {{ $image }}" {{ $image > 1 ? 'loading=lazy' : '' }}>
                @endfor
            </div>
            <div class="epic-gallery-controls">
                <button class="epic-gallery-prev" type="button" aria-label="Show previous project image">&larr; Previous</button>
                <p><span id="epic-gallery-current">1</span> / 6</p>
                <button class="epic-gallery-next" type="button" aria-label="Show next project image">Next &rarr;</button>
            </div>
            <p class="epic-gallery-caption">Artist impression · Illustrative brands, not lease commitments.</p>
        </div>
    </dialog>

    <section class="epic-facts" aria-label="Project facts">
        <div class="epic-shell epic-facts__grid">
            @foreach($facts as $fact)
                <article class="epic-fact">
                    <p><strong>{{ $fact['value'] }}</strong> @if($fact['unit'])<span>{{ $fact['unit'] }}</span>@endif</p>
                    <h2>{{ $fact['label'] }}</h2>
                </article>
            @endforeach
        </div>
    </section>

    <section class="epic-section" id="spaces-prices" aria-labelledby="prices-title">
        <div class="epic-shell">
            <header class="epic-section__head">
                <div><p class="epic-eyebrow">01 / Spaces & prices</p><h2 id="prices-title">Compare floors.<br>Choose with clarity.</h2></div>
                <p>Compare the quoted rate and format, then shortlist the position that supports your business.</p>
            </header>
            <div class="epic-rate-card">
                <div><span>Quoted floor rates</span><strong>&#8377;18,000&ndash;&#8377;32,000</strong><small>per sq ft &middot; before a unit-specific cost sheet</small></div>
                <p>Rates are indicative and vary by floor and unit. Confirm area basis, availability and additional charges before deciding.</p>
            </div>
            <div class="epic-floor-table" role="table" aria-label="EPIC floor price comparison">
                <div class="epic-floor-table__head" role="row"><span>Floor</span><span>Format & position</span><span>Rate / sq ft</span><span>Explore</span></div>
                @foreach($floors as $floor)
                <article class="epic-floor-row" role="row">
                    <div><strong>{{ $floor['name'] }}</strong></div>
                    <div><strong>{{ $floor['type'] }}</strong><p>{{ $floor['detail'] }}</p></div>
                    <div class="epic-price"><strong>{{ $floor['rate'] }}</strong><small>per sq ft</small></div>
                    <div><a href="#epic-plans">View position <span aria-hidden="true">&#8599;</span></a><a class="epic-outline-link" href="https://wa.me/919643020020?text={{ urlencode('I want to discuss the '.$floor['name'].' floor at EPIC') }}" target="_blank" rel="noopener">Ask about this floor</a></div>
                </article>
                @endforeach
            </div>
            <div class="epic-price-foot"><p>Need a total budget? Ask for the selected unit&rsquo;s area basis and all-inclusive cost sheet.</p><a href="https://wa.me/919643020020?text=Please%20share%20an%20EPIC%20unit%20cost%20sheet" target="_blank" rel="noopener">Get a unit cost sheet &#8599;</a></div>
            <p class="epic-source">Indicative floor rates &middot; Last reviewed 15 September 2026</p>
        </div>
    </section>

    <section class="epic-section epic-section--tint" aria-labelledby="view-title">
        <div class="epic-shell">
            <header class="epic-section__head">
                <div><p class="epic-eyebrow">02 / The 360 PropGuide view</p><h2 id="view-title">Choose the position.<br>Understand the trade-off.</h2></div>
                <p>What the project offers, where the differences matter and who it may suit.</p>
            </header>
            <div class="epic-view-summary">
                <p>The decision is between five distinct floor positions&mdash;not merely five prices. Ground commands the highest quoted rate, while Lower Ground, Second and Third share the same headline rate but serve different access and business needs.</p>
                <dl><div><dt>Format</dt><dd>Retail + dining</dd></div><div><dt>Delivery horizon</dt><dd>31 May 2031</dd></div><div><dt>Pricing</dt><dd>5 floor rates</dd></div></dl>
            </div>
            <div class="epic-balance">
                <div><p class="epic-eyebrow epic-eyebrow--green">3 reasons to consider</p>@foreach($consider as $i => $item)<details><summary><span>0{{ $i + 1 }}</span>{{ $item }}</summary><p>{{ ['The 75-metre frontage supports visibility and a strong arrival experience.', 'A varied unit mix can serve both destination brands and everyday retail.', 'A dedicated F&B level can create longer dwell time and an evening economy.'][$i] }}</p></details>@endforeach</div>
                <div><p class="epic-eyebrow epic-eyebrow--gold">3 things to examine</p>@foreach($examine as $i => $item)<details><summary><span>0{{ $i + 1 }}</span>{{ $item }}</summary><p>{{ ['Request the area basis, taxes, maintenance and every additional charge.', 'Factor the declared completion timeline into financing and business plans.', 'Budget separately for fit-out, signage, services and opening inventory.'][$i] }}</p></details>@endforeach</div>
            </div>
            <div class="epic-suit"><div><strong>May suit</strong><p>Buyers comparing commercial floors over a longer horizon and operators planning a future retail or F&B location.</p></div><div><strong>May not suit</strong><p>Buyers who need an immediately operational shop or rental income starting soon.</p></div></div>
            <div class="epic-mini-facts"><div><strong>170+ cars</strong><span>Planned parking</span></div><div><strong>5 lifts</strong><span>Including a service lift in plans</span></div><div><strong>Double height</strong><span>Selected Ground / F&B spaces</span></div><div><strong>Retail + dining</strong><span>Anchor and smaller formats</span></div></div>
        </div>
    </section>

    <section class="epic-section" id="epic-plans" aria-labelledby="plans-title">
        <div class="epic-shell">
            <header class="epic-section__head epic-section__head--inline"><div><p class="epic-eyebrow">03 / Site & floor positions</p><h2 id="plans-title">See where your space sits.</h2></div><a href="https://wa.me/919643020020?text=Please%20share%20the%20filed%20drawing%20set%20for%20EPIC" target="_blank" rel="noopener">Request drawing set &#8599;</a></header>
            <div class="epic-plan-tabs" role="list" aria-label="Floor positions">@foreach($floors as $i => $floor)<button type="button" role="tab" class="{{ $i === 1 ? 'is-active' : '' }}" data-floor="{{ $i }}">{{ $floor['name'] }}{{ $i === 4 ? ' &middot; F&B' : '' }}</button>@endforeach</div>
            <div class="epic-plan-layout">
                <figure><img class="epic-plan-drawing" src="{{ asset('assets/images/commercial/brochure-ground.jpg') }}" alt="Ground-floor plan from the EPIC developer brochure" loading="lazy"><figcaption>Ground floor &middot; Developer brochure drawing &middot; Not live availability</figcaption></figure>
                <div class="epic-plan-copy"><p class="epic-eyebrow" id="floor-label">Ground &middot; &#8377;32,000/sq ft</p><h3 id="floor-heading">Street-level presence, examined properly.</h3><div id="floor-details"><p><strong>50-metre road</strong><br>The main approach creates a visible, direct arrival.</p><p><strong>Anchor and smaller shops</strong><br>Compare frontage with circulation, lift and escalator access.</p><p><strong>Double-height selection</strong><br>Confirm the exact eligible units in the filed drawings.</p></div><a class="epic-btn epic-btn--dark" href="https://wa.me/919643020020?text=I%20want%20to%20discuss%20an%20EPIC%20floor" target="_blank" rel="noopener">Discuss this floor &#8599;</a></div>
            </div>
            <p class="epic-source">Plans illustrate the brochure layout and do not represent live availability. Verify dimensions and unit details against filed drawings.</p>
        </div>
    </section>
    <section class="epic-section epic-section--tint" id="epic-location" aria-labelledby="location-title">
        <div class="epic-shell">
            <header class="epic-section__head">
                <div><p class="epic-eyebrow">04 / Location & demand</p><h2 id="location-title">Siddharth Vihar,<br>Ghaziabad.</h2></div>
                <p>A substantial road frontage, with the choice of shop still depending on access, visibility and customer movement.</p>
            </header>
            <div class="epic-location-layout">
                <div class="epic-map-card">
                    <img src="{{ asset('assets/images/commercial/brochure-location.jpg') }}" alt="Developer location diagram showing EPIC in Siddharth Vihar and surrounding roads" loading="lazy">
                    <p>Developer location diagram · Not to scale · Travel times not implied</p>
                    <a class="epic-outline-link epic-map-link" href="https://www.google.com/maps/search/?api=1&query=Siddharth+Vihar+Ghaziabad+Uttar+Pradesh+201009" target="_blank" rel="noopener">Open map & directions ↗</a>
                    <address>Plot No. 8 COM/2, 3 & 4, Sector 8, Siddharth Vihar, Ghaziabad, Uttar Pradesh 201009</address>
                </div>
                <div class="epic-location-points">
                    <article><h3>Immediate approach</h3><p>The project brochure places EPIC on a 50-metre-wide road and marks the wider 75-metre Link Road separately.</p></article>
                    <article><h3>Surrounding markets</h3><p>Siddharth Vihar sits alongside Indirapuram, Pratap Vihar and Vijay Nagar—surrounding markets to assess, not measured footfall.</p></article>
                    <article><h3>Regional routes</h3><p>Delhi–Meerut Expressway and Delhi–Dehradun Expressway are marked on the schematic. The map does not establish travel times.</p></article>
                    <article><h3>360 PropGuide’s location lens</h3><p>During a visit, examine the actual turn-in, parking entry and walking route to your selected floor. Catchment and competing retail need an on-ground assessment.</p></article>
                </div>
            </div>
        </div>
    </section>

    <section class="epic-section" id="epic-costs" aria-labelledby="costs-title">
        <div class="epic-shell">
            <header class="epic-section__head"><div><p class="epic-eyebrow">05 / Costs & leasing</p><h2 id="costs-title">From purchase<br>to opening day.</h2></div><p>The quoted rate is one part of the cost. Shop fit-out and ongoing operations need their own budget.</p></header>
            <div class="epic-cost-layout">
                <div class="epic-cost-list">
                    <article><h3>Property price</h3><p>Quoted floor rate × quoted area; confirm the exact area basis in the unit cost sheet.</p></article>
                    <article><h3>Shop fit-out</h3><p>RCC floor slab and exposed RCC ceiling. The buyer provides flooring; obtain the complete handover specification.</p></article>
                    <article><h3>Parking & common-area charges</h3><p>Confirm allocation, parking price, maintenance rate and deposits before calculating the total commitment.</p></article>
                    <article><h3>Utilities, premiums & taxes</h3><p>Request itemised electrical or backup charges, location premiums and all applicable taxes.</p></article>
                </div>
                <aside class="epic-leasing-card"><p class="epic-eyebrow">Leasing reality</p><h3>What is known about leasing?</h3><p>No signed tenant, fixed rental commitment or lease-management agreement has been supplied for this review. Ask for unit-specific terms before including rent in your cash-flow plan.</p><a href="https://wa.me/919643020020?text=I%20want%20to%20discuss%20EPIC%20cost%20and%20leasing%20terms" target="_blank" rel="noopener">Discuss cost & leasing terms ↗</a></aside>
            </div>
            <details class="epic-handover"><summary>What does the brochure say about shop handover?</summary><p>RCC floor slab, ready for flooring by the buyer; exposed RCC ceiling; dry wall or brickwork with single-coat cement paint; and single-point electrical distribution. Obtain the selected unit’s complete specification before budgeting for fit-out.</p></details>
        </div>
    </section>

    <section class="epic-contact" aria-labelledby="contact-title">
        <div class="epic-shell epic-contact__grid">
            <div><p class="epic-eyebrow epic-eyebrow--light">06 / Talk to 360 PropGuide</p><h2 id="contact-title">Start with the floor.<br>We’ll help with the next decision.</h2><p>Ask for a unit cost sheet, discuss your business requirement or arrange a site visit.</p></div>
            <div class="epic-contact__actions"><a class="epic-btn epic-btn--primary" href="https://wa.me/919643020020?text=I%20want%20to%20discuss%20my%20requirement%20for%20EPIC" target="_blank" rel="noopener">Discuss my requirement ↗</a><a class="epic-btn epic-btn--ghost" href="https://wa.me/919643020020?text=I%20want%20to%20arrange%20an%20EPIC%20site%20visit" target="_blank" rel="noopener">Arrange a site visit</a><p>Or call <a href="tel:+919643020020">+91 96430 20020</a></p></div>
        </div>
    </section>

    <section class="epic-disclosure" aria-label="Project details and sources"><div class="epic-shell"><details open><summary>Project details, sources & review date</summary><div><p><strong>Project:</strong> EPIC by Jindalsons, Siddharth Vihar, Ghaziabad.</p><p><strong>Sources:</strong> Developer brochure visuals, drawings and information supplied for this page.</p><p><strong>Review date:</strong> 15 September 2026. Pricing and availability must be reconfirmed before purchase.</p></div></details></div></section>
</main>
@endsection

@section('customJS')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const floors = @json($floors);
    const headings = ['Lower-ground space with destination potential.', 'Street-level presence, examined properly.', 'Upper-level retail around shared circulation.', 'Value-led retail with vertical access in focus.', 'A dining floor designed around dwell time.'];
    const tabs = document.querySelectorAll('.epic-plan-tabs button');
    tabs.forEach((tab) => tab.addEventListener('click', function () {
        const floor = floors[Number(this.dataset.floor)];
        tabs.forEach((item) => item.classList.remove('is-active'));
        this.classList.add('is-active');
        document.getElementById('floor-label').textContent = floor.name + ' &middot; ' + floor.rate + '/sq ft';
        document.getElementById('floor-heading').textContent = headings[Number(this.dataset.floor)];
    }));

    const gallery = document.getElementById('epic-gallery-modal');
    const gallerySlides = Array.from(gallery.querySelectorAll('.epic-gallery-slide'));
    const galleryCurrent = document.getElementById('epic-gallery-current');
    let activeGalleryImage = 0;

    function showGalleryImage(index) {
        activeGalleryImage = (index + gallerySlides.length) % gallerySlides.length;
        gallerySlides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === activeGalleryImage));
        galleryCurrent.textContent = activeGalleryImage + 1;
    }

    document.getElementById('open-epic-gallery').addEventListener('click', function () {
        showGalleryImage(0);
        gallery.showModal();
        document.body.classList.add('epic-gallery-open');
    });
    gallery.querySelector('.epic-gallery-close').addEventListener('click', () => gallery.close());
    gallery.querySelector('.epic-gallery-prev').addEventListener('click', () => showGalleryImage(activeGalleryImage - 1));
    gallery.querySelector('.epic-gallery-next').addEventListener('click', () => showGalleryImage(activeGalleryImage + 1));
    gallery.addEventListener('close', () => document.body.classList.remove('epic-gallery-open'));
    gallery.addEventListener('click', function (event) {
        if (event.target === gallery) gallery.close();
    });
    gallery.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') showGalleryImage(activeGalleryImage - 1);
        if (event.key === 'ArrowRight') showGalleryImage(activeGalleryImage + 1);
    });
});
</script>
@endsection
