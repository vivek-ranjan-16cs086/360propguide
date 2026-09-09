@extends('frontend.layouts.app')

@section('title', "EMI Calculator | Calculate Home Loan EMI Instantly Online")
@section('description',"Use our EMI Calculator to check monthly home loan EMI instantly. Enter loan amount, interest rate and tenure to plan budgets smartly for property buying.")
@section('keywords', "360 PropGuide, Real Estate Services, Delhi NCR Properties, Real Estate, real estate company, noida property, real estate company in noida, property greater noida, property in greater noida west, property in gr noida")
@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/css/tools/emi.css')}}">
@endsection
@section('content')


<section class="emi-us p-4">
    <div class="emi container">

        <!-- HEADER -->
        <div class="header d-flex justify-content-between align-items-center mb-4">
            <h1>Loan Calculator</h1>
        </div>

        <div class="row">

            <!-- LEFT SIDE CONTROLS -->
            <div class="col-lg-5 mb-4">
                <div class="view p-3">
                    <div class="details">

                        <!-- AMOUNT -->
                        <div class="amount mb-3">
                            <label class="form-label">Amount</label>
                            <p id="loan-amt-text" class="fw-bold text-primary"></p>

                            <input type="number" id="loan-amt-input" class="form-control"
                                value="500000" min="0" max="10000000" step="1000">

                            <div class="d-flex justify-content-between mt-1">
                                <span>0</span><span>1 Cr</span>
                            </div>

                            <input type="range" id="loan-amount" class="form-range"
                                min="0" max="10000000" step="1000" value="500000">
                        </div>

                        <!-- TENURE -->
                        <div class="tenure mb-3">
                            <label class="form-label">Tenure (Years)</label>
                            <p id="loan-period-text" class="fw-bold text-primary"></p>

                            <input type="number" id="loan-period-input" class="form-control"
                                value="10" min="1" max="30">

                            <div class="d-flex justify-content-between mt-1">
                                <span>1</span><span>30</span>
                            </div>

                            <input type="range" id="loan-period" class="form-range"
                                min="1" max="30" step="1" value="10">
                        </div>

                        <!-- INTEREST -->
                        <div class="intrest mb-3">
                            <label class="form-label">Interest Rate (%)</label>
                            <p id="interest-rate-text" class="fw-bold text-primary"></p>

                            <input type="number" id="interest-rate-input" class="form-control"
                                value="8.5" min="1" max="15" step="0.1">

                            <div class="d-flex justify-content-between mt-1">
                                <span>1%</span><span>15%</span>
                            </div>

                            <input type="range" id="interest-rate" class="form-range"
                                min="1" max="15" step="0.1" value="8.5">
                        </div>

                    </div>

                    <div class="footer mt-4">
                        <p id="price-container">
                            <span id="price">0</span> /mo
                        </p>
                    </div>
                </div>
            </div>

            <!-- PIE CHART -->
            <div class="col-lg-6 offset-lg-1 mb-4 d-flex align-items-center">
                <div class="w-100">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

        </div>

        <!-- SUMMARY -->
        <div class="summary-box mt-4">
            <div class="item">
                <p>Principal</p>
                <p id="cp"></p>
            </div>

            <div class="item">
                <p>Interest</p>
                <p id="ci"></p>
            </div>

            <div class="item">
                <p>Total Payable</p>
                <p id="ct"></p>
            </div>
        </div>


    </div>
</section>

@endsection



