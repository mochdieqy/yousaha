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
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a href="@yield('back')" class="btn btn-dark mr-3">
        <i class="fas fa-arrow-left"></i> <!-- Add FontAwesome for icons -->
    </a>
  </nav>

  <main class="container-fluid pt-4">
    @yield('content')
  </main>

  @include('shared.script')
</body>
</html>
