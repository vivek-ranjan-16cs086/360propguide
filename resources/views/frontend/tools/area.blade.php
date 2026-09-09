@extends('frontend.layouts.app')

@section('title', "Area Converter | Convert Property Area Units Instantly Online")
@section('description',"Use our Area Converter tool to instantly convert property area units like sq ft, sq m, acres, bigha and hectare. Fast, accurate and helpful for real estate buyers.")
@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/css/tools/area.css')}}">
@endsection
@section('content')
<section class="p-4">
    <div class="container">
        <div class="row align-items-center mb-3">
            <div class="col-md-8">
                <h1 class="mb-0" style="color:#1b5577">Area Converter</h>
                <small class="text-muted">Convert between common land/area units used in India & internationally.</small>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <select id="preset-select" class="form-select w-auto d-inline-block">
                    <option value="default">Standard (Common)</option>
                    <option value="up-pb">North India (Punjab/Himachal)</option>
                    <option value="bihar">Bihar / Eastern</option>
                </select>
            </div>
        </div>

        <div class="row g-4">
            <!-- INPUT CARD -->
            <div class="col-lg-4">
                <div class="card p-3">
                    <label class="form-label">Enter value</label>
                    <div class="input-group mb-2">
                        <input id="input-value" class="form-control" type="number" value="100" min="0" step="any" />
                        <select id="input-unit" class="form-select" style="max-width:140px">
                            <!-- we fill unit options via JS -->
                        </select>
                    </div>

                    <label class="form-label d-block mt-2">Search units</label>
                    <input id="unit-search" class="form-control mb-3" placeholder="Type to filter units (e.g. 'acre', 'gaj')" />

                    <div class="d-grid gap-2">
                        <button id="convert-btn" class="btn loginBtn">Convert</button>
                    </div>
                </div>
            </div>

            <!-- RESULTS + CHART -->
            <div class="col-lg-8">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" style="color:#1b5577">Results</h5>
                        <div>
                            <button id="copy-all" class="btn btn-outline-secondary btn-sm">Copy All</button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <canvas id="areaChart" height="100"></canvas>
                        </div>
                    </div>

                    <div class="row" id="results-grid" style="row-gap:16px;">
                        <!-- dynamic result cards inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@section('customJS')
