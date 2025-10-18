<!-- Modern Footer -->
<footer class="bg-light text-dark py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- Section 1: ECO EVENT -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-uppercase mb-4">
                    <i class="fas fa-leaf text-success me-2"></i>ECO EVENT
                </h5>
                <p class="text-muted small">
                    Plateforme dédiée à la promotion de la durabilité environnementale à travers des événements, campagnes et actions écologiques qui respectent notre planète.
                </p>
                <!-- Social Links -->
                <div class="social-links mt-4">
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle me-2" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle me-2" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle me-2" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle me-2" title="Pinterest">
                        <i class="fab fa-pinterest"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Section 2: QUICK LINKS -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-uppercase mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-home me-2"></i>HOME
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('events.index') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-calendar-alt me-2"></i>ÉVÉNEMENTS
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('collectes.index') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-donate me-2"></i>COLLECTES
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('publications.index') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-newspaper me-2"></i>PUBLICATIONS
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('recyclages.index') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-recycle me-2"></i>RECYCLAGE
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Section 3: HELP & INFO -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-uppercase mb-4">Help & Info</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('reclamations.index') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-exclamation-circle me-2"></i>RÉCLAMATIONS
                        </a>
                    </li>
                    @auth
                    <li class="mb-2">
                        <a href="{{ route('assistant.chat') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-robot me-2"></i>ASSISTANT IA
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('events.my-registrations') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-ticket-alt me-2"></i>MES INSCRIPTIONS
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('profile.edit') }}" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-user me-2"></i>MON PROFIL
                        </a>
                    </li>
                    @endauth
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none hover-link">
                            <i class="fas fa-question-circle me-2"></i>FAQS
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Section 4: CONTACT US -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-uppercase mb-4">Contact Us</h5>
                <p class="text-muted small mb-3">
                    Do you have any questions or suggestions?
                </p>
                <p class="mb-2">
                    <i class="fas fa-envelope text-success me-2"></i>
                    <a href="mailto:contact@ecoevent.com" class="text-muted text-decoration-none hover-link">
                        contact@ecoevent.com
                    </a>
                </p>
                <p class="text-muted small mb-3">
                    Do you need support? Give us a call.
                </p>
                <p class="mb-2">
                    <i class="fas fa-phone text-success me-2"></i>
                    <a href="tel:+33720115278" class="text-muted text-decoration-none hover-link">
                        +33 7 20 11 52 78
                    </a>
                </p>
            </div>
        </div>

        <!-- Bottom Bar -->
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="text-muted small mb-0">
                    © Copyright {{ date('Y') }} EcoEvent. All rights reserved. 
                    <span class="text-success">Design by TemplatesJungle</span>
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="text-muted small mb-0">
                    Distribution By 
                    <a href="https://themewagon.com" class="text-success text-decoration-none" target="_blank">ThemeWagon</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Footer Hover Effects */
    .hover-link {
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .hover-link:hover {
        color: #28a745 !important;
        transform: translateX(5px);
    }
    
    .social-links .btn {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .social-links .btn:hover {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }
    
    footer {
        background-color: #f8f9fa;
        border-top: 3px solid #28a745;
    }
    
    footer h5 {
        position: relative;
        padding-bottom: 10px;
    }
    
    footer h5::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background-color: #28a745;
    }
</style>
