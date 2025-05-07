<!DOCTYPE html>
<html lang="en">
<head>
    @include('shared.meta')
    @include('shared.stylesheet')
    <style>
        body {
            overflow-x: hidden;
        }
        #sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            color: #fff;
            transition: left 0.3s ease;
            z-index: 1050;
        }
        #sidebar.active {
            left: 0;
        }
        #sidebar .list-group-item {
            color: #fff;
            border: none;
        }
        #sidebar .list-group-item:hover {
            background: #495057;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>
  <!-- Sidebar -->
  <div id="sidebar" class="bg-dark">
    <div class="list-group list-group-flush">
      <a href="{{ route('additional-page.about') }}" class="list-group-item bg-dark">Tentang Kami</a>
      <a href="{{ route('additional-page.terms') }}" class="list-group-item bg-dark">Syarat Layanan</a>
      <a href="{{ route('additional-page.privacy') }}" class="list-group-item bg-dark">Kebijakan Privasi</a>
      <a href="{{ route('auth.sign-out') }}" class="list-group-item bg-dark">Logout</a>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="btn btn-dark mr-3" id="menu-toggle">
        <i class="fas fa-bars"></i> <!-- Add FontAwesome for icons -->
    </button>
    <button class="btn btn-outline-light ml-auto" id="create-item">
        <i class="fas fa-plus"></i> <!-- Add FontAwesome for icons -->
    </button>
  </nav>

  <main class="container-fluid pt-4">
    @yield('content')
  </main>

  <script>
    // Toggle Sidebar
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');

    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('active');
    });

    // Close Sidebar when clicking outside
    document.addEventListener('click', function(event) {
      if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
        sidebar.classList.remove('active');
      }
    });
  </script>

  @include('shared.script')
</body>
</html>
