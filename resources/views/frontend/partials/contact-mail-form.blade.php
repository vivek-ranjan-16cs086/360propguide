<form method="POST" class="popupForm" action="{{ route('contact-mail') }}">
    @csrf

    <input type="hidden" name="formName" value="popup" />

    <!-- Name -->
    <div class="mb-3">
        <div class="form-group input-group">
            <span class="input-group-text">
                <i class="fa-solid fa-user"></i>
            </span>

            <input type="text"
                   class="form-control error commonerr"
                   name="name"
                   placeholder="Name" />
        </div>

        <span class="text-danger error-name"></span>
    </div>

    <!-- Email -->
    <div class="mb-3">
        <div class="form-group input-group">
            <span class="input-group-text">
                <i class="fa-solid fa-envelope"></i>
            </span>

            <input type="email"
                   placeholder="Email Address*"
                   name="email"
                   class="form-control" />
        </div>

        <span class="text-danger error-email"></span>
    </div>

    <!-- Mobile -->
    <div class="mb-3">
        <div class="form-group input-group">
            <span class="input-group-text">
                <i class="fa-solid fa-phone"></i>
            </span>

            <input type="tel"
                   class="form-control"
                   name="mobile"
                   placeholder="Mobile*"
                   maxlength="10"
                   pattern="[0-9]{10}"
                   inputmode="numeric" />
        </div>

        <span class="text-danger error-mobile"></span>
    </div>

    <!-- Message -->
    <div class="mb-3">
        <div class="form-group">
            <textarea placeholder="Message"
                      name="message"
                      class="form-control"
                      rows="3"></textarea>
        </div>
    </div>

    <!-- Recaptcha -->
    <div class="mb-3">
        <div class="g-recaptcha"
             data-sitekey="{{ config('services.recaptcha.site_key') }}">
        </div>

        <span class="text-danger error-recaptcha"></span>

        @if ($errors->has('recaptchaform5'))
            <div class="alert alert-danger mt-2">
                {{ $errors->first('recaptchaform5') }}
            </div>
        @endif
    </div>

    <!-- Submit -->
    <div class="mb-3">
        <button type="submit" 
                class="btn customBtn w-100 submitButton" id="contact-mail-submit">
            Submit
        </button>
    </div>

</form>

