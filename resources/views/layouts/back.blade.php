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
  
  <style>
    /* Reset tous les liens de navigation à l'état normal - FORCE l'état par défaut */
    .sidebar .nav .nav-item .nav-link {
      background-color: transparent !important;
      color: #6c7293 !important;
      transition: all 0.3s ease;
      border-radius: 0.375rem !important;
      margin: 2px 0 !important;
    }
    
    /* Forcer l'état normal même avec la classe active si elle n'est pas légitime */
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active) {
      background-color: transparent !important;
      color: #6c7293 !important;
    }
    
    /* Supprimer TOUS les styles actifs par défaut */
    .sidebar .nav .nav-item .nav-link.active:not(.legitimate-active) {
      background-color: transparent !important;
      color: #6c7293 !important;
    }
    
    .sidebar .nav .nav-item .nav-link .menu-icon,
    .sidebar .nav .nav-item .nav-link .menu-title {
      color: #6c7293 !important;
    }
    
    /* Styles au survol seulement */
    .sidebar .nav .nav-item .nav-link:hover {
      background-color: #667eea !important;
      color: #ffffff !important;
      transform: translateX(2px);
    }
    
    .sidebar .nav .nav-item .nav-link:hover .menu-icon,
    .sidebar .nav .nav-item .nav-link:hover .menu-title {
      color: #ffffff !important;
    }
    
    /* Styles actifs seulement pour la classe .legitimate-active */
    .sidebar .nav .nav-item .nav-link.legitimate-active {
      background-color: #667eea !important;
      color: #ffffff !important;
      border-radius: 0.375rem !important;
      box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3) !important;
      transform: translateX(2px) !important;
    }
    
    .sidebar .nav .nav-item .nav-link.legitimate-active .menu-icon,
    .sidebar .nav .nav-item .nav-link.legitimate-active .menu-title {
      color: #ffffff !important;
      font-weight: 600 !important;
    }
    
    /* Animation au clic */
    .sidebar .nav .nav-item .nav-link:active {
      transform: scale(0.98) !important;
    }
    
    /* Styles pour les sous-menus */
    .sidebar .nav .nav-item .collapse .nav .nav-item .nav-link {
      background-color: transparent !important;
      color: #6c7293 !important;
    }
    
    .sidebar .nav .nav-item .collapse .nav .nav-item .nav-link:hover {
      background-color: rgba(102, 126, 234, 0.8) !important;
      color: #ffffff !important;
      transform: translateX(5px);
    }
    
    .sidebar .nav .nav-item .collapse .nav .nav-item .nav-link.active {
      background-color: #667eea !important;
      color: #ffffff !important;
    }
    
    /* Corrections pour les boutons dans l'admin */
    .btn {
      border-radius: 0.375rem !important;
      font-weight: 500 !important;
      padding: 0.5rem 1rem !important;
      font-size: 0.875rem !important;
      line-height: 1.5 !important;
      border-width: 1px !important;
      transition: all 0.15s ease-in-out !important;
    }
    
    .btn-primary {
      background-color: #667eea !important;
      border-color: #667eea !important;
      color: #ffffff !important;
    }
    
    .btn-primary:hover {
      background-color: #5a6fd8 !important;
      border-color: #5a6fd8 !important;
      color: #ffffff !important;
    }
    
    .btn-outline-primary {
      background-color: transparent !important;
      border-color: #667eea !important;
      color: #667eea !important;
    }
    
    .btn-outline-primary:hover {
      background-color: #667eea !important;
      border-color: #667eea !important;
      color: #ffffff !important;
    }
    
    .btn-outline-secondary {
      background-color: transparent !important;
      border-color: #6c757d !important;
      color: #6c757d !important;
    }
    
    .btn-outline-secondary:hover {
      background-color: #6c757d !important;
      border-color: #6c757d !important;
      color: #ffffff !important;
    }
    
    .btn-success {
      background-color: #28a745 !important;
      border-color: #28a745 !important;
      color: #ffffff !important;
    }
    
    .btn-warning {
      background-color: #ffc107 !important;
      border-color: #ffc107 !important;
      color: #212529 !important;
    }
    
    .btn-danger {
      background-color: #dc3545 !important;
      border-color: #dc3545 !important;
      color: #ffffff !important;
    }
    
    .btn-info {
      background-color: #17a2b8 !important;
      border-color: #17a2b8 !important;
      color: #ffffff !important;
    }
    
    .btn-secondary {
      background-color: #6c757d !important;
      border-color: #6c757d !important;
      color: #ffffff !important;
    }
    
    /* Groupes de boutons */
    .btn-group .btn {
      margin-right: 0 !important;
    }
    
    .btn-group .btn:not(:first-child) {
      border-left: 0 !important;
    }
    
    .btn-group .btn:first-child {
      border-top-right-radius: 0 !important;
      border-bottom-right-radius: 0 !important;
    }
    
    .btn-group .btn:last-child {
      border-top-left-radius: 0 !important;
      border-bottom-left-radius: 0 !important;
    }
    
    .btn-group .btn:not(:first-child):not(:last-child) {
      border-radius: 0 !important;
    }
    
    /* Espacement pour les boutons côte à côte */
    .d-flex .btn + .btn {
      margin-left: 0.5rem !important;
    }
    
    /* Corrections pour les formulaires */
    .form-control {
      border-radius: 0.375rem !important;
      border: 1px solid #ced4da !important;
      padding: 0.5rem 0.75rem !important;
      font-size: 0.875rem !important;
      line-height: 1.5 !important;
    }
    
    .form-control:focus {
      border-color: #667eea !important;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
    }
    
    /* Corrections pour les cartes */
    .card {
      border-radius: 0.5rem !important;
      border: 1px solid rgba(0, 0, 0, 0.125) !important;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    .card-header {
      border-bottom: 1px solid rgba(0, 0, 0, 0.125) !important;
      background-color: rgba(0, 0, 0, 0.03) !important;
    }
    
    /* Corrections pour les alertes */
    .alert {
      border-radius: 0.375rem !important;
      padding: 0.75rem 1.25rem !important;
      margin-bottom: 1rem !important;
      border: 1px solid transparent !important;
    }
    
    .alert-success {
      background-color: #d4edda !important;
      border-color: #c3e6cb !important;
      color: #155724 !important;
    }
    
    .alert-danger {
      background-color: #f8d7da !important;
      border-color: #f5c6cb !important;
      color: #721c24 !important;
    }
    
    .alert-warning {
      background-color: #fff3cd !important;
      border-color: #ffeaa7 !important;
      color: #856404 !important;
    }
    
    .alert-info {
      background-color: #d1ecf1 !important;
      border-color: #bee5eb !important;
      color: #0c5460 !important;
    }
    
    /* Corrections pour les boutons de fermeture des alertes (Bootstrap 4) */
    .alert .close {
      position: absolute;
      top: 0;
      right: 0;
      padding: 0.75rem 1.25rem;
      color: inherit;
      background: none;
      border: 0;
      font-size: 1.5rem;
      font-weight: 700;
      line-height: 1;
      opacity: 0.5;
      text-shadow: 0 1px 0 #fff;
    }
    
    .alert .close:hover {
      opacity: 0.75;
    }
    
    /* Amélioration des boutons dans les tableaux */
    .table .btn {
      padding: 0.25rem 0.5rem !important;
      font-size: 0.75rem !important;
      margin: 0.125rem !important;
    }
    
    .table .btn-group .btn {
      margin: 0 !important;
    }
    
    /* Corrections pour les icônes MDI */
    .mdi {
      font-size: 1rem !important;
      vertical-align: middle !important;
    }
    
    .btn .mdi {
      margin-right: 0.25rem !important;
    }
    
    /* Amélioration de l'espacement des formulaires */
    .form-group {
      margin-bottom: 1rem !important;
    }
    
    .form-label {
      margin-bottom: 0.5rem !important;
      font-weight: 500 !important;
      color: #495057 !important;
    }
    
    /* Corrections pour les badges */
    .badge {
      padding: 0.25em 0.4em !important;
      font-size: 75% !important;
      font-weight: 700 !important;
      line-height: 1 !important;
      text-align: center !important;
      white-space: nowrap !important;
      vertical-align: baseline !important;
      border-radius: 0.25rem !important;
    }
    
    .badge-primary {
      background-color: #667eea !important;
      color: #ffffff !important;
    }
    
    .badge-success {
      background-color: #28a745 !important;
      color: #ffffff !important;
    }
    
    .badge-warning {
      background-color: #ffc107 !important;
      color: #212529 !important;
    }
    
    .badge-danger {
      background-color: #dc3545 !important;
      color: #ffffff !important;
    }
    
    .badge-info {
      background-color: #17a2b8 !important;
      color: #ffffff !important;
    }
    
    .badge-secondary {
      background-color: #6c757d !important;
      color: #ffffff !important;
    }
    
    /* Corrections pour les dropdowns */
    .dropdown-menu {
      border-radius: 0.375rem !important;
      border: 1px solid rgba(0, 0, 0, 0.15) !important;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175) !important;
    }
    
    .dropdown-item {
      padding: 0.5rem 1rem !important;
      color: #212529 !important;
      text-decoration: none !important;
      background-color: transparent !important;
      border: 0 !important;
      display: block !important;
      width: 100% !important;
      clear: both !important;
      font-weight: 400 !important;
      text-align: inherit !important;
      white-space: nowrap !important;
    }
    
    .dropdown-item:hover {
      background-color: #f8f9fa !important;
      color: #16181b !important;
    }
    
    /* Corrections pour la pagination */
    .pagination {
      display: flex !important;
      padding-left: 0 !important;
      list-style: none !important;
      border-radius: 0.375rem !important;
    }
    
    .page-link {
      position: relative !important;
      display: block !important;
      padding: 0.5rem 0.75rem !important;
      margin-left: -1px !important;
      line-height: 1.25 !important;
      color: #667eea !important;
      text-decoration: none !important;
      background-color: #fff !important;
      border: 1px solid #dee2e6 !important;
    }
    
    .page-link:hover {
      z-index: 2 !important;
      color: #5a6fd8 !important;
      text-decoration: none !important;
      background-color: #e9ecef !important;
      border-color: #dee2e6 !important;
    }
    
    .page-item.active .page-link {
      z-index: 3 !important;
      color: #fff !important;
      background-color: #667eea !important;
      border-color: #667eea !important;
    }
    
    /* Uniformisation de la largeur des pages admin */
    .main-panel {
      width: 100% !important;
      max-width: none !important;
    }
    
    .content-wrapper {
      width: 100% !important;
      max-width: none !important;
      padding: 2rem 2rem !important;
    }
    
    /* Largeur uniforme pour les cartes */
    .card {
      width: 100% !important;
      max-width: none !important;
    }
    
    /* Grille responsive uniforme */
    .row {
      margin-left: -15px !important;
      margin-right: -15px !important;
    }
    
    .col-md-12.grid-margin.stretch-card,
    .col-lg-12.grid-margin.stretch-card {
      padding-left: 15px !important;
      padding-right: 15px !important;
      width: 100% !important;
    }
    
    /* Formulaires uniformes */
    .form-control {
      width: 100% !important;
    }
    
    /* Tableaux responsives */
    .table-responsive {
      width: 100% !important;
      overflow-x: auto !important;
    }
    
    .table {
      width: 100% !important;
      min-width: 800px !important;
    }
    
    /* Boutons uniformes */
    .btn-group {
      width: auto !important;
    }
    
    /* Espacement uniforme */
    .grid-margin {
      margin-bottom: 2rem !important;
    }
    
    .stretch-card {
      display: flex !important;
      align-items: stretch !important;
    }
    
    .stretch-card > .card {
      width: 100% !important;
      flex: 1 !important;
    }
    
    /* Styles spécifiques pour les pages d'événements */
    .event-page .main-panel {
      width: 100% !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    
    .event-page .content-wrapper {
      padding: 2rem !important;
      width: 100% !important;
      max-width: 100% !important;
    }
    
    /* Uniformisation des formulaires d'événements */
    .event-form .row {
      width: 100% !important;
    }
    
    .event-form .col-md-6,
    .event-form .col-md-4,
    .event-form .col-md-8,
    .event-form .col-md-12 {
      padding-left: 15px !important;
      padding-right: 15px !important;
    }
    
    /* Cartes d'événements uniformes */
    .event-card {
      width: 100% !important;
      margin-bottom: 2rem !important;
    }
    
    .event-card .card-body {
      padding: 2rem !important;
    }
    
    /* Images d'événements responsives */
    .event-image {
      width: 100% !important;
      max-width: 500px !important;
      height: auto !important;
      border-radius: 0.5rem !important;
      object-fit: cover !important;
    }
    
    /* Images dans les cartes d'événements */
    .event-card img {
      max-width: 100% !important;
      height: auto !important;
      border-radius: 0.375rem !important;
    }
    
    /* Conteneur d'image uniforme */
    .event-image-container {
      width: 100% !important;
      max-width: 500px !important;
      margin: 0 auto !important;
      text-align: center !important;
    }
    
    /* Statistiques d'événements uniformes */
    .event-stats .row {
      margin: 0 !important;
    }
    
    .event-stats .col-md-3 {
      padding: 0.5rem !important;
    }
    
    /* Tableaux d'événements */
    .events-table {
      width: 100% !important;
    }
    
    .events-table .table {
      margin-bottom: 0 !important;
    }
    
    /* Responsive design pour mobile */
    @media (max-width: 768px) {
      .content-wrapper {
        padding: 1rem !important;
      }
      
      .event-card .card-body {
        padding: 1rem !important;
      }
      
      .table-responsive {
        font-size: 0.875rem !important;
      }
    }
    
    /* Prévention ABSOLUE des conflits avec les scripts Skydash */
    .sidebar .nav .nav-item .nav-link.mm-active:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link.mm-show:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link[aria-expanded="true"]:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link.show:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link.collapsed:not(.legitimate-active) {
      background-color: transparent !important;
      color: #6c7293 !important;
    }
    
    /* FORCER BRUTALEMENT l'état normal pour TOUS les liens sauf celui avec legitimate-active */
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active):hover,
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active):focus,
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active):active {
      background-color: transparent !important;
      color: #6c7293 !important;
    }
    
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active) .menu-icon,
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active) .menu-title,
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active):hover .menu-icon,
    .sidebar .nav .nav-item .nav-link:not(.legitimate-active):hover .menu-title {
      color: #6c7293 !important;
    }
    
    /* EMPÊCHER ABSOLUMENT tous les styles actifs non autorisés */
    .sidebar .nav .nav-item .nav-link.active:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link.mm-active:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link.show:not(.legitimate-active),
    .sidebar .nav .nav-item .nav-link[aria-expanded="true"]:not(.legitimate-active) {
      background-color: transparent !important;
      color: #6c7293 !important;
    }
    
    /* Styles spécifiques pour les sous-menus */
    .sidebar .nav .nav-item .collapse .nav .nav-item .nav-link.legitimate-active {
      background-color: rgba(102, 126, 234, 0.9) !important;
      color: #ffffff !important;
      margin-left: 10px !important;
    }
    
    /* Animation fluide pour les changements d'état */
    .sidebar .nav .nav-item .nav-link.legitimate-active {
      animation: activateLink 0.3s ease-in-out;
    }
    
    @keyframes activateLink {
      0% {
        background-color: transparent;
        transform: translateX(0);
      }
      50% {
        transform: translateX(3px);
      }
      100% {
        background-color: #667eea;
        transform: translateX(0);
      }
    }
  </style>
  
