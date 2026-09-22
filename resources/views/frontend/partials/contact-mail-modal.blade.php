<div class="modal fade"
     id="contactMailModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header border-0">
                            <img src="{{ asset('frontend/360logo.png') }}" alt="360propguide" class="w-50 mx-auto">

                            <button type="button" class="btn-close align-self-start ms-0 shadow-none"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <h3 class="h6 text-center mb-3 fw-bold">
                            Contact Us
                        </h3>

            <div class="modal-body">

                @include('frontend.partials.contact-mail-form')

            </div>

        </div>

    </div>
</div>