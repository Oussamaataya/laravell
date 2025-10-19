<!DOCTYPE html>
<html lang="fr">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title', 'Admin - EcoEvent')</title>
  
  <!-- plugins:css -->
  @vite(['resources/assets-back/vendors/feather/feather.css'])
  <!-- FontAwesome (used by admin views for action icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pbQj7fM2aQYkG0xE1mZg0bJkq3q1G9X2l5VYJzK9X0s2r3bK6Gz1YJkq3q1G9X2l5VYJzK9X0s2r3bK6Gz1YJkq==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  @vite(['resources/assets-back/vendors/ti-icons/css/themify-icons.css'])
  @vite(['resources/assets-back/vendors/css/vendor.bundle.base.css'])
  @vite(['resources/assets-back/vendors/datatables.net-bs4/dataTables.bootstrap4.css'])
  @vite(['resources/assets-back/js/select.dataTables.min.css'])
  @vite(['resources/assets-back/css/vertical-layout-light/style.css'])
  
  <!-- Enhanced Admin Styles -->
  <link rel="stylesheet" href="{{ asset('css/admin-enhanced.css') }}">
  
  <link rel="shortcut icon" href="images/favicon.png" />
  @stack('styles')
  <style>
    /* Basic sidebar active state styling to match admin theme */
    .sidebar .nav .nav-item .nav-link.legitimate-active {
      background-color: #667eea !important;
      color: #fff !important;
      border-radius: 0.375rem !important;
      box-shadow: 0 2px 4px rgba(102,126,234,0.25) !important;
    }
    .sidebar .nav .nav-item .nav-link.legitimate-active .menu-icon,
    .sidebar .nav .nav-item .nav-link.legitimate-active .menu-title { color: #fff !important; }

    /* Pagination tweaks for bootstrap look */
    .pagination { display:flex; padding-left:0; list-style:none; }
    .page-link { color:#667eea; border:1px solid #dee2e6; padding:0.5rem 0.75rem; margin-left:-1px; }
    .page-item.active .page-link { background-color:#667eea; color:#fff; border-color:#667eea; }
  </style>
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
            <a class="nav-link {{ request()->routeIs('admin.publications.*') ? 'active' : '' }}" href="{{ route('admin.publications.index') }}">
              <i class="icon-docs menu-icon"></i>
              <span class="menu-title">Publications</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.commentaires.*') ? 'active' : '' }}" href="{{ route('admin.commentaires.index') }}">
              <i class="icon-speech menu-icon"></i>
              <span class="menu-title">Commentaires</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.chat-rooms.*') ? 'active' : '' }}" href="{{ route('admin.chat-rooms.index') }}">
              <i class="icon-bubbles menu-icon"></i>
              <span class="menu-title">Chat Rooms</span>
            </a>
            <div class="collapse {{ request()->routeIs('admin.chat-rooms.*') ? 'show' : '' }}" id="chat-management">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> 
                  <a class="nav-link {{ request()->routeIs('admin.chat-rooms.index') ? 'active' : '' }}" href="{{ route('admin.chat-rooms.index') }}">
                    <i class="fas fa-list me-2"></i>
                    Liste des rooms
                  </a>
                </li>
                <li class="nav-item"> 
                  <a class="nav-link {{ request()->routeIs('admin.chat-rooms.create') ? 'active' : '' }}" href="{{ route('admin.chat-rooms.create') }}">
                    <i class="fas fa-plus me-2"></i>
                    Créer une room
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
            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
              <i class="icon-settings menu-icon"></i>
              <span class="menu-title">Mon Profil</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <!-- Main panel -->
      @yield('content')
      
      
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const currentRoute = '{{ request()->route()->getName() }}';
      function resetAllLinks() {
        document.querySelectorAll('.sidebar .nav-link').forEach(l=>{
          l.classList.remove('legitimate-active','active','mm-active','show');
        });
      }
      function setTarget(href) {
        const link = document.querySelector('a[href="'+href+'"]');
        if(link) link.classList.add('legitimate-active');
      }
      setTimeout(function(){
        resetAllLinks();
        if(currentRoute && currentRoute.startsWith('admin.publications')) {
          setTarget('{{ route('admin.publications.index') }}');
        } else if(currentRoute && currentRoute.startsWith('admin.commentaires')) {
          setTarget('{{ route('admin.commentaires.index') }}');
        } else if(currentRoute && currentRoute.startsWith('admin.chat-rooms')) {
          setTarget('{{ route('admin.chat-rooms.index') }}');
        } else if(currentRoute && currentRoute.startsWith('admin.users')) {
          setTarget('{{ route('admin.users.index') }}');
        } else if(currentRoute && currentRoute.startsWith('admin.events')) {
          setTarget('{{ route('admin.events.index') }}');
        } else if(currentRoute === 'admin.dashboard') {
          setTarget('{{ route('admin.dashboard') }}');
        }
      }, 300);
    });
  </script>
</body>
</html>