</head>

<body>
  <div class="container-scroller">
    <!-- Navbar -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ route('admin.dashboard') }}">
          <img src="{{ Vite::asset('resources/assets-back/images/logo.svg') }}" class="mr-2" alt="EcoEvent">
        </a>
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
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
              <i class="icon-people menu-icon"></i>
              <span class="menu-title">Gestion Utilisateurs</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.events.index') }}">
              <i class="ti-calendar menu-icon"></i>
              <span class="menu-title">Événements</span>
            </a>
          </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.collectes.index') }}" data-toggle="collapse" aria-expanded="false" aria-controls="collectesSubmenu">
                                    <i class="ti-money menu-icon"></i>
                                    <span class="menu-title">Gestion de Collecte</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="collapse" id="collectesSubmenu">
                                    <ul class="nav flex-column sub-menu">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.collectes.index') }}">
                                                Collectes
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.campagnes.index') }}">
                                                Campagnes
                                            </a>
                                        </li>
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
            <a class="nav-link" href="{{ route('profile.edit') }}">
              <i class="ti-user menu-icon"></i>
              <span class="menu-title">Mon Profil</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <!-- Main panel -->
      @yield('content')
      
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

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
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const currentRoute = '{{ request()->route()->getName() }}';
      
      // Fonction pour nettoyer tous les liens
      function resetAllLinks() {
        document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
          link.classList.remove('legitimate-active', 'active', 'mm-active', 'show', 'mm-show');
          link.style.backgroundColor = '';
          link.style.color = '';
        });
      }
      
      // Fonction pour activer un lien
      function activateLink(link) {
        resetAllLinks();
        link.classList.add('legitimate-active');
        console.log('Lien activé:', link.href);
      }
      
      // Initialisation au chargement
      setTimeout(function() {
        resetAllLinks();
        
        // Activer le bon lien selon la route
        let targetLink = null;
        
        if (currentRoute === 'admin.dashboard') {
          targetLink = document.querySelector('a[href="{{ route('admin.dashboard') }}"]');
        } else if (currentRoute && currentRoute.startsWith('admin.users')) {
          targetLink = document.querySelector('a[href="{{ route('admin.users.index') }}"]');
        } else if (currentRoute && currentRoute.startsWith('admin.events')) {
          targetLink = document.querySelector('a[href="{{ route('admin.events.index') }}"]');
        } else if (currentRoute === 'profile.edit') {
          targetLink = document.querySelector('a[href="{{ route('profile.edit') }}"]');
        }
        
        if (targetLink) {
          targetLink.classList.add('legitimate-active');
          console.log('Navigation initialisée pour:', currentRoute);
        }
      }, 500);
      
      // Gestion des clics sur les liens de la sidebar
      document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
          // Activer immédiatement le lien cliqué
          activateLink(this);
          
          // Maintenir l'activation même après la navigation
          setTimeout(function() {
            if (document.querySelector('.sidebar .nav-link.legitimate-active') !== link) {
              activateLink(link);
            }
          }, 100);
        });
      });
      
      // Vérification périodique pour maintenir l'état
      setInterval(function() {
        const activeLinks = document.querySelectorAll('.sidebar .nav-link.legitimate-active');
        if (activeLinks.length !== 1) {
          // Si pas exactement 1 lien actif, corriger
          resetAllLinks();
          
          let targetLink = null;
          if (currentRoute === 'admin.dashboard') {
            targetLink = document.querySelector('a[href="{{ route('admin.dashboard') }}"]');
          } else if (currentRoute && currentRoute.startsWith('admin.users')) {
            targetLink = document.querySelector('a[href="{{ route('admin.users.index') }}"]');
          } else if (currentRoute && currentRoute.startsWith('admin.events')) {
            targetLink = document.querySelector('a[href="{{ route('admin.events.index') }}"]');
          } else if (currentRoute === 'profile.edit') {
            targetLink = document.querySelector('a[href="{{ route('profile.edit') }}"]');
          }
          
          if (targetLink) {
            targetLink.classList.add('legitimate-active');
          }
        }
      }, 3000);
    });
  </script>
  
</body>
</html>
