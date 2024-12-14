<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include("config.php"); // Include your database connection file
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventura - Real Estate Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #000066;
            --secondary-color: #0000cc;
            --accent-color: #4d4dff;
            --text-color: #333;
            --bg-light: #f8f9fa;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: var(--text-color);
            background-color: var(--bg-light);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            transition: var(--transition);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.1);
        }

        .sidebar-header img {
            max-width: 100%;
            height: auto;
        }

        .sidebar-menu {
            padding: 0;
            list-style-type: none;
            margin-top: 20px;
        }

        .sidebar-menu li {
            padding: 10px 20px;
            transition: var(--transition);
        }

        .sidebar-menu li:hover {
            background-color: var(--secondary-color);
        }

        .sidebar-menu li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu li a span {
            transition: var(--transition);
        }

        .sidebar.collapsed .sidebar-menu li a span {
            display: none;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: var(--transition);
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-color);
            cursor: pointer;
        }

        .user-menu img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px;
        }

        .dash-widget-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background-color: var(--accent-color);
            color: white;
            margin-right: 15px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .dropdown-item:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .list-group-item {
            border: none;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            transition: var(--transition);
        }

        .list-group-item:hover {
            background-color: var(--bg-light);
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .sidebar.collapsed {
                width: 100%;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.expanded {
                margin-left: 0;
            }
            .toggle-sidebar {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .card-deck {
                flex-direction: column;
            }
        }

        .mobile-menu {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu {
                display: flex;
                justify-content: space-around;
                background-color: var(--primary-color);
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 10px;
                z-index: 1000;
            }
            .mobile-menu a {
                color: white;
                text-decoration: none;
                display: flex;
                flex-direction: column;
                align-items: center;
                font-size: 12px;
            }
            .mobile-menu i {
                font-size: 20px;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="assets/img/rsadmin.png" alt="Ventura Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="#"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-users"></i><span>Users</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">All Users</a></li>
                    <li><a class="dropdown-item" href="#">Admins</a></li>
                    <li><a class="dropdown-item" href="#">Agents</a></li>
                    <li><a class="dropdown-item" href="#">Clients</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-building"></i><span>Properties</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">All Properties</a></li>
                    <li><a class="dropdown-item" href="#">Add Property</a></li>
                    <li><a class="dropdown-item" href="#">Edit Property</a></li>
                    <li><a class="dropdown-item" href="#">Delete Property</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fas fa-map-marker-alt"></i><span>Locations</span></a></li>
            <li><a href="#"><i class="fas fa-comments"></i><span>Queries</span></a></li>
            <li><a href="#"><i class="fas fa-cog"></i><span>Settings</span></a></li>
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <button class="toggle-sidebar me-3" id="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#">Ventura Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="assets/img/profiles/avatar-01.png" alt="Admin" class="rounded-circle me-2" width="32" height="32">
                                <span>Admin</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <h1 class="mb-4">Dashboard</h1>
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="dash-widget-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php 
                                    $sql = "SELECT * FROM users";
                                    $query = $con->query($sql);
                                    echo $query->num_rows;
                                ?></h3>
                                    <p class="text-muted mb-0">Registered Users</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="dash-widget-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php $sql = "SELECT * FROM agents"; $query = $con->query($sql); echo $query->num_rows; ?></h3>
                                    <p class="text-muted mb-0">Agents</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="dash-widget-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php $sql = "SELECT * FROM property"; $query = $con->query($sql); echo $query->num_rows; ?></h3>
                                    <p class="text-muted mb-0">Properties</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="dash-widget-icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php $sql = "SELECT * FROM property WHERE type = 'apartment'"; $query = $con->query($sql); echo $query->num_rows; ?></h3>
                                    <p class="text-muted mb-0">Apartments</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Properties</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Sunset Apartments
                                    <span class="badge bg-primary">New</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Green Valley Homes
                                    <span class="badge bg-secondary">Updated</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    City View Lofts
                                    <span class="badge bg-success">Sold</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Testimonials</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6  class="mb-0">John Doe</h6>
                                            <small class="text-muted">Interested in Sunset Apartments</small>
                                        </div>
                                        <span class="badge bg-info">New</span>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6  class="mb-0">Jane Smith</h6>
                                            <small class="text-muted">Question about Green Valley Homes</small>
                                        </div>
                                        <span class="badge bg-warning">Pending</span>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Mike Johnson</h6>
                                            <small class="text-muted">Booking request for City View Lofts</small>
                                        </div>
                                        <span class="badge bg-success">Resolved</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Property Sales</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="propertySalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User Growth</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="userGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile-menu">
        <a href="#"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="#"><i class="fas fa-building"></i><span>Properties</span></a>
        <a href="#"><i class="fas fa-users"></i><span>Users</span></a>
        <a href="#"><i class="fas fa-cog"></i><span>Settings</span></a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#toggle-sidebar').click(function() {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            });

            // Responsive sidebar behavior
            function checkWidth() {
                if ($(window).width() < 768) {
                    $('#sidebar').addClass('collapsed');
                    $('#main-content').addClass('expanded');
                } else {
                    $('#sidebar').removeClass('collapsed');
                    $('#main-content').removeClass('expanded');
                }
            }

            // Check width on page load and window resize
            checkWidth();
            $(window).resize(checkWidth);

            // Add active class to clicked sidebar item
            $('.sidebar-menu li').click(function() {
                $('.sidebar-menu li').removeClass('active');
                $(this).addClass('active');
            });

            // Simulate loading of dashboard data
            function loadDashboardData() {
                $('.dash-widget-icon').each(function(index) {
                    $(this).css('transform', 'scale(0)');
                    setTimeout(() => {
                        $(this).css('transform', 'scale(1)');
                    }, index * 200);
                });
            }

            loadDashboardData();

            // Initialize Bootstrap dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });

            // Property Sales Chart
            var propertySalesCtx = document.getElementById('propertySalesChart').getContext('2d');
            var propertySalesChart = new Chart(propertySalesCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Sales',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: 'rgba(0, 0, 102, 0.6)',
                        borderColor: 'rgba(0, 0, 102, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // User Growth Chart
            var userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
            var userGrowthChart = new Chart(userGrowthCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Users',
                        data: [65, 59, 80, 81, 56, 55],
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>