<nav class="navbar navbar-expand-lg bg-light text-uppercase fs-6 p-3 border-bottom align-items-center">
    <div class="container-fluid">
      <div class="row justify-content-between align-items-center w-100">
<div class="col-auto">
  <a class="navbar-brand text-black font-bold text-3xl" href="{{ route('home') }}">
    ECO EVENT
  </a>
</div>

        <div class="col-auto">
          <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
            aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
              <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
              <ul class="navbar-nav justify-content-end flex-grow-1 gap-1 gap-md-5 pe-3">
                <li class="nav-item">
                  <a class="nav-link active" href="{{ route('home') }}">Accueil</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('events.index') }}">
                    <i class="fas fa-calendar-alt me-1"></i>Événements
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('collectes.index') }}">
                    <i class="fas fa-hand-holding-heart me-1"></i>Collectes
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('reclamations.index') }}">Reclamations</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('publications.index') }}">
                    <i class="fas fa-newspaper me-1"></i>Publications
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('recyclages.index') }}">
                    <i class="fas fa-recycle me-1"></i>Recyclage
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Contact</a>
                </li>
                
                @auth
                  <!-- Menu utilisateur connecté -->
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownUser" data-bs-toggle="dropdown"
                      aria-haspopup="true" aria-expanded="false">
                      <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu list-unstyled" aria-labelledby="dropdownUser">
                      @if(auth()->user()->isAdmin())
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                      @else
                        <li><a class="dropdown-item" href="{{ route('home') }}">Mon Espace</a></li>
                      @endif
                      <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon Profil</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                          @csrf
                          <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                          </button>
                        </form>
                      </li>
                    </ul>
                  </li>
                @else
                  <!-- Boutons pour utilisateurs non connectés -->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                      <i class="fas fa-sign-in-alt me-1"></i>Connexion
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white px-3 rounded" href="{{ route('register') }}">
                      <i class="fas fa-user-plus me-1"></i>Inscription
                    </a>
                  </li>
                @endauth
              </ul>
            </div>
          </div>
        </div>

        <div class="col-3 col-lg-auto">
          <ul class="list-unstyled d-flex m-0">
            <li class="search-box mx-2">
              <a href="#search" class="search-button">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <use xlink:href="#search"></use>
                </svg>
              </a>
            </li>
          </ul>
        </div>

      </div>

    </div>
  </nav>

  <!-- Cart offcanvas removed (not used) -->
