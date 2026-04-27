{{-- =====================================================
     Frontend Footer Component
     Bootstrap-based footer using the Metary design system.
     Adapted for Jose Consulting Limited (JCL) brand.
     ===================================================== --}}

<footer class="footer-style01 bg-secondary pt-10 pb-8">

    {{-- MAIN FOOTER CONTENT --}}
    <div class="container">
        <div class="row mt-n2-9 mb-6">

            {{-- COLUMN 1: Brand + Description --}}
            <div class="col-md-6 col-lg-4 mt-2-9">
                <div class="pe-md-1-6 pe-xxl-7">
                    <div class="footer-logo mb-1-6">
                        <a href="{{ route('home') }}" class="jcl-logo-on-dark">
                            <img src="{{ asset('images/dark_logo.png') }}"
                                 alt="Jose Consulting Limited">
                        </a>
                    </div>
                    <p class="text-white mb-1-6 opacity8">
                        Jose Consulting Limited helps individuals and organisations strengthen
                        employability, workforce readiness, and global opportunity pathways across
                        the maritime/Logistics and energy sectors.
                    </p>
                    {{-- Social Links --}}
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white opacity7 text-decoration-none" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-white opacity7 text-decoration-none" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-white opacity7 text-decoration-none" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-white opacity7 text-decoration-none" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- COLUMN 2: Quick Links --}}
            <div class="col-md-6 col-lg-2 mt-2-9">
                <div class="ps-md-2-6 ps-xl-7">
                    <h3 class="text-white h5 mb-2-4">Explore</h3>
                    <ul class="links-wrap list-unstyled mb-0">
                        <li><a href="{{ route('about.index') }}">About JCL</a></li>
                        <li><a href="{{ route('leadership.index') }}">Leadership</a></li>
                        <li><a href="{{ route('partnerships.index') }}">Partnerships</a></li>
                        <li><a href="{{ route('training.index') }}">Training</a></li>
                        <li><a href="{{ route('contact.index') }}">Contact</a></li>
                    </ul>
                </div>
            </div>

            {{-- COLUMN 3: Platform Links --}}
            <div class="col-md-6 col-lg-2 mt-2-9">
                <div class="ps-lg-2">
                    <h3 class="text-white h5 mb-2-4">Platform</h3>
                    <ul class="links-wrap list-unstyled mb-0">
                        <li><a href="{{ route('job.index') }}">Browse Jobs</a></li>
                        <li><a href="{{ route('companies.index') }}">Companies</a></li>
                        <li><a href="{{ route('candidate.index') }}">Candidates</a></li>
                        <li><a href="{{ route('news.index') }}">News &amp; Insights</a></li>
                        <li><a href="{{ route('plan.index') }}">Pricing Plans</a></li>
                    </ul>
                </div>
            </div>

            {{-- COLUMN 4: Newsletter --}}
            <div class="col-md-6 col-lg-4 mt-2-9">
                <div class="ps-lg-5">
                    <h3 class="text-white h5 mb-2-4">Newsletter</h3>
                    <p class="text-white mb-1-6 display-28 opacity8">
                        Subscribe to receive maritime industry updates, job alerts, and training opportunities.
                    </p>
                    <form class="quform newsletter-form"
                          action="{{ route('contact.index') }}"
                          method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="quform-elements">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="quform-element mb-0">
                                        <div class="quform-input">
                                            <input class="form-control"
                                                   id="footer_email"
                                                   type="email"
                                                   name="email_address"
                                                   placeholder="Your email address">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="quform-submit-inner">
                                        <button class="btn btn-white text-white m-0" type="submit">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                    <div class="quform-loading-wrap">
                                        <span class="quform-loading"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- BOTTOM BAR: copyright + links --}}
    <div class="container">
        <div class="row mt-n4 align-items-center">

            <div class="col-lg-6 mt-4">
                <div class="text-center text-lg-start">
                    <p class="d-inline-block text-white mb-0 opacity8">
                        &copy; <span class="current-year"></span>
                        Jose Consulting Limited. Powered by
                        <a href="{{ route('home') }}" class="text-primary text-white-hover">JOSEOCEANJOBS</a>
                    </p>
                </div>
            </div>

            <div class="col-lg-6 mt-4">
                <div class="text-center text-lg-end">
                    <ul class="list-unstyled mb-0">
                        <li class="display-30 d-inline-block border-end border-color-light-white pe-3 me-2 lh-1">
                            <a href="#" class="text-white text-primary-hover opacity7">Terms &amp; Conditions</a>
                        </li>
                        <li class="display-30 d-inline-block lh-1">
                            <a href="#" class="text-white text-primary-hover opacity7">Privacy Policy</a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</footer>
