<!DOCTYPE html>
<html lang="fr">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title', 'Admin - EcoEvent')</title>
  
  <!-- plugins:css -->
  @vite(['resources/assets-back/vendors/feather/feather.css'])
  @vite(['resources/assets-back/vendors/ti-icons/css/themify-icons.css'])
  @vite(['resources/assets-back/vendors/css/vendor.bundle.base.css'])
  @vite(['resources/assets-back/vendors/datatables.net-bs4/dataTables.bootstrap4.css'])
  @vite(['resources/assets-back/js/select.dataTables.min.css'])
  @vite(['resources/assets-back/css/vertical-layout-light/style.css'])
  
  <link rel="shortcut icon" href="images/favicon.png" />
  @stack('styles')
</head>

<body>
  <div class="container-scroller">
    <!-- Navbar -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
       <div class="col-auto">
  <a class="navbar-brand text-black font-bold text-3xl" >
    ECO EVENT
  </a>
</div>
        <a class="navbar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}">
          <img src="{{ Vite::asset('resources/assets-back/images/logo-mini.svg') }}" alt="EcoEvent">
        </a>
      </div>
      
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Rechercher..." aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>
        
        <ul class="navbar-nav navbar-nav-right">
          <!-- Notifications -->
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-success">
                    <i class="ti-info-alt mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Bienvenue sur EcoEvent</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    Maintenant
                  </p>
                </div>
              </a>
            </div>
          </li>
          
          <!-- Profile -->
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="{{ Vite::asset('resources/assets-back/images/faces/face28.jpg') }}" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <div class="dropdown-header text-center">
                <strong>{{ auth()->user()->name }}</strong>
                <small class="text-muted">{{ auth()->user()->email }}</small>
              </div>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="ti-settings text-primary"></i>
                Mon Profil
              </a>
              <div class="dropdown-divider"></div>
              <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                  <i class="ti-power-off text-primary"></i>
                  Déconnexion
                </button>
              </form>
            </div>
          </li>
        </ul>
        
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    
    <!-- Page body wrapper -->
    <div class="container-fluid page-body-wrapper">
      <!-- Sidebar -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#user-management" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" aria-controls="user-management">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">Gestion Utilisateurs</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="user-management">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> 
                  <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    Liste des utilisateurs
                  </a>
                </li>
                <li class="nav-item"> 
                  <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">
                    Créer un utilisateur
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#events-management" aria-expanded="false" aria-controls="events-management">
              <i class="icon-calendar menu-icon"></i>
              <span class="menu-title">Gestion Événements</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="events-management">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="#">Liste des événements</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Créer un événement</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Catégories</a></li>
              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}" target="_blank">
              <i class="icon-globe menu-icon"></i>
              <span class="menu-title">Voir le site</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
              <i class="icon-settings menu-icon"></i>
              <span class="menu-title">Mon Profil</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <!-- Main panel -->
      @yield('content')
      
      <!-- Footer -->
      <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
            Copyright © {{ date('Y') }} EcoEvent. Tous droits réservés.
          </span>
          <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
            Fait avec <i class="ti-heart text-danger ml-1"></i> pour l'environnement
          </span>
        </div>
      </footer>
    </div>
  </div>

  <!-- Scripts -->
  @vite(['resources/assets-back/vendors/js/vendor.bundle.base.js'])
  @vite(['resources/assets-back/vendors/chart.js/Chart.min.js'])
  @vite(['resources/assets-back/vendors/datatables.net/jquery.dataTables.js'])
  @vite(['resources/assets-back/vendors/datatables.net-bs4/dataTables.bootstrap4.js'])
  @vite(['resources/assets-back/js/dataTables.select.min.js'])
  @vite(['resources/assets-back/js/off-canvas.js'])
  @vite(['resources/assets-back/js/hoverable-collapse.js'])
  @vite(['resources/assets-back/js/template.js'])
  @vite(['resources/assets-back/js/settings.js'])
  @vite(['resources/assets-back/js/todolist.js'])
  @vite(['resources/assets-back/js/dashboard.js'])
  @vite(['resources/assets-back/js/Chart.roundedBarCharts.js'])
  
  @stack('scripts')
</body>
</html>
