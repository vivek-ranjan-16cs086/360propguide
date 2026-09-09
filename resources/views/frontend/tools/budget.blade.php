@extends('frontend.layouts.app')
@section('title', "Home Budget Calculator | Plan Your Property Investment Smartly")
@section('description',"Use our Home Budget Calculator to plan your property investment smartly. Estimate affordability, EMI, loan needs, and budget for homes across Delhi NCR.")
@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/css/tools/budget.css')}}">
@endsection

@section('content')
<section class="p-4">
  <div class="container">

    <div class="row align-items-center mb-4">
      <div class="col-md-8">
        <h1 class="mb-0" style="color:#1b5577">Home Budget Calculator</h1>
        <small class="text-muted">Use sliders to estimate maximum affordable home budget.</small>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button id="hb-export" class="btn loginBtn">Export Report</button>
      </div>
    </div>

    <div class="row g-4">
      <!-- Controls -->
      <div class="col-lg-5">
        <div class="card hb-card">

          <div class="mb-3">
            <label class="form-label">Savings / Available Down Payment (₹)</label>
            <div class="d-flex align-items-center gap-2">
              <input id="savings-input" type="number" class="form-control" min="0" step="1000" value="500000">
              <div style="width:120px" class="text-end hb-small">Default: ₹500,000</div>
            </div>
            <input id="savings-range" type="range" class="form-range mt-2 hb-range" min="0" max="5000000" step="1000" value="500000">
          </div>

          <div class="mb-3">
            <label class="form-label">EMI You Can Afford (₹ / month)</label>
            <div class="d-flex align-items-center gap-2">
              <input id="emi-input" type="number" class="form-control" min="1000" step="100" value="25000">
              <div style="width:120px" class="text-end hb-small">Default: ₹25,000</div>
            </div>
            <input id="emi-range" type="range" class="form-range mt-2 hb-range" min="1000" max="200000" step="100" value="25000">
          </div>

          <div class="mb-3">
            <label class="form-label">Loan Tenure (years)</label>
            <div class="d-flex align-items-center gap-2">
              <input id="tenure-input" type="number" class="form-control" min="1" max="40" step="1" value="20">
              <div style="width:120px" class="text-end hb-small">Default: 20 yrs</div>
            </div>
            <input id="tenure-range" type="range" class="form-range mt-2 hb-range" min="1" max="40" step="1" value="20">
          </div>

          <div class="mb-3">
            <label class="form-label">Interest Rate (annual %)</label>
            <div class="d-flex align-items-center gap-2">
              <input id="rate-input" type="number" class="form-control" min="1" max="20" step="0.01" value="8.75">
              <div style="width:120px" class="text-end hb-small">Fixed by you</div>
            </div>
            <input id="rate-range" type="range" class="form-range mt-2 hb-range" min="1" max="20" step="0.01" value="8.75">
          </div>

          <div class="d-grid gap-2 mt-3">
            <button id="hb-calc" class="btn loginBtn">Estimate Budget</button>
            <button id="hb-reset" class="btn btn-outline-secondary">Reset Defaults</button>
          </div>
        </div>
      </div>

      <!-- Results -->
      <div class="col-lg-7">
        <div class="card hb-card">

          <div class="row align-items-center">
            <div class="col-md-7">
              <h5 style="color:#1b5577">Estimated Budget</h5>
              <p class="hb-small mb-0">Based on your affordable EMI, tenure and interest rate.</p>
            </div>
            <div class="col-md-5 text-md-end">
              <div class="hb-small">Monthly EMI (input)</div>
              <div id="emi-display" class="hb-value">₹ 25,000</div>
            </div>
          </div>

          <div class="row g-3 mt-3">
            <div class="col-md-6">
              <div class="hb-result">
                <div class="hb-small">Maximum Loan</div>
                <div id="max-loan" class="hb-value">₹ 0</div>
                <div class="hb-small">(loan from EMI, tenure & rate)</div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="hb-result">
                <div class="hb-small">Total Property Budget</div>
                <div id="total-budget" class="hb-value">₹ 0</div>
                <div class="hb-small">Loan + Down payment</div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="hb-result">
                <div class="hb-small">Total Payable (loan)</div>
                <div id="total-payable" class="hb-value">₹ 0</div>
                <div class="hb-small">Principal + interest over tenure</div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="hb-result">
                <div class="hb-small">Total Interest Paid</div>
                <div id="total-interest" class="hb-value">₹ 0</div>
                <div class="hb-small">Over loan tenure</div>
              </div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-6">
              <canvas id="budgetDoughnut"></canvas>
            </div>
            <div class="col-md-6">
              <div class="hb-small">Quick notes</div>
              <ul class="small text-muted">
                <li>EMI used to compute maximum loan: the higher the tenure, the larger the loan for the same EMI.</li>
                <li>Property budget = Loan + Available savings (down payment).</li>
                <li>These are estimates; for formal pre-approval consult a lender.</li>
              </ul>
            </div>
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

    // Elements
    const savingsInput = document.getElementById('savings-input');
    const savingsRange = document.getElementById('savings-range');

    const emiInput = document.getElementById('emi-input');
    const emiRange = document.getElementById('emi-range');

    const tenureInput = document.getElementById('tenure-input');
    const tenureRange = document.getElementById('tenure-range');

    const rateInput = document.getElementById('rate-input');
    const rateRange = document.getElementById('rate-range');

    const calcBtn = document.getElementById('hb-calc');
    const resetBtn = document.getElementById('hb-reset');
    const exportBtn = document.getElementById('hb-export');

    const emiDisplay = document.getElementById('emi-display');
    const maxLoanEl = document.getElementById('max-loan');
    const totalBudgetEl = document.getElementById('total-budget');
    const totalPayableEl = document.getElementById('total-payable');
    const totalInterestEl = document.getElementById('total-interest');

    // Chart
    const doughnutCtx = document.getElementById('budgetDoughnut').getContext('2d');
    let doughnut = null;

    // Helpers
    function toINR(n, frac = 0) {
      return '₹ ' + Number(n).toLocaleString('en-IN', {
        maximumFractionDigits: frac
      });
    }

    function monthlyRate(annualPercent) {
      return Number(annualPercent) / 1200;
    }

    function loanForEmi(emi, r, n) {
      if (r === 0) return emi * n;
      const pow = Math.pow(1 + r, n);
      const factor = (pow - 1) / (r * pow);
      return emi * factor;
    }

    // Keep inputs in sync with ranges
    function syncPair(inputEl, rangeEl) {
      inputEl.addEventListener('input', () => {
        rangeEl.value = inputEl.value;
        computeAndRender();
      });
      rangeEl.addEventListener('input', () => {
        inputEl.value = rangeEl.value;
        computeAndRender();
      });
    }

    syncPair(savingsInput, savingsRange);
    syncPair(emiInput, emiRange);
    syncPair(tenureInput, tenureRange);
    syncPair(rateInput, rateRange);

    // Core compute + render
    function computeAndRender() {
      // read values
      const savings = Number(savingsInput.value) || 0;
      const emi = Number(emiInput.value) || 0;
      const tenureYears = Math.max(1, Math.min(40, Number(tenureInput.value) || 20));
      const rateAnnual = Number(rateInput.value) || 8.75;

      emiDisplay.textContent = toINR(emi, 0);

      const rMonthly = monthlyRate(rateAnnual);
      const nMonths = Math.round(tenureYears * 12);

      // compute max loan supported by EMI
      const maxLoan = loanForEmi(emi, rMonthly, nMonths);

      // totals
      const totalPayable = emi * nMonths; // total paid over tenure
      const totalInterest = totalPayable - maxLoan;

      const totalBudget = Math.max(0, maxLoan + savings);

      // render numbers
      maxLoanEl.textContent = toINR(Math.round(maxLoan), 0);
      totalBudgetEl.textContent = toINR(Math.round(totalBudget), 0);
      totalPayableEl.textContent = toINR(Math.round(totalPayable), 0);
      totalInterestEl.textContent = toINR(Math.round(totalInterest), 0);

      // update doughnut chart: Loan vs Down payment
      const loanNum = Math.round(maxLoan);
      const downNum = Math.round(savings);
      updateDoughnut(['Loan', 'Down payment'], [loanNum, downNum]);
    }

    // Chart init / update
    function initDoughnut() {
      doughnut = new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
          labels: [],
          datasets: [{
            data: [],
            backgroundColor: ['#1b5577', '#F59E0B']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: (ctx) => `${ctx.label}: ${toINR(ctx.parsed) }`
              }
            }
          }
        }
      });
    }

    function updateDoughnut(labels, data) {
      if (!doughnut) initDoughnut();
      doughnut.data.labels = labels;
      doughnut.data.datasets[0].data = data;
      doughnut.update();
    }

    // Export (copy report)
    exportBtn.addEventListener('click', async () => {
      const savings = Number(savingsInput.value) || 0;
      const emi = Number(emiInput.value) || 0;
      const tenureYears = Math.max(1, Math.min(40, Number(tenureInput.value) || 20));
      const rateAnnual = Number(rateInput.value) || 8.75;
      const rMonthly = monthlyRate(rateAnnual);
      const nMonths = Math.round(tenureYears * 12);
      const maxLoan = loanForEmi(emi, rMonthly, nMonths);
      const totalPayable = emi * nMonths;
      const totalInterest = totalPayable - maxLoan;
      const totalBudget = Math.max(0, maxLoan + savings);

      const lines = [
        'Home Budget Estimate - 360 PropGuide',
        `Date: ${new Date().toLocaleString()}`,
        '',
        `Savings (down payment): ${toINR(savings)}`,
        `Affordable EMI: ${toINR(emi)} / month`,
        `Tenure: ${tenureYears} years`,
        `Interest rate: ${rateAnnual}% (annual)`,
        '',
        `Estimated maximum loan: ${toINR(Math.round(maxLoan))}`,
        `Total payable (over tenure): ${toINR(Math.round(totalPayable))}`,
        `Total interest (over tenure): ${toINR(Math.round(totalInterest))}`,
        `Estimated total property budget (loan + down payment): ${toINR(Math.round(totalBudget))}`
      ];

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
        a.download = 'home-budget-estimate.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
      }
    });

    // Reset defaults
    resetBtn.addEventListener('click', () => {
      savingsInput.value = 500000;
      savingsRange.value = 500000;
      emiInput.value = 25000;
      emiRange.value = 25000;
      tenureInput.value = 20;
      tenureRange.value = 20;
      rateInput.value = 8.75;
      rateRange.value = 8.75;
      computeAndRender();
    });

    // init
    computeAndRender();

  });
</script>
@endsection