@if(!empty($projects) && count($projects) > 0)
@foreach($projects as $project)
@php
$typologyDisplay = !empty($project->typology_text) ? $project->typology_text : (!empty($project->typology) &&
is_string($project->typology) ? $project->typology : 'Details on request');
$statusClean = !empty($project->project_status) ? ucfirst(str_replace('_', ' ', clean($project->project_status))) : '';
$hasRera = !empty($project->rera_no) && strtoupper(trim($project->rera_no)) !== 'N/A';
$projectUrl = route('projects.details', $project->slug);
@endphp
<article class="projects-grid__item">
    <a href="{{ $projectUrl }}" class="project-card" aria-label="View {{ $project->project_name }}">
        <div class="project-card__media">

            <img src="{{env('APP_URL').'storage/'.$project->logo_image }}"
                alt="{{ $project->project_name }} - 360 PropGuide" loading="lazy">
            <div class="project-card__badges">
                <div class="project-card__badge-left">
                    @if(!empty($statusClean))
                    <span class="project-card__status">{{ $statusClean }}</span>
                    @endif
                </div>
                <div class="project-card__badge-right">
                    @if($hasRera)
                    <span class="project-card__rera"><i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        RERA</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="project-card__body">
            <h3 class="project-card__title" title="{{ $project->project_name }}">{{ $project->project_name }}</h3>
            <div class="project-card__meta">
                <div class="project-card__meta-item">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <span>{{ $project->location ?: 'Location on request' }}</span>
                </div>
                <div class="project-card__meta-item">
                    <i class="fa-solid fa-building" aria-hidden="true"></i>
                    <span>{{ $typologyDisplay }}</span>
                </div>
            </div>
            <div class="project-card__divider"></div>
            <div class="project-card__footer">
                <div class="project-card__price-wrap">
                    <span class="project-card__price-label">Starting from</span>
                    <p class="project-card__price">
                        ₹{{ formatPrice($project->price) }}
                        @if(!empty($project->max_price) && $project->max_price != $project->price)
                        <em>– ₹{{ formatPrice($project->max_price) }}</em>
                        @endif
                    </p>
                </div>
                <span class="project-card__cta">View <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
            </div>
        </div>
    </a>
</article>
@endforeach
@else
<div class="projects-grid__empty">
    <div class="project-empty-state">
        <span class="project-empty-state__icon"><i class="fa-solid fa-house-circle-xmark" aria-hidden="true"></i></span>
        <h3>No projects found</h3>
        <p>Try adjusting your search or filters to discover more properties.</p>
        <a href="{{ route('projects') }}" class="project-empty-state__reset">Reset filters</a>
    </div>
</div>
@endif