<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-PWVX37VL');</script>
	<!-- End Google Tag Manager -->
  @yield('priority')
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    @yield('title', '360 PropGuide | Trusted Real Estate Services in
    Delhi/NCR')
  </title>
  <meta name="description"
    content="@yield('description', '360 PropGuide Real Estate Services was established in 2018 to bring world-class & trusted property solutions to the Delhi/NCR market.')">
  <meta name="keywords"
    content="@yield('keywords', '360 PropGuide,Real Estate Services,Delhi NCR Properties,Property Consultants Delhi NCR,Buy Sell Rent Properties,Real Estate Experts NCR,Delhi NCR Real Estate Guide')">

  <!-- Include additional CSS files here -->
  <link rel="canonical" href="@yield('canonical','https://360propguide.com/')">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="robots" content="max-image-preview:large">
  <link rel="stylesheet" href="{{url('frontend/libraries/bootstrap.min.css')}}">

  <link rel="stylesheet" href="{{url('frontend/libraries/fontawesome/css/all.min.css')}}" media="print"
    onload="this.media='all'">

  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
    media="print" onload="this.media='all'">
  @unless(View::hasSection('skipAos'))
  <link rel="stylesheet" href="{{url('frontend/libraries/aos-master/dist/aos.css')}}" media="print"
    onload="this.media='all'">
  @endunless

  <link rel="stylesheet" href="{{url('frontend/libraries/swiper.css')}}">

  <link rel="stylesheet"
    href="{{ url('frontend/css/header1.css') }}?v={{ @filemtime(public_path('frontend/css/header1.css')) ?: time() }}">

  @unless(View::hasSection('skipPropertiesCss'))
  <link rel="stylesheet"
    href="{{ url('frontend/css/properties.css') }}?v={{ @filemtime(public_path('frontend/css/properties.css')) ?: time() }}">
  @endunless
  <link rel="stylesheet"
    href="{{ url('frontend/main.css') }}?v={{ @filemtime(public_path('frontend/main.css')) ?: time() }}">

  <link rel="stylesheet"
    href="{{ url('frontend/style.css') }}?v={{ @filemtime(public_path('frontend/style.css')) ?: time() }}">
  <noscript>
    <link rel="stylesheet" href="{{ url('frontend/css/header1.css') }}">
    <link rel="stylesheet" href="{{ url('frontend/css/properties.css') }}">
    <link rel="stylesheet" href="{{ url('frontend/main.css') }}">
    <link rel="stylesheet" href="{{ url('frontend/style.css') }}">
  </noscript>
  <!-- Favicon for all browsers -->
  <link rel="icon" href="{{url('favicon.ico')}}" type="image/x-icon">
  <link rel="shortcut icon" href="{{url('favicon.ico')}}" type="image/x-icon">

  <!-- PNG versions -->
  <link rel="icon" type="image/png" sizes="16x16" href="{{url('favicon-16x16.png')}}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{url('favicon-32x32.png')}}">

  <link rel="icon" type="image/png" sizes="192x192" href="{{url('favicon-192x192.png')}}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


  <!-- custom page css -->
  @yield('customCSS')
  @stack('schema')

  @if (!View::hasSection('schema') && empty($faqSchema))
  {{-- Default fallback FAQ schema --}}

  <script defer type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "360 PropGuide",
      "image": "https://www.360propguide.com/frontend/360%20logo-01.png",
      "@id": "",
      "url": "https://www.360propguide.com/",
      "telephone": "+91 96430 20020",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Plot No. 694M, Sector 107",
        "addressLocality": "Noida, Uttar Pradesh",
        "postalCode": "201301",
        "addressCountry": "IN"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
        ],
        "opens": "09:00",
        "closes": "06:00"
      }
    }
  </script>
  @endif
  <meta property="og:title" content="@yield('title', '360 PropGuide')">
  <meta property="og:site_name" content="360 PropGuide">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:description"
    content="@yield('description', '360 PropGuide is one of the popular real estate companies in Noida. It has earned a name for delivering authentic and comprehensive property solutions. The company takes care to satisfy its customers. It offers numerous services like buying, selling, and leasing properties to facilitate a hassle-free experience for the clients. Their skilled team focuses on helping clients at every stage of the real estate process, making them a reliable partner in the busy Noida property market.')">
  <meta property="og:type" content="website">
  <meta property="og:image" content="@yield('og_image', url('frontend/favicon.jpg'))">

  <!-- Marketing tags load after the first real interaction, outside initial rendering. -->
  <script>
    
  </script>

</head>

