<!DOCTYPE html>
<html lang="en">
<head>
    @include('shared.meta')
    @include('shared.stylesheet')
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
        }
        
        #sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            color: #fff;
            transition: all 0.3s ease;
            z-index: 1050;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        #sidebar.active {
            left: 0;
        }
        
        #sidebar .list-group-item {
            color: #fff;
            border: none;
            padding: 15px 20px;
            transition: all 0.3s ease;
            border-radius: 0;
        }
        
        #sidebar .list-group-item:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateX(5px);
        }
        
        #sidebar .list-group-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 1.5rem;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .btn-menu-toggle {
            border: none;
            background: transparent;
            color: #fff;
            font-size: 1.2rem;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-menu-toggle:hover {
            background: rgba(255,255,255,0.1);
            transform: scale(1.05);
        }
        

        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        
        .sidebar-header h5 {
            color: #fff;
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-header p {
            color: rgba(255,255,255,0.7);
            margin: 5px 0 0 0;
            font-size: 0.9rem;
        }
        
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 80px);
        }
        
        .main-content.sidebar-open {
            margin-left: 250px;
        }
        
        @media (max-width: 768px) {
            .main-content.sidebar-open {
                margin-left: 0;
            }
        }
        
        /* Enhanced Global Alert Styling */
        .alert {
            border-left: 4px solid;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .alert-danger {
            border-left-color: #dc3545;
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        }
        
        .alert-success {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        
        .alert-info {
            border-left-color: #17a2b8;
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        }
        
        .alert-warning {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        }
        
        /* Enhanced button hover effects */
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: all 0.2s ease;
        }
    </style>
</head>
<body>
  <!-- Sidebar -->
  <div id="sidebar" style="background-color: #007bff;">
    <div class="sidebar-header">
      <h5><img src="{{ asset('favicon/android-icon-36x36.png') }}" alt="{{ config('app.name') }}" width="24" height="24" class="me-2"> {{ config('app.name') }}</h5>
      <p>Mini ERP System</p>
    </div>
    <div class="list-group list-group-flush">
      <!-- ERP Modules -->
      @can('products.view')
      <a href="{{ route('products.index') }}" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-boxes"></i> Products
      </a>
      @endcan
      
      @can('customers.view')
      <a href="#" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-users"></i> Customers
      </a>
      @endcan
      
      @can('suppliers.view')
      <a href="{{ route('suppliers.index') }}" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-building"></i> Suppliers
      </a>
      @endcan
      
      @can('warehouses.view')
      <a href="{{ route('warehouses.index') }}" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-warehouse"></i> Warehouses
      </a>
      @endcan
      
      @can('sales-orders.view')
      <a href="#" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-chart-line"></i> Sales Orders
      </a>
      @endcan
      
      @can('purchase-orders.view')
      <a href="#" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-shopping-cart"></i> Purchase Orders
      </a>
      @endcan
      
      @can('general-ledger.view')
      <a href="#" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-dollar-sign"></i> Finance
      </a>
      @endcan
      
      @can('employees.view')
      <a href="#" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-users-cog"></i> HR
      </a>
      @endcan
      
      <hr style="border-color: rgba(255,255,255,0.2); margin: 15px 0;">
      
      <!-- Additional Pages -->
      <a href="{{ route('additional-page.about') }}" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-info-circle"></i> About Us
      </a>
      <a href="{{ route('additional-page.terms') }}" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-file-contract"></i> Terms of Service
      </a>
      <a href="{{ route('additional-page.privacy') }}" class="list-group-item" style="background-color: #007bff; border: none;">
        <i class="fas fa-shield-alt"></i> Privacy Policy
      </a>
      
      <div class="list-group-item" style="background-color: #007bff; border: none; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 10px;">
        <a href="{{ route('auth.sign-out') }}" class="text-white text-decoration-none">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #007bff;">
    <div class="container-fluid">
      <button class="btn btn-menu-toggle me-3" id="menu-toggle" title="Toggle Menu">
        <i class="fas fa-bars"></i>
      </button>
      
      <a class="navbar-brand" href="{{ route('home') }}">
        <img src="{{ asset('favicon/android-icon-36x36.png') }}" alt="{{ config('app.name') }}" width="32" height="32" class="me-2">
        {{ config('app.name') }}
      </a>
      
      <div class="ms-auto">
        <div class="dropdown">
          <button class="btn btn-link text-light text-decoration-none dropdown-toggle" 
                  type="button" 
                  id="userDropdown" 
                  data-bs-toggle="dropdown" 
                  aria-expanded="false">
            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="{{ route('auth.profile') }}">
              <i class="fas fa-user-edit me-2"></i>Profile Settings
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('auth.sign-out') }}">
              <i class="fas fa-sign-out-alt me-2"></i>Sign Out
            </a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <main class="main-content" id="main-content">
    <div class="container-fluid pt-4">
      <!-- Global Error Messages -->
      @if(session('error'))
      <div class="row mb-3">
        <div class="col-12">
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
      @endif

      <!-- Global Success Messages -->
      @if(session('success'))
      <div class="row mb-3">
        <div class="col-12">
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
      @endif

      <!-- Global Validation Errors -->
      @if($errors->any())
      <div class="row mb-3">
        <div class="col-12">
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
      @endif

      @yield('content')
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Toggle Sidebar
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');
    const mainContent = document.getElementById('main-content');

    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('sidebar-open');
    });

    // Close Sidebar when clicking outside
    document.addEventListener('click', function(event) {
      if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
        sidebar.classList.remove('active');
        mainContent.classList.remove('sidebar-open');
      }
    });

    // Close sidebar on mobile when clicking a link
    const sidebarLinks = document.querySelectorAll('#sidebar a');
    sidebarLinks.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          sidebar.classList.remove('active');
          mainContent.classList.remove('sidebar-open');
        }
      });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('active');
        mainContent.classList.remove('sidebar-open');
      }
    });

    // Global Alert Management
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-hide alerts after 8 seconds
      setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
          alert.style.transition = 'opacity 0.5s ease';
          alert.style.opacity = '0';
          setTimeout(() => alert.remove(), 500);
        });
      }, 8000);
      
      // Add smooth scrolling to error messages
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.addEventListener('click', function() {
          window.scrollTo({
            top: 0,
            behavior: 'smooth'
          });
        });
      });
    });
  </script>

  @include('shared.script')
</body>
</html>
