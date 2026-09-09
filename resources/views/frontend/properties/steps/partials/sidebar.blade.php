@php
// Define step titles
$stepTitles = [
1 => 'Basic Details',
2 => 'Advanced Details',
3 => 'Amenities',
4 => 'Galleries',
5 => 'Verify',
];
$stepRoutes = [
1 => 'property_details',
2 => 'advanced_details',
3 => 'amenities',
4 => 'galleries',
5 => 'verify',
];
$completedSteps = $property->step_id ?? 0;
$totalSteps = count($stepTitles);
$progressPercent = min(100, max(0, (($completedSteps - 1) / max(1, $totalSteps - 1)) * 100));

@endphp

{{-- Step Sidebar --}}
        <div class="col-md-5 col-lg-4 mb-4 col-xxl-3">
            <div class=" card p-4 p-lg-5 border-0 shadow-lg h-fit">
                <h2 class="h3">Post your property</h2>
                <p class="text-muted mb-1">Sell or rent your property</p>

                {{-- Progress Bar --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="progress rounded-pill w-100" role="progressbar" aria-valuenow="{{ $progressPercent }}"
                        aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-primary rounded-pill" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <span class="ms-2 text-muted">{{ round($progressPercent) }}%</span>
                </div>

                {{-- Step List (Desktop) --}}
                <div class="step-wrapper d-none d-md-flex flex-column align-items-start">
                    @foreach ($stepTitles as $step => $title)
                    @php
                    $status = $step == $currentStep ? 'in_progress' : ($step < $completedSteps ? 'completed' : 'pending' );
                        $routeName='postproperty.edit.' . $stepRoutes[$step];
                        $route=$step <=$completedSteps ? route($routeName, $property->property_uid) : '#';
                        @endphp

                        <div class="d-flex align-items-center align-items-md-start step-item position-relative">
                            <div class="d-flex flex-column align-items-center">
                                <div class="step-circle {{ $status }} {{ $step === $currentStep ? 'current-step' : '' }}">
                                    @if ($status === 'completed')
                                    <i class="fas fa-check text-white"></i>
                                    @elseif ($status === 'in_progress')
                                    {{-- Can add loader if needed --}}
                                    @else
                                    <i class="fas fa-clock text-white"></i>
                                    @endif
                                </div>
                                @if ($step < $totalSteps)
                                    <div class="step-line {{ $status }}"></div>
                                @endif

                            </div>

                            <div class="ms-md-3">
                                @if ($step <= $completedSteps)
                                    <a href="{{ $route }}" class="text-decoration-none text-dark fw-semibold">{{ $title }}</a>
                                    @else
                                    <div class="fw-semibold text-muted">{{ $title }}</div>
                                    @endif

                                    <div class="step-status {{ $status }}">
                                        {{ $status === 'in_progress' ? 'In progress' : ucfirst($status) }}
                                    </div>
                            </div>
                        </div>
                        @endforeach
                </div>


            </div>

            {{-- Mobile Step Pills --}}
            <div class="step-wrapper d-flex d-md-none gap-2 overflow-auto px-2 mt-3">
                @foreach ($stepTitles as $step => $title)
                @php
                $status = $step < $completedSteps ? 'completed' : ($step==$completedSteps ? 'in_progress' : 'pending' );
                    $routeName='postproperty.edit.' . $stepRoutes[$step]; $route=$step <=$completedSteps ? route($routeName,
                    $property->property_uid) : '#';
                    @endphp

                    <a href="{{ $route }}" class="step-pill {{ $status }} text-decoration-none">
                        @if ($status === 'completed')
                        <i class="fas fa-check-circle me-1"></i>
                        @elseif ($status === 'in_progress')
                        <i class="fas fa-spinner fa-spin me-1"></i>
                        @else
                        <i class="fas fa-clock me-1"></i>
                        @endif
                        {{ $title }}
                    </a>
                    @endforeach

            </div>

        </div>