<body class="{{ request()->routeIs('frontend.project-details1') ? 'hide-floating-buttons' : '' }}">
	 <!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PWVX37VL"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	@include('frontend.layouts.header1')
	@yield('content')

	@if (!isset($hideCustomLinks))
	@include('frontend.partials.custom-links')
	@endif

  <footer>@include('frontend.layouts.footer')</footer>


  <div id="cookieConsentPopup" class="cookie-consent-popup shadow-lg">
    <p class="mb-3">We use cookies to ensure you get the best experience on our website.
      <a href="{{url('privacy')}}">Privacy Policy</a>.
    </p>
    <button id="acceptCookies" class="customBtn btn me-2">I Agree</button>
    <button id="declineCookies" class="customBtn btn">Decline</button>
  </div>
  @php
  // Default message
  $whatsappMessage = 'I want to know more about your projects';

  // If $projects exists, use its name
  if(isset($projects) && !empty($projects->project_name)) {
  $whatsappMessage = 'I want to know more about ' . $projects->project_name;
  }
  @endphp
  <!--Popup form -->

  <div class="modal fade" id="contactModalPopup" tabindex="-1" aria-hidden="true" aria-labelledby="contactModalTitle">
    <div class="modal-dialog popupFormHome">
      <div class="modal-content p-3">
        <div class="modal-header border-0">
          <img src="{{asset('frontend/360logo.webp')}}" alt="360 PropGuide" class="mx-auto popup-logo-img" width="180"
            height="70" style="max-width: 180px; height: auto; object-fit: contain; display: block;" loading="lazy"
            decoding="async">

          <button type="button" class="btn-close align-self-start ms-0 shadow-none" data-bs-dismiss="modal"
            aria-label="Close"></button>
        </div>
        <h5 class="text-center" id="contactModalTitle">Exclusive Property Deals - Enquire Today!</h5>
        <div class="modal-body form-wrapper">
          <div class="alert alert-success success-message d-none">
            Your enquiry has been submitted successfully.
          </div>
          <form method="POST" class="popupForm" action="{{route('contact-mail')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="formName" value="popup">
            <div class="mb-3">

              <input id="name" type="text" class="form-control shadow-none name" name="name" placeholder="Name*">
              <span class="text-danger error-name"></span>
            </div>
            <div class="mb-3">

              <input id="mobile" type="tel" class="form-control shadow-none mobile" name="mobile" placeholder="Mobile*">
              <span class="text-danger error-mobile"></span>
            </div>
            <div class="mb-3">

              <input id="email" type="email" class="form-control shadow-none email" name="email" placeholder="Email*">
              <span class="text-danger error-email"></span>
            </div>
            <div class="mb-3">

              <textarea name="message" id="message" class="form-control shadow-none  " placeholder="Message"></textarea>

            </div>
            <div class="mb-3">
              <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
              <span class="text-danger error-recaptcha"></span>
            </div>
            @if ($errors->has('recaptchaform2'))
            <div class="alert alert-danger">
              {{ $errors->first('recaptchaform2') }}
            </div>
            @endif
            <button type="submit" class="btn customBtn text-white mb-3 w-100 submitButton">Submit</button>
          </form>
          <div class="w-full d-flex justify-content-between gap-2">
            <!-- Submit Button (left) -->

            <a href="tel:+919643020020" class="btn customBtn orange text-white w-50"><i
                class="fas fa-phone me-2"></i>Call Us</a>

            <!-- WhatsApp Button (right) -->
            <a href="https://wa.me/919643020020?text={{ urlencode($whatsappMessage) }}" target="_blank"
              class=" whatsapp text-white w-50 d-flex justify-content-center align-items-center">
              <i class="fab fa-whatsapp me-2"></i> WhatsApp
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!--End Popup form -->

  <!-- <div id="contact-icon"
    class="position-fixed d-flex justify-content-evenly shadow-lg w-fit p-3 rounded-pill bg-white custom"> -->
  <!--Phone link -->
  <!-- <a aria-label="phone link" class="d-flex justify-content-center align-items-center px-2" href="tel:+919643020020"
      target="_blank">
      <svg class="text-success" width="50" height="30" viewBox="17.052 7.009 15.938 16.944"
        xmlns="http://www.w3.org/2000/svg" fill="currentColor">
        <g>
          <path
            d="M21.886 7.46c-.265-.483-.827-.599-1.271-.247l-1.74 1.38c-2.164 1.718-2.44 4.79-.618 6.86l5.96 6.772c1.822 2.07 5.011 2.316 7.13.542l1.313-1.1c.428-.357.444-.946.028-1.322l-2.904-2.63c-.412-.373-1.105-.404-1.54-.076l-1.714 1.296-4.843-5.353 1.743-1.259c.446-.321.595-.97.327-1.457l-1.87-3.406z"
            fill-rule="evenodd"></path>
        </g>
      </svg>
    </a> -->

  <!--WhatsApp link -->
  <!-- <a aria-label="whatsapp link" class="d-flex justify-content-center align-items-center border-start border-gray px-2 whatsappgtm"
      href="https://wa.me/+919643020020?text={{ urlencode($whatsappMessage) }}" target="_blank" id="whatsapp-btn-1">
      <svg class="text-success" width="50" height="30" viewBox="15 5 19.904 20" xmlns="http://www.w3.org/2000/svg"
        fill="currentColor">
        <g>
          <path
            d="M29.512 16.985c-.248-.124-1.465-.723-1.692-.806-.227-.082-.392-.123-.557.124-.165.248-.64.806-.784.971-.145.166-.29.186-.537.062-.247-.124-1.045-.385-1.991-1.229-.736-.656-1.233-1.467-1.378-1.715-.144-.248-.015-.382.109-.506.111-.11.248-.289.371-.433.124-.145.165-.248.248-.414.082-.165.041-.31-.02-.433-.063-.124-.558-1.343-.764-1.84-.201-.482-.405-.416-.557-.424a9.932 9.932 0 0 0-.475-.009.91.91 0 0 0-.66.31c-.227.248-.867.847-.867 2.066 0 1.219.887 2.396 1.011 2.561.124.166 1.746 2.667 4.23 3.74.591.255 1.053.407 1.412.521.593.189 1.133.162 1.56.098.476-.07 1.465-.599 1.671-1.177.207-.579.207-1.074.145-1.178-.062-.103-.227-.165-.475-.289m-4.518 6.17h-.004a8.225 8.225 0 0 1-4.192-1.149l-.3-.178-3.118.817.832-3.04-.196-.31a8.218 8.218 0 0 1-1.26-4.385c.002-4.541 3.697-8.236 8.241-8.236 2.2 0 4.268.859 5.824 2.416a8.188 8.188 0 0 1 2.41 5.827c-.002 4.542-3.697 8.237-8.237 8.237m7.01-15.248A9.846 9.846 0 0 0 24.994 5c-5.463 0-9.909 4.446-9.91 9.91a9.879 9.879 0 0 0 1.322 4.954L15 25l5.254-1.378a9.902 9.902 0 0 0 4.736 1.206h.004c5.462 0 9.908-4.446 9.91-9.91a9.851 9.851 0 0 0-2.9-7.012"
            fill-rule="evenodd"></path>
        </g>
      </svg>
    </a>
  </div> -->

  <i class="fa fa-arrow-up" id="scrollToTopBtn" role="button" tabindex="0" aria-label="Scroll to top"></i>
  <script src="{{url('frontend/libraries/bootstrap.bundle.min.js')}}"></script>
  <script src="{{url('frontend/libraries/swiper.js')}}"></script>
  <script src="{{url('frontend/libraries/jquery.min.js')}}"></script>
  @unless(View::hasSection('skipAos'))
  <script defer src="{{url('frontend/libraries/aos-master/dist/aos.js')}}"></script>
  @endunless
  @if(View::hasSection('usesSweetAlert'))
  <script defer src="{{url('frontend/libraries/sweetalert.js')}}"></script>
  @endif
  @include('frontend.layouts.sidebar')
  <script type="module">
    window.enablePushNotifications = async function () {
      if (!('Notification' in window) || !('serviceWorker' in navigator)) return;
      if (Notification.permission !== 'granted') return;

      const [{ initializeApp }, { getMessaging, getToken }] = await Promise.all([
        import('https://www.gstatic.com/firebasejs/11.9.1/firebase-app.js'),
        import('https://www.gstatic.com/firebasejs/11.9.1/firebase-messaging.js')
      ]);
      const app = initializeApp({
        apiKey: 'AIzaSyA9m5_wyKiGP24zAWCBAqBlbB1oQegRL1I',
        authDomain: 'propguide-2512a.firebaseapp.com',
        projectId: 'propguide-2512a',
        storageBucket: 'propguide-2512a.firebasestorage.app',
        messagingSenderId: '450958899567',
        appId: '1:450958899567:web:dfc995802095ff861c509a'
      });
      const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js?v=4');
      const token = await getToken(getMessaging(app), {
        vapidKey: 'BLl22qeAh6ZP6JBwROpX3GPJtC1-XWhvKxlfSc8jytwKWGKNb98_1R_Z3YBFCyRfqLX6h4clgdx-zfAJ5McUa1I',
        serviceWorkerRegistration: registration
      });
      if (!token) return;

      await fetch('/save-fcm-token', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ token })
      });
    };

    if ('Notification' in window && Notification.permission === 'granted') {
      window.addEventListener('pointerdown', function () {
        window.enablePushNotifications().catch(() => { });
      }, { once: true, passive: true });
    }
  </script>
  <script>
    function loadRecaptcha() {
      if (document.getElementById("recaptchaScript")) return;

      let script = document.createElement("script");
      script.src = "https://www.google.com/recaptcha/api.js";
      script.id = "recaptchaScript";
      script.async = true;
      document.body.appendChild(script);
    }
    document.querySelectorAll("input, textarea").forEach(el => {
      el.addEventListener("focus", loadRecaptcha, { once: true });
    });
    document.addEventListener("DOMContentLoaded", function () {
      // Check if the consent cookie is already stored
      if (!localStorage.getItem('cookieConsent')) {
        // Show the consent popup if consent is not stored
        document.getElementById('cookieConsentPopup').style.display = 'block';
      }

      // Handle consent acceptance
      document.getElementById('acceptCookies').addEventListener('click', function () {
        localStorage.setItem('cookieConsent', 'true');
        document.getElementById('cookieConsentPopup').style.display = 'none';
      });

      // Handle consent decline
      document.getElementById('declineCookies').addEventListener('click', function () {
        localStorage.setItem('cookieConsent', 'false');
        document.getElementById('cookieConsentPopup').style.display = 'none';
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.load-more-btn').forEach(function (button) {
        button.addEventListener('click', function () {
          const type = button.getAttribute('data-target');
          const container = document.getElementById(`${type}-container`);
          const currentPage = parseInt(container.getAttribute('data-page'), 10) || 1;
          const nextPage = currentPage + 1;
          const path = window.location.pathname;

          button.disabled = true;
          button.textContent = 'Loading...';

          fetch(`/api/load-more-links?type=${type}&page=${nextPage}&path=${encodeURIComponent(path)}`)
            .then(res => res.json())
            .then(data => {
              data.links.forEach(link => {
                const div = document.createElement('div');
                div.className = `col-12 col-sm-6 col-md-4 col-lg-3 ${type}-item`;
                div.innerHTML = `<a href="${link.url}" class="custom-link-item"><i class="fa-solid fa-location-arrow link-icon"></i><span class="link-text">${link.text}</span></a>`;
                container.appendChild(div);
              });

              container.setAttribute('data-page', data.currentPage);
              button.disabled = false;
              button.innerHTML = '<i class="fa-solid fa-plus me-1"></i> View More Links';

              if (!data.hasMore) {
                const footer = button.closest('.custom-links-footer');
                if (footer) footer.style.display = 'none';
                else button.style.display = 'none';
              }
            })
            .catch(() => {
              button.disabled = false;
              button.innerHTML = '<i class="fa-solid fa-plus me-1"></i> View More Links';
            });
        });
      });
    });
  </script>
  <script defer>
    function setCookie(name, value, days) {
      var expires = "";
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(";");
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
      }
      return null;
    }

    document.addEventListener('DOMContentLoaded', function () {
      if (!getCookie("cookieForm")) {
        setTimeout(function () {
          var contactModalPopup = new bootstrap.Modal(document.getElementById('contactModalPopup'), {});
          contactModalPopup.show();

          // When user closes the modal, set cookie for 1 day
          var modalEl = document.getElementById('contactModalPopup');
          modalEl.addEventListener('hidden.bs.modal', function () {
            setCookie('cookieForm', 'true', 1);
          });
        }, 10000);
      }
    });

    const closeBtn = document.getElementById('closeBtn');
    const formPopup = document.getElementById('formPopup');

    if (closeBtn && formPopup) {
      closeBtn.addEventListener('click', function () {
        formPopup.style.display = 'none';
      });
    }
  </script>


  <script>
    $(document).ready(function () {
      $(".popupForm").submit(function (e) {
        e.preventDefault();
        let form = $(this);

        form.find(".error-name, .error-email, .error-mobile, .error-recaptcha").text('');
        $('.success-message').addClass('d-none').text('');

        let name = (form.find('[name="name"]').val() || "").trim();
        let email = (form.find('[name="email"]').val() || "").trim();
        let mobile = (form.find('[name="mobile"]').val() || "").trim();
        let recaptcha = (form.find('[name="g-recaptcha-response"]').val() || "").trim();

        let isValid = true;

        if (name === "") {
          form.find(".error-name").text("Name is required.");
          isValid = false;
        }

        if (email === "") {
          form.find(".error-email").text("Email is required.");
          isValid = false;
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
          form.find(".error-email").text("Invalid email format.");
          isValid = false;
        }

        if (mobile === "") {
          form.find(".error-mobile").text("Mobile number is required.");
          isValid = false;
        } else if (!/^\d{10}$/.test(mobile)) {
          form.find(".error-mobile").text("Enter a valid 10-digit mobile number.");
          isValid = false;
        }

        if (form.find('[name="g-recaptcha-response"]').length > 0 && recaptcha === "") {
          form.find(".error-recaptcha").text("Please validate Recaptcha");
          isValid = false;
        }

        if (!isValid) return;

        let formData = new FormData(this);
        //formData.append('g-recaptcha-response', recaptcha);

        let submitButton = form.find('.submitButton');
        submitButton.prop('disabled', true).html('<div class="loader"></div>');

        $.ajax({
          url: "{{ route('contact-mail') }}",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,

          success: function (response) {
            if (response.success) {

              form[0].reset();
              grecaptcha.reset();
              submitButton.prop('disabled', false).html('Submit');

              //  Direct redirect to Thank You page
              window.location.href = response.redirect_url;
            }
          },

          error: function (xhr) {
            submitButton.prop('disabled', false).html('Submit');
            let errors = xhr.responseJSON.errors;
            if (errors) {
              if (errors.name) form.find(".error-name").text(errors.name[0]);
              if (errors.email) form.find(".error-email").text(errors.email[0]);
              if (errors.mobile) form.find(".error-mobile").text(errors.mobile[0]);
              if (errors['g-recaptcha-response']) form.find(".error-recaptcha").text(errors['g-recaptcha-response'][0]);
            }
          }
        });
      });
    });
  </script>
  <script defer>
    document.addEventListener('DOMContentLoaded', function () {
      // Use event delegation to support dynamically loaded images
      document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-existing')) {
          const imagePath = e.target.dataset.path;
          const propertyId = e.target.dataset.propertyId;

          if (confirm('Are you sure you want to delete this image?')) {
            fetch("{{ route('postproperty.image.json.delete') }}", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({
                image: imagePath,
                property_id: propertyId
              })
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  // Remove the parent .position-relative container
                  e.target.closest('.position-relative').remove();
                } else {
                  alert('Failed to delete image.');
                }
              });
          }
        }
      });
    });
  </script>
  <script defer>

    const input = document.getElementById('galleries');
    const dropArea = document.getElementById('drop-area');
    const previewContainer = document.getElementById('preview');
    let selectedFiles = [];

    // Reset previews and selected files on load
    window.addEventListener('DOMContentLoaded', () => {
      previewContainer?.innerHTML = '';
      selectedFiles = [];
    });

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
      });
    });

    // Highlight on drag
    ['dragenter', 'dragover'].forEach(eventName => {
      dropArea.addEventListener(eventName, () => dropArea.classList.add('border-primary'));
    });
    ['dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, () => dropArea.classList.remove('border-primary'));
    });

    // Handle drop
    dropArea.addEventListener('drop', (e) => {
      const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
      selectedFiles.push(...files);
      updateFileInput();
      renderPreviews();
    });

    // Handle file input change
    input.addEventListener('change', (e) => {
      const files = Array.from(e.target.files).filter(file => file.type.startsWith('image/'));
      selectedFiles.push(...files);
      updateFileInput();
      renderPreviews();
    });

    // Update input's file list to include selected files
    function updateFileInput() {
      const dataTransfer = new DataTransfer();
      selectedFiles.forEach(file => dataTransfer.items.add(file));
      input.files = dataTransfer.files;
    }

    // Render image previews
    function renderPreviews() {
      previewContainer.innerHTML = '';

      selectedFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const div = document.createElement('div');
          div.className = 'position-relative mb-3 me-2';
          div.style.width = '120px';
          div.style.height = '120px';
          div.style.display = 'inline-block';

          const img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'w-100 h-100 object-fit-contain border rounded';

          const closeBtn = document.createElement('span');
          closeBtn.innerHTML = '&times;';
          closeBtn.className = 'position-absolute top-0 end-0 bg-danger text-white rounded-circle small imageX';
          closeBtn.style.cursor = 'pointer';
          closeBtn.style.transform = 'translate(50%, -50%)';
          closeBtn.style.zIndex = '2';
          closeBtn.dataset.index = i;

          closeBtn.addEventListener('click', (event) => {
            const idx = parseInt(event.target.dataset.index);
            selectedFiles.splice(idx, 1);
            updateFileInput();
            renderPreviews();
          });

          div.appendChild(img);
          div.appendChild(closeBtn);
          previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
      });
    }
  </script>
  <script>
    //add overlay on scroll up
    $(window).scroll(function () {
      let position = $(this).scrollTop();
      if (position >= 100) {
        $("#navbar").addClass("scrolled");
      } else {
        $("#navbar").removeClass("scrolled");
      }
    });

    const sections = document.querySelectorAll("section");
    const navItems = document.querySelectorAll(".scrollNavigation ul li");

    window.addEventListener("scroll", () => {
      let current = "";

      sections.forEach((section) => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;

        if (pageYOffset >= sectionTop - sectionHeight / 3) {
          current = section.getAttribute("id");
        }
      });

      navItems.forEach((item) => {
        item.classList.remove("active");
        if (item.getAttribute("data-target") === current) {
          item.classList.add("active");
        }
      });
    });

    // Scroll to the section on click
    navItems.forEach((item) => {
      item.addEventListener("click", () => {
        const targetId = item.getAttribute("data-target");
        const targetSection = document.getElementById(targetId);

        if (targetSection) {
          targetSection.scrollIntoView({
            behavior: "smooth",
          });
        }
      });
    });

    window.addEventListener('load', function () {
      setTimeout(() => {
        if (window.AOS) AOS.init();
      }, 2000);
    });

    // Get the button
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");

    // Show the button when scrolling down
    window.onscroll = function () {
      if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
      ) {
        scrollToTopBtn.style.display = "block";
      } else {
        scrollToTopBtn.style.display = "none";
      }
    };

    // Scroll to the top when the button is clicked
    scrollToTopBtn.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth", // Smooth scroll animation
      });
    });
    scrollToTopBtn.addEventListener("keydown", function (event) {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        scrollToTopBtn.click();
      }
    });
    if (typeof window.formatPrice !== 'function') {
      function formatPrice(amount) {
        let formattedPrice;

        if (amount >= 10000000) {
          formattedPrice = (amount / 10000000).toFixed(2) + " Cr";
        } else if (amount >= 100000) {
          formattedPrice = (amount / 100000).toFixed(2) + " Lakh";
        } else {
          formattedPrice = new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          }).format(amount);
        }

        return formattedPrice;
      }
    }
  </script>
  <script>
    function formatPriceIndian(value) {
      const number = parseInt(value);
      if (isNaN(number)) return "";

      if (number >= 10000000) {
        return (number / 10000000).toFixed(2).replace(/\.00$/, '') + ' Cr';
      } else if (number >= 100000) {
        return (number / 100000).toFixed(2).replace(/\.00$/, '') + ' Lakh';
      } else if (number >= 1000) {
        return (number / 1000).toFixed(2).replace(/\.00$/, '') + ' K';
      }

      return number.toString();
    }

    document.addEventListener("DOMContentLoaded", function () {
      const priceInput = document.getElementById("PropertyPrice");
      const formattedLabel = document.getElementById("formattedPrice");

      // Exit early if the input doesn't exist
      if (!priceInput || !formattedLabel) return;

      function updateFormattedPrice() {
        const formatted = formatPriceIndian(priceInput.value);
        formattedLabel.textContent = formatted ? ` ₹ ${formatted}` : '';
      }

      priceInput.addEventListener("input", updateFormattedPrice);
      updateFormattedPrice(); // initialize on page load if value exists
    });
  </script>

  <script>
    $(document).ready(function () {
      let debounceTimer;

      $('#project_search').on('input', function () {
        clearTimeout(debounceTimer);

        const query = $(this).val();

        if (query.length < 2) {
          $('#project_suggestions').hide();
          return;
        }

        debounceTimer = setTimeout(function () {
          fetchProjects(query);
        }, 300);
      });

      //New: Fetch all on focus/click
      $('#project_search').on('focus click', function () {
        const query = $(this).val();

        // Fetch all projects if query is empty or too short
        if (query.length < 2) {
          fetchProjects('');
        }
      });

      // Extracted function to reuse logic
      function fetchProjects(keyword) {
        $.ajax({
          url: '/projects/search',
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: {
            keyword: keyword,
            location: "{{ $property->city ?? '' }}",
            projectType: "{{ $property->property_type ?? '' }}"
          },
          success: function (response) {
            const suggestions = response.data;
            let html = '';

            if (suggestions.length > 0) {
              suggestions.forEach(function (project) {
                html += `<a href="#" class="list-group-item list-group-item-action" data-id="${project.id}" data-name="${project.name}">${project.name}</a>`;
              });
            } else {
              html = `<div class="list-group-item disabled">No results</div>`;
            }

            $('#project_suggestions').html(html).show();
          }
        });
      }


      // Select project from dropdown
      $(document).on('click', '#project_suggestions a', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#project_search').val(name);
        $('#project_id').val(id);
        $('#project_suggestions').hide();
      });



      // Hide dropdown when clicking outside
      $(document).click(function (e) {
        if (!$(e.target).closest('#project_search, #project_suggestions').length) {
          $('#project_suggestions').hide();
        }
      });
    });
  </script>
  <script>
    const numberInputs = document.querySelectorAll('.only-numeric');

    numberInputs.forEach(input => {
      input.addEventListener('input', function () {
        // Remove anything that's not a digit
        let cleaned = this.value.replace(/\D/g, '');

        const maxLength = parseInt(this.getAttribute('maxlength'));
        const maxValue = parseInt(this.getAttribute('max'));

        // Enforce maxlength if defined
        if (!isNaN(maxLength)) {
          cleaned = cleaned.slice(0, maxLength);
        }

        // Enforce max value if defined
        if (!isNaN(maxValue) && parseInt(cleaned) > maxValue) {
          cleaned = maxValue.toString();
        }

        this.value = cleaned;
      });

      // Prevent typing of "-" or "e"
      input.addEventListener('keydown', function (e) {
        if (['-', 'e', '+'].includes(e.key)) {
          e.preventDefault();
        }
      });
    });

  </script>
  <script>
    $(document).on('submit', '#stepForm', function (e) {
      console.log('Form submitted');

      e.preventDefault();

      const $form = $(this);
      const url = $form.attr('action');
      const formData = new FormData(this);
      let isValid = true;

      // Clear previous error states
      $('#formErrors').html('');
      $form.find('.form-control, .form-select, input[type="radio"]').removeClass('is-invalid is-valid');
      $form.find('.invalid-feedback').text('');

      //FRONTEND VALIDATION START
      $form.find('[name]').each(function () {
        const $input = $(this);
        const name = $input.attr('name');
        const value = $input.val();
        const type = $input.attr('type');
        const isRequired = $input.prop('required');
        const label = $input.attr('data-label') || 'This field';
        console.log(label);

        // Handle radio group validation
        if (type === 'radio' || type === 'checkbox') {
          if ($form.find(`input[name="${name}"]:checked`).length === 0) {
            const $group = $form.find(`input[name="${name}"]`);
            const $errorEl = $group.closest('.mb-3, .mb-4').find('.invalid-feedback').first();
            $group.addClass('is-invalid');
            $errorEl.text(`Please select ${label} `);
            isValid = false;
          }
        }

        // Validate text/number/url/etc.
        else if (isRequired && (value === '' || value === null)) {
          $input.addClass('is-invalid');
          $input.closest('.mb-3, .mb-4').find('.invalid-feedback').text(`${label} is required`);
          isValid = false;
        } else {
          $input.addClass('is-valid');
        }
      });

      // If frontend validation fails, stop here
      if (!isValid) return;

      //  AJAX SUBMISSION START 
      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
          if (res.html && res.redirect_url) {
            window.history.pushState({}, '', res.redirect_url); // Push step URL
            $('#stepContent').html(res.html); // Replace form content
            window.scrollTo({
              top: 0,
              behavior: 'smooth'
            }); // Scroll to top after loading new content
          }
        },
        error: function (xhr) {
          if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;

            // Show server-side validation errors
            $.each(errors, function (field, messages) {
              const $input = $form.find(`[name="${field}"]`);
              const $errorContainer = $input.closest('.mb-3, .mb-4').find('.invalid-feedback');

              if ($input.length && $errorContainer.length) {
                $input.addClass('is-invalid');
                $errorContainer.text(messages[0]);
              }
            });
          } else {
            $('#formErrors').html('Something went wrong. Please try again.');
          }
        }
      });
    });

    window.addEventListener('popstate', function (event) {
      const stepContent = document.getElementById('stepContent');

      // Only run on multi-step form pages
      if (!stepContent) return;

      const url = window.location.href;

      $.ajax({
        url: url,
        type: 'GET',
        success: function (res) {
          stepContent.innerHTML = res;

          window.scrollTo({
            top: 0,
            behavior: 'smooth'
          });
        },
        error: function () {
          stepContent.innerHTML = '<p class="text-danger">Failed to load previous step. Please refresh.</p>';
        }
      });
    });
  </script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    let searchDebounceTimer = null;

    // Show project results when the input field is focused
    $(document).on('focus', '.keyword', function () {
      const $parent = $(this).closest('.searchboxs');
      const $results = $parent.find('.project-results');

      $results.show().html('<li>Searching Projects...</li>');

      const location = $parent.find('select[name="location"]').val();
      const bhkType = $parent.find('select[name="bhkType"]').val();

      if (location) {
        $.ajax({
          url: "{{ route('projects.search') }}",
          method: "POST",
          data: { location, bhkType },
          success: function (data) {
            if (data.data.length > 0) {
              let results = '';
              $.each(data.data, function (index, project) {
                results += `<li><a href="/projects/${project.slug}">${project.name}</a></li>`;
              });
              $results.html(results);
            } else {
              $results.html('<li><a>No projects available for selected configuration</a></li>');
            }
          },
          error: function () {
            $results.html('<li><a>There was an error with the search request. Please try again.</a></li>');
          }
        });
      } else {
        $results.empty();
      }
    });

    // Prevent blur hiding on clicking results
    $(document).on('mousedown', '.project-results', function (event) {
      if (event.target.tagName === 'A') event.preventDefault();
    });

    // Hide results on blur (if not clicking result)
    $(document).on('blur', '.keyword', function (e) {
      const $parent = $(this).closest('.searchboxs');
      const $results = $parent.find('.project-results');
      if (!$(e.relatedTarget).is('.project-results a')) $results.hide();
    });

    // Stop propagation on result click
    $(document).on('click', '.project-results a', function (e) {
      e.stopPropagation();
    });

    // On keyup: search by keyword + filters
    $(document).on('keyup', '.keyword', function () {
      const $input = $(this);
      const $parent = $input.closest('.searchboxs');
      const $results = $parent.find('.project-results');

      const keyword = $input.val().trim();
      const location = $parent.find('select[name="location"]').val();
      const bhkType = $parent.find('select[name="bhkType"]').val();

      clearTimeout(searchDebounceTimer);

      if (keyword === '') {
        $results.empty().hide();
        return;
      }

      $results.show().html('<li><a>Searching Projects...</a></li>');

      searchDebounceTimer = setTimeout(function () {
        $.ajax({
          url: "{{ route('projects.search') }}",
          method: "POST",
          data: { keyword, location, bhkType },
          success: function (data) {
            if (data.data && data.data.length > 0) {
              let results = '';
              $.each(data.data, function (index, project) {
                let url = (project.type === 'custom')
                  ? '/' + project.slug
                  : '/projects/' + project.slug;

                results += `<li><a href="${url}">${project.name}</a></li>`;
              });
              $results.html(results).show();
            } else {
              $results.html('<li><a>No results found</a></li>').show();
            }
          },
          error: function () {
            $results.html('<li><a>There was an error with the search request. Please try again.</a></li>');
          }
        });
      }, 400);
    });


    // Search button click
    $(document).on('click', '.searchBtn', function () {
      const $parent = $(this).closest('.searchboxs');
      const keyword = $parent.find('.keyword').val().trim();
      const location = $parent.find('select[name="location"]').val();
      const bhkType = $parent.find('select[name="bhkType"]').val();

      let slugParts = [];

      if (bhkType) slugParts.push(bhkType.toLowerCase().replace(/\s+/g, '-'));
      if (location) slugParts.push("projects-in-" + location.toLowerCase().replace(/\s+/g, '-'));
      if (bhkType && !location) slugParts.push("projects");
      if (!location && keyword && !bhkType) slugParts.push("projects");

      const finalSlug = slugParts.join('-');
      if (keyword !== '') {
        window.location.href = "/" + finalSlug + "?keyword=" + keyword;
      } else {
        window.location.href = "/" + finalSlug;
      }
    });
  </script>
  @yield('customJS')
  <!-- Floating Contact Buttons -->

  <div class="floating-contact-buttons">

    <!-- WhatsApp -->
    <a href="https://wa.me/919643020020" target="_blank" rel="noopener noreferrer"
      class="floating-contact whatsapp-contact-btn" aria-label="WhatsApp">

      <span class="floating-icon">

        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512"
          xmlns="http://www.w3.org/2000/svg">

          <path
            d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z">
          </path>

        </svg>

      </span>

      <span class="floating-label">WhatsApp</span>

    </a>


    <!-- Call -->
    @if (!request()->routeIs('projects.details'))

    <a href="tel:+919643020020" class="floating-contact call-contact-btn call-contact-btns" aria-label="Call Now">

      <span class="floating-label">Call Now</span>

      <span class="floating-icon">

        <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">

          <path fill="currentColor"
            d="M164.9 24.6c-7.7-18.6-28.2-28.5-47.4-23.2L24.9 24.9C10.7 28.8 0 41.7 0 56.3C0 304.1 207.9 512 455.7 512c14.6 0 27.5-10.7 31.4-24.9l23.5-92.6c5.3-19.2-4.6-39.7-23.2-47.4l-101.5-43.5c-16.3-7-35.2-2.2-46.2 11.6l-45.5 55.5c-73.6-34.4-132.8-93.6-167.2-167.2l55.5-45.5c13.8-11 18.6-29.9 11.6-46.2L164.9 24.6z">
          </path>

        </svg>

      </span>

    </a>

    @endif

  </div>
  <style>
    /* ==========================
   FLOATING CONTACT BUTTONS
========================== */

    .floating-contact-buttons {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 30px;

      width: 100%;
      padding: 0 25px;

      display: flex;
      justify-content: space-between;
      align-items: center;

      box-sizing: border-box;

      z-index: 99999;

      pointer-events: none;
    }


    /* Main Button */

    .floating-contact {
      position: relative;

      width: 62px;
      height: 62px;

      border-radius: 50%;

      display: flex;
      align-items: center;
      justify-content: center;

      text-decoration: none !important;

      pointer-events: auto;

      transition: all 0.3s ease;
    }


    /* Outer Circle */

    .floating-contact::before {
      content: "";

      position: absolute;

      width: 96px;
      height: 96px;

      border-radius: 50%;

      z-index: -1;

      animation: floatingPulse 2s infinite;
    }


    /* WhatsApp Outer */

    .whatsapp-contact-btn::before {
      background: rgba(37, 211, 102, 0.15);
    }


    /* Call Outer */

    .call-contact-btn::before {
      background: rgba(30, 136, 229, 0.15);
    }


    /* Icon Circle */

    .floating-icon {
      width: 50px;
      height: 50px;

      border-radius: 50%;

      display: flex;
      align-items: center;
      justify-content: center;

      color: #fff;

      box-shadow:
        0 5px 15px rgba(0, 0, 0, 0.20);

      position: relative;

      z-index: 2;

      transition: all 0.3s ease;
    }


    /* WhatsApp */

    .whatsapp-contact-btn .floating-icon {
      background: #25D366;
    }


    /* Call */

    .call-contact-btn .floating-icon {
      background: #1976D2;
    }


    /* SVG */

    .floating-icon svg {
      width: 30px;
      height: 30px;

      display: block;
    }


    /* Call icon */

    .call-contact-btn .floating-icon svg {
      width: 25px;
      height: 25px;
    }


    /* Label */

    .floating-label {
      position: absolute;

      bottom: -30px;

      background: #222;
      color: #fff;

      padding: 4px 10px;

      border-radius: 15px;

      font-size: 11px;
      font-weight: 600;

      white-space: nowrap;

      opacity: 0;

      transform: translateY(5px);

      transition: all 0.3s ease;

      pointer-events: none;
    }


    /* Label position */

    .whatsapp-contact-btn .floating-label {
      left: 50%;
      transform: translate(-50%, 5px);
    }

    .call-contact-btn .floating-label {
      right: 50%;
      transform: translate(50%, 5px);
    }


    /* Hover */

    .floating-contact:hover {
      transform: scale(1.10);
    }

    .floating-contact:hover .floating-icon {
      box-shadow:
        0 8px 25px rgba(0, 0, 0, 0.30);
    }

    .floating-contact:hover .floating-label {
      opacity: 1;
      transform: translate(-50%, 0);
    }

    .call-contact-btn:hover .floating-label {
      transform: translate(50%, 0);
    }


    /* Pulse */

    @keyframes floatingPulse {

      0% {
        transform: scale(0.80);
        opacity: 0.8;
      }

      50% {
        transform: scale(1.08);
        opacity: 0.35;
      }

      100% {
        transform: scale(0.80);
        opacity: 0.8;
      }
    }


    /* Icon Animation */

    .floating-icon {
      animation: iconBlink 2s infinite;
    }

    @keyframes iconBlink {

      0% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.06);
      }

      100% {
        transform: scale(1);
      }
    }


    /* ==========================
   MOBILE
========================== */

    @media (max-width: 576px) {

      .floating-contact-buttons {
        bottom: 48px;
        padding: 0 15px;
      }

      .floating-contact {
        width: 54px;
        height: 54px;
      }

      .floating-icon {
        width: 45px;
        height: 45px;
      }

      .floating-icon svg {
        width: 22px;
        height: 22px;
      }

      .call-contact-btn .floating-icon svg {
        width: 20px;
        height: 20px;
      }

      .floating-contact::before {
        width: 82px;
        height: 82px;
      }

      .floating-label {
        display: none;
      }
    }
  </style>


</body>

</html>