@extends('frontend.layouts.app')

@section('title', 'Login with OTP')
@section('customCSS')
    <link rel="stylesheet" href="{{asset('frontend/css/login.css')}}">
@endsection
@section('content')
    
    <section class="backimgof360Login mobile-popup-wrapper"> 
        <div class="container pt-md-5">
            <div class="divimg mt-2 d-flex d-md-none justify-content-center align-items-center flex-column">
                <div class="LoginHeadTxt">Sell or Rent Property</div>
                <div class="LoginHeadTxt2">online faster with 360 propguide.</div>
            </div>
            <div class="text-center fw-bold text-rotator d-md-none">
                <div class="fortxt">
                    <div class="d-flex gap-2 justify-content-center">
                        <img src="./assets/images/check-mark.png" width="20" class="mt-1" alt="">
                        <div class="fs-5">Advertise for Free</div>
                    </div>
                </div>
                <div class="fortxt">
                    <div class="d-flex gap-2 justify-content-center">
                        <img src="./assets/images/check-mark.png" width="20" class="mt-1" alt="">
                        <div class="fs-5">Get Unlimited Enquiries</div>
                    </div>
                </div>
                <div class="fortxt">
                    <div class="d-flex gap-2 justify-content-center">
                        <img src="./assets/images/check-mark.png" width="20" class="mt-1" alt="">
                        <div class="fs-5">Get Shortlisted Buyers & Tenants</div>
                    </div>
                </div>
                <div class="fortxt">
                    <div class="d-flex gap-2 justify-content-center">
                        <img src="./assets/images/check-mark.png" width="20" class="mt-1" alt="">
                        <div class="fs-5">Assistance in Co-ordinating Site Visits</div>
                    </div>
                </div>
            </div>


            <div class="row pt-md-5">
                <div class="col-lg-7 mt-4 endcol">
                    <div class="fs-2 fw-bold">Sell or Rent Property</div>
                    <div class="fs-2 fw-bold">online faster with 360 propguide.</div>
                    <div class="mt-2">

                    </div>
                </div>
                <div class="col-lg-4 mt-3 ms-auto">
                    <div class="py-4 px-5  pb-5 bg-light shadow-lg rounded-5">
                        <div class="fw-bold LoginFormText ">Start Posting Your Property, it's Free</div>
                        <div class="  fw-bold mb-2 mt-1 mb-3 LoginFormText  ">Please login :- </div>
                        <div id="otp-alert" class="alert d-none"></div>
                        <form method="POST" action="{{ route('frontend.otp.verify') }}" id="otp-login-form">
                            @csrf
                            <label for="phone" class="mb-1 text-secondary fw-bold">Phone Number*</label>
							<a href="javascript:void(0)" class="edit d-none  btn text-primary text-decoration-underline float-end" id="edit">Edit</a>
							<input type="tel" id="phone" name="phone" maxlength="10" class="form-control p-2 loginInput36o" pattern="\d{10}" placeholder="Enter Your number" required>

                            <div class="d-none" id="otp-section">
                                <label for="otp" class="mt-2 mb-1 text-secondary fw-bold">Enter OTP</label>
								<input type="text" name="otp" id="otp" placeholder="Enter 6-digit OTP" class="form-control p-2 loginInput36o" maxlength="6" required>

                            </div>
                            <button class="py-2 px-5 form-control mt-4 text-light bg-success send-otp-btn"
                                id="send-otp-btn">Send
                                OTP</button>
                            <button type="submit" class="py-2 px-5 form-control mt-3 text-light bg-success d-none"
                                id="verify-btn">Verify OTP</button>
<div id="otp-timer" class="text-danger fw-bold text-center mt-2"></div>

                            <div class="text-center mt-2 "> <button
                                    class=" bg-transparent border-0 text-decoration-none d-none resend-otp-btn">Resend OTP</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJS')
    <script>
	let otpCountdownInterval; 
	document.getElementById('phone').addEventListener('input', function (e) {
    this.value = this.value.replace(/\D/g, '').slice(0, 10); // Remove non-digits and limit to 10 digits
});
document.getElementById('otp').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 6); // Only digits, max 6
});