<script src="{{ asset('frontend/libraries/chart.umd.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const presets = {
            default: {
                factors: {
                    "sqm": 1,
                    "sqft": 0.092903,
                    "sqyd": 0.83612736,
                    "acre": 4046.856421,
                    "hectare": 10000,
                    "sqinch": 0.00064516,
                    "sqmile": 2589988.110336,
                    "are": 100,

                    "gaj": 0.83612736,
                    "marla": 25.292852,
                    "guntha": 101.17141,
                    "kanal": 505.857052,
                    "katha": 126.441037,

                    "bigha": 2500
                }
            },
            "up-pb": {
                factors: {
                    "sqm": 1,
                    "sqft": 0.092903,
                    "sqyd": 0.83612736,
                    "acre": 4046.856421,
                    "hectare": 10000,
                    "sqinch": 0.00064516,
                    "sqmile": 2589988.110336,
                    "are": 100,
                    "gaj": 0.83612736,
                    "marla": 25.292852,
                    "guntha": 101.17141,
                    "kanal": 505.857052,
                    "katha": 126.441037,
                    "bigha": 1011.71367
                }
            },
            "bihar": {
                factors: {
                    "sqm": 1,
                    "sqft": 0.092903,
                    "sqyd": 0.83612736,
                    "acre": 4046.856421,
                    "hectare": 10000,
                    "sqinch": 0.00064516,
                    "sqmile": 2589988.110336,
                    "are": 100,
                    "gaj": 0.83612736,
                    "marla": 25.292852,
                    "guntha": 101.17141,
                    "kanal": 505.857052,
                    "katha": 55.7418,
                    "bigha": 2500
                }
            }
        };


        const unitsMeta = [{
                key: "sqm",
                label: "Square Meters (m²)"
            },
            {
                key: "sqft",
                label: "Square Feet (ft²)"
            },
            {
                key: "sqyd",
                label: "Square Yards / Gaj (yd²)"
            },
            {
                key: "sqinch",
                label: "Square Inches (in²)"
            },
            {
                key: "acre",
                label: "Acre"
            },
            {
                key: "hectare",
                label: "Hectare"
            },
            {
                key: "are",
                label: "Are"
            },
            {
                key: "sqmile",
                label: "Square Mile"
            },
            {
                key: "marla",
                label: "Marla"
            },
            {
                key: "guntha",
                label: "Guntha"
            },
            {
                key: "kanal",
                label: "Kanal"
            },
            {
                key: "katha",
                label: "Katha"
            },
            {
                key: "bigha",
                label: "Bigha"
            }
        ];


        let state = {
            preset: 'default',
            factors: {
                ...presets.default.factors
            },
            inputValue: 100,
            inputUnit: 'sqm',
            chart: null
        };


        const inputValueEl = document.getElementById('input-value');
        const inputUnitEl = document.getElementById('input-unit');
        const convertBtn = document.getElementById('convert-btn');
        const resultsGrid = document.getElementById('results-grid');
        const unitSearch = document.getElementById('unit-search');
        const areaChartCanvas = document.getElementById('areaChart').getContext('2d');
        const presetSelect = document.getElementById('preset-select');
        const copyAllBtn = document.getElementById('copy-all');

        function populateUnitOptions() {
            inputUnitEl.innerHTML = '';
            unitsMeta.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u.key;
                opt.textContent = u.label;
                inputUnitEl.appendChild(opt);
            });
            inputUnitEl.value = state.inputUnit;
        }

        populateUnitOptions();

        // Apply preset
        function applyPreset(name) {
            if (!presets[name]) name = 'default';
            state.preset = name;
            state.factors = {
                ...presets[name].factors
            };
            renderResults();
        }

        presetSelect.addEventListener('change', () => {
            applyPreset(presetSelect.value);
        });

        function convertToAll(value, fromUnit) {
            const factors = state.factors;
            const valueInSqm = parseFloat(value) * (factors[fromUnit] || 1);
            const out = {};
            for (const key in factors) {
                out[key] = valueInSqm / factors[key];
            }
            return out;
        }

        function animateNumber(el, start, end, duration = 600) {
            const startTime = performance.now();

            function tick(now) {
                const t = Math.min((now - startTime) / duration, 1);
                const eased = t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t; // easeInOutQuad-ish
                const current = start + (end - start) * eased;
                el.textContent = Number(current).toLocaleString('en-IN', {
                    maximumFractionDigits: 6
                });
                if (t < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        }


        async function copyText(text) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (e) {
                return false;
            }
        }


        function makeResultCard(unitKey, unitLabel, value) {
            const wrapper = document.createElement('div');
            wrapper.className = 'col-12 col-md-6';

            const inner = document.createElement('div');
            inner.className = 'result-card';

            const left = document.createElement('div');
            left.innerHTML = `<div class="unit-label">${unitLabel}</div><div class="small-muted">${unitKey}</div>`;

            const right = document.createElement('div');
            right.style.display = 'flex';
            right.style.alignItems = 'center';
            right.style.gap = '10px';

            const num = document.createElement('div');
            num.className = 'value-num';
            num.textContent = Number(value).toLocaleString('en-IN', {
                maximumFractionDigits: 6
            });

            const btn = document.createElement('button');
            btn.className = 'copy-btn';
            btn.innerHTML = 'Copy';
            btn.title = 'Copy value';
            btn.addEventListener('click', async () => {
                const ok = await copyText(`${value}`);
                btn.textContent = ok ? 'Copied' : 'Copy';
                setTimeout(() => btn.textContent = 'Copy', 1200);
            });

            right.appendChild(num);
            right.appendChild(btn);

            inner.appendChild(left);
            inner.appendChild(right);
            wrapper.appendChild(inner);
            return {
                wrapper,
                numEl: num
            };
        }

        let lastResults = {};

        function renderResults(filter = '') {
            const val = state.inputValue;
            const from = state.inputUnit;
            const results = convertToAll(val, from);
            lastResults = results;

            resultsGrid.innerHTML = '';
            const chartLabels = [];
            const chartData = [];

            unitsMeta.forEach(u => {
                if (!results.hasOwnProperty(u.key)) return;
                if (filter && !u.label.toLowerCase().includes(filter.toLowerCase()) && !u.key.includes(filter.toLowerCase())) return;
                const v = results[u.key];
                const card = makeResultCard(u.key, u.label, v);
                resultsGrid.appendChild(card.wrapper);
                chartLabels.push(u.label);
                chartData.push(Number(v.toFixed(6)));
            });


            if (state.chart) {
                state.chart.data.labels = chartLabels;
                state.chart.data.datasets[0].data = chartData;
                state.chart.update();
            }
        }

        function initChart() {
            state.chart = new Chart(areaChartCanvas, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Converted value (relative)',
                        data: [],
                        backgroundColor: '#1b5577'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // EVENTS
        convertBtn.addEventListener('click', () => {
            state.inputValue = Number(inputValueEl.value) || 0;
            state.inputUnit = inputUnitEl.value;
            renderResults(unitSearch.value.trim());
        });

        inputValueEl.addEventListener('input', () => {
            state.inputValue = Number(inputValueEl.value) || 0;
            renderResults(unitSearch.value.trim());
        });
        inputUnitEl.addEventListener('change', () => {
            state.inputUnit = inputUnitEl.value;
            renderResults(unitSearch.value.trim());
        });

        unitSearch.addEventListener('input', () => {
            renderResults(unitSearch.value.trim());
        });

        copyAllBtn.addEventListener('click', async () => {
            const lines = [];
            for (const meta of unitsMeta) {
                if (!lastResults.hasOwnProperty(meta.key)) continue;
                lines.push(`${meta.label}: ${Number(lastResults[meta.key]).toLocaleString('en-IN', { maximumFractionDigits: 6 })}`);
            }
            const ok = await copyText(lines.join('\n'));
            copyAllBtn.textContent = ok ? 'Copied' : 'Copy All';
            setTimeout(() => copyAllBtn.textContent = 'Copy All', 1200);
        });

        // initial setup
        initChart();
        applyPreset('default');
        // initial render
        state.inputValue = Number(inputValueEl.value) || 0;
        state.inputUnit = inputUnitEl.value;
        renderResults();

    });
</script>
@endsection