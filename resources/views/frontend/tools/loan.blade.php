@extends('frontend.layouts.app')

@section('title', "Loan Eligibility Calculator | Check Your Home Loan Limit")
@section('description', "Use our Loan Eligibility Calculator to instantly check your home loan limit based on income, EMI capacity, interest rate, and tenure. Plan smarter property buying.")
@section('keywords', "Real Estate Services")
@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/css/tools/loan.css')}}">
@endsection
@section('content')
<section class="p-4">
    <div class="container">

        <!-- HEADER -->
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h1 class="mb-0" style="color:#1b5577">Loan Eligibility Calculator</h1>
                <small class="text-muted">Advanced FOIR-based model (bank-level). Enter borrowers' details to get a detailed eligibility report.</small>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button id="check-eligibility" class="btn loginBtn">Check Eligibility</button>
            </div>
        </div>

        <div class="row g-4">


            <div class="col-lg-5">
                <div class="card p-3">

                    <div class="mb-3">
                        <label class="form-label">Number of Borrowers</label>
                        <input id="num-borrowers" type="number" class="form-control" min="1" max="4" value="1">
                    </div>

                    <div id="borrowers-container">

                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Desired Tenure (years)</label>
                        <input id="desired-tenure" type="number" class="form-control" min="1" max="40" value="20">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rate of Interest (annual %)</label>
                        <input id="interest-rate" type="number" class="form-control" min="1" max="20" step="0.01" value="8.9">
                    </div>

                    <div class="d-flex gap-2">
                        <button id="reset-btn" class="btn btn-outline-secondary">Reset</button>
                        <button id="export-btn" class="btn btn-outline-secondary">Export Report</button>
                    </div>

                </div>
            </div>

            <!-- RIGHT: RESULTS -->
            <div class="col-lg-7">
                <div class="card p-3">
                    <h5 style="color:#1b5577">Detailed Bank Report</h5>

                    <div id="report" class="mt-3">
                        <div class="text-muted">Fill inputs and click <strong>Check Eligibility</strong>.</div>
                    </div>

                    <div class="mt-4">
                        <h6>Affordability Summary</h6>
                        <div id="summary-cards" class="row g-3">

                        </div>
                    </div>
                </div>

                <!-- Chart Card -->
                <div class="card p-3 mt-4">
                    <h5 style="color:#1b5577">Loan Amount vs Tenure</h5>
                    <small class="text-muted">Chart shows how eligible loan amount grows with tenure (combined eligible EMI fixed).</small>
                    <div class="mt-3">
                        <canvas id="loanTenureChart" height="220"></canvas>
                    </div>
                    <div class="mt-2 muted-small">Vertical green = requested tenure. Vertical orange = conservative max tenure (shortest borrower's allowable tenure).</div>
                </div>

                <div class="card p-3 mt-4">
                    <h5 style="color:#1b5577">FAQs</h5>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">
                                    What is FOIR?
                                </button>
                            </h2>
                            <div id="faqOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">FOIR stands for Financial Obligation to Income Ratio. It is the percentage of your net monthly income that banks allow for EMIs + other obligations.</div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">
                                    How is maximum tenure calculated?
                                </button>
                            </h2>
                            <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Banks cap loan repayment age. For salaried applicants we assume completion age 60; for self-employed completion age 65. Max tenure = (MaxCompletionAge - currentAge).</div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4" style="color:#1b5577">Articles</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">How banks compute eligibility — an insider's guide</a></li>
                        <li><a href="#" class="text-decoration-none">Improve your home loan eligibility: 7 practical tips</a></li>
                        <li><a href="#" class="text-decoration-none">FOIR vs DTI: what lenders look at</a></li>
                    </ul>
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


        const FOIR_MAP = {
            'salaried': 0.55,
            'self-employed': 0.45,
            'business': 0.40
        };


        const MAX_COMPLETION_AGE = {
            'salaried': 60,
            'self-employed': 65,
            'business': 65
        };

        // Helper DOM references
        const numBorrowersEl = document.getElementById('num-borrowers');
        const borrowersContainer = document.getElementById('borrowers-container');
        const desiredTenureEl = document.getElementById('desired-tenure');
        const interestRateEl = document.getElementById('interest-rate');
        const checkBtn = document.getElementById('check-eligibility');
        const resetBtn = document.getElementById('reset-btn');
        const reportEl = document.getElementById('report');
        const summaryCards = document.getElementById('summary-cards');
        const exportBtn = document.getElementById('export-btn');

        // Chart context
        const loanTenureCtx = document.getElementById('loanTenureChart').getContext('2d');
        let loanTenureChart = null;

    
        function createBorrowerCard(index, data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = 'borrower-card';
            wrapper.dataset.index = index;

            wrapper.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Borrower ${index + 1}</strong>
        <button class="btn btn-sm btn-outline-secondary remove-borrower" ${index === 0 ? 'style="display:none"' : ''}>Remove</button>
      </div>

      <div class="row g-2">
        <div class="col-6">
          <label class="form-label">Age</label>
          <input type="number" class="form-control borrower-age" min="18" max="70" value="${data.age ?? 30}">
        </div>
        <div class="col-6">
          <label class="form-label">Occupation</label>
          <select class="form-select borrower-occupation">
            <option value="salaried" ${data.occupation === 'salaried' ? 'selected' : ''}>Salaried</option>
            <option value="self-employed" ${data.occupation === 'self-employed' ? 'selected' : ''}>Self-employed</option>
            <option value="business" ${data.occupation === 'business' ? 'selected' : ''}>Business</option>
          </select>
        </div>

        <div class="col-12 mt-2">
          <label class="form-label">Monthly Net Income (₹)</label>
          <input type="number" class="form-control borrower-income" min="0" value="${data.income ?? 50000}">
        </div>

        <div class="col-12 mt-2">
          <label class="form-label">Existing Monthly EMI / Obligations (₹)</label>
          <input type="number" class="form-control borrower-existing" min="0" value="${data.existing ?? 0}">
        </div>
      </div>
    `;

            // remove button handler
            wrapper.querySelector('.remove-borrower')?.addEventListener('click', () => {
                wrapper.remove();
                refreshBorrowerIndexes();
                updateNumBorrowersValue();
            });

            return wrapper;
        }

        function refreshBorrowerIndexes() {
            const cards = borrowersContainer.querySelectorAll('.borrower-card');
            cards.forEach((c, i) => {
                c.dataset.index = i;
                c.querySelector('strong').textContent = `Borrower ${i + 1}`;
                const removeBtn = c.querySelector('.remove-borrower');
                if (removeBtn) removeBtn.style.display = i === 0 ? 'none' : '';
            });
        }

        function updateNumBorrowersValue() {
            const count = borrowersContainer.querySelectorAll('.borrower-card').length;
            numBorrowersEl.value = count;
        }

        function setNumBorrowers(n) {

            n = Math.max(1, Math.min(4, Number(n) || 1));
            const current = borrowersContainer.querySelectorAll('.borrower-card').length;
            if (n > current) {
                for (let i = 0; i < n - current; i++) {
                    borrowersContainer.appendChild(createBorrowerCard(current + i));
                }
            } else if (n < current) {

                for (let i = 0; i < current - n; i++) {
                    const cards = borrowersContainer.querySelectorAll('.borrower-card');
                    if (cards.length > 1) cards[cards.length - 1].remove();
                }
            }
            refreshBorrowerIndexes();
            updateNumBorrowersValue();
        }


        borrowersContainer.appendChild(createBorrowerCard(0));
        setNumBorrowers(1);


        numBorrowersEl.addEventListener('input', (e) => {
            setNumBorrowers(e.target.value);
        });


        resetBtn.addEventListener('click', () => {
            borrowersContainer.innerHTML = '';
            borrowersContainer.appendChild(createBorrowerCard(0));
            setNumBorrowers(1);
            desiredTenureEl.value = 20;
            interestRateEl.value = 8.9;
            reportEl.innerHTML = '<div class="text-muted">Fill inputs and click <strong>Check Eligibility</strong>.</div>';
            summaryCards.innerHTML = '';
            updateLoanTenureChart([], [], null, null);
        });


        function monthlyRate(annualPercent) {
            return (Number(annualPercent) / 1200);
        }

    
        function emiForLoan(principal, r, n) {
            if (r === 0) return principal / n;
            const num = principal * r * Math.pow(1 + r, n);
            const den = Math.pow(1 + r, n) - 1;
            return num / den;
        }

     
        function loanForEmi(emi, r, n) {
            if (r === 0) return emi * n;
            const factor = (Math.pow(1 + r, n) - 1) / (r * Math.pow(1 + r, n));
            return emi * factor;
        }

        // Safe numeric parse
        function safeNumber(v) {
            const n = Number(v);
            return isFinite(n) ? n : 0;
        }


        function calculateEligibility() {


            const borrowerCards = borrowersContainer.querySelectorAll('.borrower-card');
            const borrowers = Array.from(borrowerCards).map(card => {
                const age = safeNumber(card.querySelector('.borrower-age').value);
                const occupation = card.querySelector('.borrower-occupation').value;
                const income = safeNumber(card.querySelector('.borrower-income').value);
                const existing = safeNumber(card.querySelector('.borrower-existing').value);
                return {
                    age,
                    occupation,
                    income,
                    existing
                };
            });

            const interestAnnual = safeNumber(interestRateEl.value);
            const tenureYearsRequested = Math.max(1, Math.min(40, safeNumber(desiredTenureEl.value)));
            const rMonthly = monthlyRate(interestAnnual);
            const nMonthsRequested = Math.round(tenureYearsRequested * 12);

            // Per-borrower computations
            let totalEligibleEmi = 0;
            let totalIncome = 0;
            let totalExisting = 0;
            const borrowerResults = borrowers.map(b => {
                const foir = FOIR_MAP[b.occupation] ?? 0.45;
                const maxEmiAllowed = Math.max(0, b.income * foir - b.existing); // eligible EMI from this borrower
                // cap negative to zero
                const maxAge = MAX_COMPLETION_AGE[b.occupation] ?? 60;
                const maxTenureMonths = Math.max(0, (maxAge - b.age) * 12);
                const effectiveTenureMonths = Math.max(12, Math.min(nMonthsRequested, maxTenureMonths || nMonthsRequested)); // at least 12 months
                const loanAtRequestedTenure = loanForEmi(maxEmiAllowed, rMonthly, effectiveTenureMonths);
                const riskClass = (b.income <= 20000) ? 'High' : (b.income <= 60000 ? 'Medium' : 'Low');

                totalEligibleEmi += maxEmiAllowed;
                totalIncome += b.income;
                totalExisting += b.existing;

                return {
                    ...b,
                    foir,
                    maxEmiAllowed,
                    maxTenureMonths,
                    effectiveTenureMonths,
                    loanAtRequestedTenure,
                    riskClass
                };
            });


            const combinedEligibleEmi = totalEligibleEmi;
            const combinedLoanAtRequestedTenure = loanForEmi(combinedEligibleEmi, rMonthly, nMonthsRequested);
            const minEffectiveTenure = borrowerResults.length ? Math.min(...borrowerResults.map(b => b.maxTenureMonths || nMonthsRequested)) : nMonthsRequested;
            const loanConservative = loanForEmi(combinedEligibleEmi, rMonthly, minEffectiveTenure);


            const affordabilityRatio = totalIncome ? (combinedEligibleEmi / totalIncome) : 0;
            const affordabilityScore = Math.max(0, Math.min(100, Math.round((1 - affordabilityRatio) * 100)));

            return {
                borrowers: borrowerResults,
                totalIncome,
                totalExisting,
                combinedEligibleEmi,
                combinedLoanAtRequestedTenure,
                loanConservative,
                affordabilityRatio,
                affordabilityScore,
                interestAnnual,
                tenureYearsRequested,
                nMonthsRequested,
                rMonthly,
                minEffectiveTenure
            };
        }


        function renderReport(result) {
            // Clear
            reportEl.innerHTML = '';

            // Header summary
            const header = document.createElement('div');
            header.innerHTML = `
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="info-key">Combined Eligible EMI</div>
          <div class="big-number">₹ ${Number(result.combinedEligibleEmi).toLocaleString('en-IN', { maximumFractionDigits: 2 })} / month</div>
          <div class="muted-small">Based on FOIR & existing obligations</div>
        </div>
        <div>
          <div class="info-key">Max Loan (requested tenure ${result.tenureYearsRequested} yrs)</div>
          <div class="big-number">₹ ${Number(result.combinedLoanAtRequestedTenure).toLocaleString('en-IN', { maximumFractionDigits: 0 })}</div>
          <div class="muted-small">Using interest ${result.interestAnnual}%</div>
        </div>
        <div>
          <div class="info-key">Conservative Loan (based on shortest borrower tenure)</div>
          <div class="big-number">₹ ${Number(result.loanConservative).toLocaleString('en-IN', { maximumFractionDigits: 0 })}</div>
          <div class="muted-small">Conservative approach—useful for joint loans</div>
        </div>
      </div>
    `;
            reportEl.appendChild(header);

            // Divider
            reportEl.appendChild(document.createElement('hr'));

            // Per-borrower details
            const perHeader = document.createElement('h6');
            perHeader.textContent = 'Per Borrower Details';
            reportEl.appendChild(perHeader);

            result.borrowers.forEach((b, i) => {
                const card = document.createElement('div');
                card.className = 'p-3 mb-2';
                card.style.border = '1px solid rgba(0,0,0,0.05)';
                card.style.borderRadius = '8px';
                card.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="muted-small">Borrower ${i+1} — ${b.occupation.replace('-', ' ')}</div>
            <div style="font-weight:700; font-size:18px;">Income: ₹ ${Number(b.income).toLocaleString('en-IN')} | Existing EMI: ₹ ${Number(b.existing).toLocaleString('en-IN')}</div>
          </div>
          <div style="text-align:right">
            <div class="info-key">FOIR used</div>
            <div class="big-number">${(b.foir*100).toFixed(0)}%</div>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-md-4">
            <div class="info-key">Max Eligible EMI</div>
            <div>₹ ${Number(b.maxEmiAllowed).toLocaleString('en-IN', {maximumFractionDigits:2})}</div>
          </div>
          <div class="col-md-4">
            <div class="info-key">Max Completion Age</div>
            <div>${MAX_COMPLETION_AGE[b.occupation] ?? 60} yrs</div>
          </div>
          <div class="col-md-4">
            <div class="info-key">Max Tenure Allowed</div>
            <div>${Math.floor(b.maxTenureMonths/12)} yrs (${b.maxTenureMonths} months)</div>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-md-6">
            <div class="info-key">Loan possible at effective tenure (${Math.round(b.effectiveTenureMonths/12)} yrs)</div>
            <div>₹ ${Number(b.loanAtRequestedTenure).toLocaleString('en-IN', {maximumFractionDigits:0})}</div>
          </div>
          <div class="col-md-6" style="text-align:right">
            <div class="info-key">Risk class</div>
            <div class="${b.riskClass === 'Low' ? 'badge-ok' : (b.riskClass === 'Medium' ? 'badge-warn' : 'badge-bad')}">${b.riskClass}</div>
          </div>
        </div>
      `;
                reportEl.appendChild(card);
            });

            // Summary stats cards
            summaryCards.innerHTML = '';
            const cards = [{
                    title: 'Total Net Income',
                    value: `₹ ${Number(result.totalIncome).toLocaleString('en-IN')}`
                },
                {
                    title: 'Total Existing Obligations',
                    value: `₹ ${Number(result.totalExisting).toLocaleString('en-IN')}`
                },
                {
                    title: 'Combined Eligible EMI',
                    value: `₹ ${Number(result.combinedEligibleEmi).toLocaleString('en-IN')}`
                },
                {
                    title: 'Affordability Score',
                    value: `${result.affordabilityScore} / 100`
                }
            ];
            cards.forEach(c => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3';
                col.innerHTML = `
        <div class="p-3" style="background:#fff;border-radius:10px;border:1px solid rgba(0,0,0,0.04);box-shadow:0 6px 18px rgba(0,0,0,0.04)">
          <div class="info-key">${c.title}</div>
          <div class="big-number">${c.value}</div>
        </div>
      `;
                summaryCards.appendChild(col);
            });

            const footer = document.createElement('div');
            footer.className = 'mt-3 muted-small';
            footer.innerHTML = `
      <div>Suggestion: For a conservative joint loan, banks often use conservative tenure and combined stress tests. Use the conservative loan value for borrower safety. Contact a lender for a formal pre-approval.</div>
    `;
            reportEl.appendChild(document.createElement('hr'));
            reportEl.appendChild(footer);


            const maxChartYears = 30;
            const years = [];
            const loanAmounts = [];
            for (let y = 1; y <= maxChartYears; y++) {
                const months = y * 12;
                const loanAmt = loanForEmi(result.combinedEligibleEmi, result.rMonthly, months);
                years.push(String(y));
                loanAmounts.push(Math.round(loanAmt));
            }

            const requestedTenure = result.tenureYearsRequested;
            const conservativeYears = Math.max(1, Math.floor(result.minEffectiveTenure / 12));

            updateLoanTenureChart(years, loanAmounts, requestedTenure, conservativeYears);
        }

        /* ---------------------------
           Chart functions
           --------------------------- */
        function initLoanTenureChart() {
            loanTenureChart = new Chart(loanTenureCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Eligible Loan Amount (₹)',
                        data: [],
                        borderColor: '#1b5577',
                        backgroundColor: 'rgba(27,85,119,0.08)',
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `₹ ${Number(ctx.parsed.y).toLocaleString('en-IN')}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tenure (years)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Loan Amount (₹)'
                            },
                            ticks: {
                                callback: function(val) {
                                    return '₹' + Number(val).toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        function updateLoanTenureChart(labels, data, requestedTenure = null, conservativeYears = null) {
            if (!loanTenureChart) initLoanTenureChart();
            loanTenureChart.data.labels = labels;
            loanTenureChart.data.datasets[0].data = data;
            const reqIndex = requestedTenure ? Math.max(0, requestedTenure - 1) : null;
            const consIndex = conservativeYears ? Math.max(0, conservativeYears - 1) : null;


            loanTenureChart.data.datasets = loanTenureChart.data.datasets.filter(ds => !ds.id || !String(ds.id).startsWith('__marker'));

            if (reqIndex !== null && reqIndex < labels.length) {
                const markerVal = data[reqIndex];
                loanTenureChart.data.datasets.push({
                    id: '__marker-request',
                    label: 'Requested Tenure',
                    type: 'scatter',
                    data: [{
                        x: labels[reqIndex],
                        y: markerVal
                    }],
                    backgroundColor: '#10b981',
                    borderColor: '#10b981',
                    pointRadius: 6,
                    showLine: false
                });
            }

            if (consIndex !== null && consIndex < labels.length) {
                const markerVal2 = data[consIndex];
                loanTenureChart.data.datasets.push({
                    id: '__marker-cons',
                    label: 'Conservative Tenure',
                    type: 'scatter',
                    data: [{
                        x: labels[consIndex],
                        y: markerVal2
                    }],
                    backgroundColor: '#f59e0b',
                    borderColor: '#f59e0b',
                    pointRadius: 6,
                    showLine: false
                });
            }

            loanTenureChart.update();
        }


        checkBtn.addEventListener('click', () => {
            const res = calculateEligibility();
            renderReport(res);
        });

        // Recompute chart when inputs change (live update)
        interestRateEl.addEventListener('input', () => {
            const res = calculateEligibility();
            renderReport(res);
        });
        desiredTenureEl.addEventListener('input', () => {
            const res = calculateEligibility();
            renderReport(res);
        });
        borrowersContainer.addEventListener('input', () => {

            const res = calculateEligibility();
            renderReport(res);
        });


        exportBtn.addEventListener('click', async () => {
            const res = calculateEligibility();
            let lines = [];
            lines.push('Loan Eligibility Report - 360 PropGuide');
            lines.push(`Date: ${new Date().toLocaleString()}`);
            lines.push('');
            res.borrowers.forEach((b, i) => {
                lines.push(`Borrower ${i+1}: Occupation=${b.occupation}, Age=${b.age}, Income=₹${b.income}, Existing=₹${b.existing}`);
                lines.push(`  FOIR=${(b.foir*100).toFixed(0)}%  Eligible EMI=₹${b.maxEmiAllowed.toFixed(2)}  MaxTenureMonths=${b.maxTenureMonths}`);
            });
            lines.push('');
            lines.push(`Combined Eligible EMI: ₹${res.combinedEligibleEmi.toFixed(2)}`);
            lines.push(`Loan at requested tenure (${res.tenureYearsRequested} yrs, ${res.interestAnnual}%): ₹${res.combinedLoanAtRequestedTenure.toFixed(0)}`);
            lines.push(`Conservative loan (min effective tenure): ₹${res.loanConservative.toFixed(0)}`);
            lines.push(`Affordability Score: ${res.affordabilityScore}/100`);
            const text = lines.join('\n');

            try {
                await navigator.clipboard.writeText(text);
                exportBtn.textContent = 'Copied';
                setTimeout(() => exportBtn.textContent = 'Export Report', 1200);
            } catch (e) {
                const blob = new Blob([text], {
                    type: 'text/plain;charset=utf-8'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'loan-eligibility-report.txt';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            }
        });

        reportEl.innerHTML = '<div class="text-muted">Fill inputs and click <strong>Check Eligibility</strong>.</div>';
        initLoanTenureChart();

    });
</script>
@endsection