document.addEventListener('DOMContentLoaded', function () {
    const sendBtn = document.getElementById('send-otp-btn');
    const resendBtn = document.querySelector('.resend-otp-btn');
    const verifyBtn = document.getElementById('verify-btn');
    const otpSection = document.getElementById('otp-section');
    const alertBox = document.getElementById('otp-alert');
    const phoneInput = document.getElementById('phone');
    const form = document.getElementById('otp-login-form');
    const edit = document.getElementById('edit');
	const timerElement = document.getElementById('otp-timer');

	edit.addEventListener('click', function () {
		clearInterval(otpCountdownInterval);
		deleteCookie('otp_phone');
        deleteCookie('otp_expiry');
		alertBox.classList.add('d-none');
		otpSection.classList.add('d-none');
		verifyBtn.classList.add('d-none');
		resendBtn.classList.add('d-none');
		this.classList.add('d-none');
		sendBtn.classList.remove('d-none');
		sendBtn.disabled = false;
		resendBtn.disabled = false;
		phoneInput.disabled = false;
		timerElement.textContent ="";
	})
	const storedPhone = getCookie('otp_phone');
            if (storedPhone) {
                phoneInput.value = storedPhone;
                otpSection.classList.remove('d-none');
                verifyBtn.classList.remove('d-none');
                resendBtn.classList.remove('d-none');
                sendBtn.classList.add('d-none');
				edit.classList.remove('d-none');
				phoneInput.disabled = true;
				resendBtn.disabled = true;
				startOtpCountdown()
            }
    // Prevent form from submitting on pressing Enter
   function sendOtp(e, isResend = false) {
    if (e) e.preventDefault(); // Only if called from an event

    const phone = phoneInput.value.trim();

    if (!/^\d{10}$/.test(phone)) {
        showAlert('Enter a valid 10-digit phone number.', 'danger');
        return;
    }

    // OTP AJAX Send
    sendBtn.disabled = true;
    phoneInput.disabled = true;
    const originalText = sendBtn.textContent;
    sendBtn.textContent = isResend ? 'Resending...' : 'Sending...';

    fetch('{{ route('frontend.otp.send') }}', {
        method: 'POST',
		credentials: 'same-origin',
        headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
		},

        body: JSON.stringify({ phone })
		//body: JSON.stringify({ phone_number: phone, app_name: '360propguide' })		
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status) {
            const message = isResend
				? 'OTP resent successfully to your phone number.'
				: 'OTP sent successfully to your phone number.';
			showAlert(message, 'success'); // ✅ green message

            showAlert(message, 'success');
            otpSection.classList.remove('d-none');
            verifyBtn.classList.remove('d-none');
            resendBtn.classList.remove('d-none');
            edit.classList.remove('d-none');
            sendBtn.classList.add('d-none');
            setCookie('otp_phone', phone, 5);
			setCookie('otp_expiry', Date.now() + 5 * 60 * 1000, 5);
			startOtpCountdown();
        } else {
            phoneInput.disabled = false;
            showAlert(data.message || 'Something went wrong.', 'danger');
        }
    })
    .catch(() => {
        phoneInput.disabled = false;
        showAlert('Failed to send OTP. Try again.', 'danger');
    })
    .finally(() => {
        sendBtn.disabled = false;
        sendBtn.textContent = originalText;
    });
}

// Handle sendBtn click (normal send)
sendBtn.addEventListener('click', function (e) {
    sendOtp(e, false);
});

// Handle resendBtn click (resend logic)
resendBtn.addEventListener('click', function (e) {
	resendBtn.disabled = true;
    sendOtp(e, true);
});

// Handle OTP verify button
document.getElementById('verify-btn').addEventListener('click', function (e) {
    e.preventDefault();
	verifyBtn.disabled = true;
    const phone = document.getElementById('phone').value.trim();
    const otp = document.getElementById('otp').value.trim();

    if (!/^\d{10}$/.test(phone)) {
        showAlert('Enter a valid 10-digit phone number.', 'danger');
		verifyBtn.disabled = false;
        return;
    }

    if (!/^\d{6}$/.test(otp)) {
        showAlert('Enter a valid 6-digit OTP.', 'danger');
		verifyBtn.disabled = false;
        return;
    }

    this.disabled = true;
    const originalText = this.textContent;
    this.textContent = 'Verifying...';

    fetch('{{ route('frontend.otp.verify') }}', {
        method: 'POST',
		credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
          body: JSON.stringify({ phone, otp })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status) {
            showAlert(data.message || 'Login successful!', 'success');
			deleteCookie('otp_phone');
            // Optional: redirect after success
            setTimeout(() => window.location.href = data.redirect || '/', 1000);
        } else {
            showAlert(data.message || 'Invalid OTP.', 'danger');
			verifyBtn.disabled = false;
        }
    })
    .catch(() => {
        showAlert('Verification failed. Try again.', 'danger');
		verifyBtn.disabled = false;
    })
});


function startOtpCountdown() {
    const expiry = parseInt(getCookie('otp_expiry'), 10);

    // Clear any existing interval first
    if (otpCountdownInterval) {
        clearInterval(otpCountdownInterval);
    }

    if (!expiry || Date.now() > expiry) {
        timerElement.textContent = 'OTP expired, please resend';
        showAlert('OTP expired. Please resend.', 'danger');
        deleteCookie('otp_phone');
        deleteCookie('otp_expiry');
        return;
    }

    otpCountdownInterval = setInterval(() => {
        const remaining = expiry - Date.now();

        if (remaining <= 0) {
            clearInterval(otpCountdownInterval);
            timerElement.textContent = 'OTP expired, please resend';
            showAlert('OTP expired. Please resend.', 'danger');
            deleteCookie('otp_phone');
            deleteCookie('otp_expiry');
			resendBtn.disabled = false;
            return;
        }

        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000).toString().padStart(2, '0');
        timerElement.textContent = `OTP will expire in: ${minutes}:${seconds}`;
    }, 1000);
}



    function showAlert(message, type) {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    }

    function setCookie(name, value, minutes) {
        const expires = new Date(Date.now() + minutes * 60 * 1000).toUTCString();
        document.cookie = `${name}=${value}; expires=${expires}; path=/`;
    }

    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }
	function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}
});
</script>




@endsection