@section('customJS')
<script src="{{ asset('frontend/libraries/chart.umd.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        
        const state = {
            P: 0,
            R: 0,
            N: 0,
            pie: null,
            line: null,
        };
        
        const el = (id) => document.getElementById(id);
        const safe = (id) => {
            const ref = el(id);
            if (!ref) console.error("Element missing:", id);
            return ref;
        };
        
        const loanAmtSlider = safe("loan-amount");
        const loanAmtInput = safe("loan-amt-input");
        const loanAmtText = safe("loan-amt-text");
        
        const rateSlider = safe("interest-rate");
        const rateInput = safe("interest-rate-input");
        const rateText = safe("interest-rate-text");
        
        const periodSlider = safe("loan-period");
        const periodInput = safe("loan-period-input");
        const periodText = safe("loan-period-text");
        
        const cp = safe("cp");
        const ci = safe("ci");
        const ct = safe("ct");
        const price = safe("price");
        
        
        function updateAmount(val) {
            state.P = parseFloat(val);
            loanAmtInput.value = val;
            loanAmtSlider.value = val;
            loanAmtText.textContent = Number(val).toLocaleString("en-IN");
            render();
        }
        
        function updateRate(val) {
            state.R = parseFloat(val);
            rateInput.value = val;
            rateSlider.value = val;
            rateText.textContent = val + "%";
            render();
        }
        
        function updatePeriod(val) {
            state.N = parseFloat(val);
            periodInput.value = val;
            periodSlider.value = val;
            periodText.textContent = val + " years";
            render();
        }
        
        loanAmtSlider.addEventListener("input", e => updateAmount(e.target.value));
        loanAmtInput.addEventListener("input", e => updateAmount(e.target.value));
        
        rateSlider.addEventListener("input", e => updateRate(e.target.value));
        rateInput.addEventListener("input", e => updateRate(e.target.value));
        
        periodSlider.addEventListener("input", e => updatePeriod(e.target.value));
        periodInput.addEventListener("input", e => updatePeriod(e.target.value));
        
        
        
        function calculateBreakdown(P, r, emi) {
            
            let totalInterest = 0;
            let yearlyInterest = [];
            let yearlyPrincipal = [];
            let years = [];
            
            let principalAcc = 0;
            let interestAcc = 0;
            let monthCount = 0;
            let p = P,
            year = 1;
            
            while (p > 0 && year <= 50) {
                const interest = p * r;
                const principalPaid = emi - interest;
                p -= principalPaid;
                
                totalInterest += interest;
                principalAcc += principalPaid;
                interestAcc += interest;
                monthCount++;
                
                if (monthCount === 12) {
                    years.push(year++);
                    yearlyPrincipal.push(Math.floor(principalAcc));
                    yearlyInterest.push(Math.floor(interestAcc));
                    principalAcc = 0;
                    interestAcc = 0;
                    monthCount = 0;
                }
                
                if (p <= 0) break;
            }
            
            return {
                totalInterest,
                yearlyPrincipal,
                yearlyInterest,
                years
            };
        }
        
        function render() {
            
            const {
                P,
                R,
                N
            } = state;
            if (!P || !R || !N) return;
            
            const r = R / 1200;
            const n = N * 12;
            
            
            const emi = Math.round((P * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1));
            
            const breakdown = calculateBreakdown(P, r, emi);
            
            cp.textContent = formatPriceJS(P);
            ci.textContent = formatPriceJS(breakdown.totalInterest);
            ct.textContent = formatPriceJS(P + breakdown.totalInterest);
            price.textContent = formatPriceJS(emi);
            
            state.pie.data.datasets[0].data = [P, breakdown.totalInterest];
            state.pie.update();
            
            state.line.data.labels = breakdown.years;
            state.line.data.datasets[0].data = breakdown.yearlyPrincipal;
            state.line.data.datasets[1].data = breakdown.yearlyInterest;
            ct.textContent = formatPriceJS(P + breakdown.totalInterest);
            state.line.update();
        }
        
        function initCharts() {
            
            state.line = new Chart(el("lineChart"), {
                type: "line",
                data: {
                    labels: [],
                    datasets: [{
                        label: "Principal",
                        borderColor: "#0b3e64",
                        data: []
                    },
                    {
                        label: "Interest",
                        borderColor: "#f97f2f",
                        data: []
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: "Yearly Breakdown"
                    }
                }
            }
        });
        
        state.pie = new Chart(el("pieChart"), {
            type: "doughnut",
            data: {
                labels: ["Principal", "Interest"],
                datasets: [{
                    backgroundColor: ["#0b3e64", "#f97f2f"],
                    data: [0, 0]
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: "Total Split"
                    }
                }
            }
        });
    }
    
    function formatPriceJS(price) {
        if (price < 100000) {
            return (price / 1000).toFixed(2) + " K";
        } else if (price < 10000000) {
            return (price / 100000).toFixed(2) + " Lakh";
        } else {
            return (price / 10000000).toFixed(2) + " Cr";
        }
    }
    
    
    function init() {
        initCharts();
        updateAmount(loanAmtSlider.value);
        updateRate(rateSlider.value);
        updatePeriod(periodSlider.value);
    }
    
    init();
    
});
</script>
@endsection