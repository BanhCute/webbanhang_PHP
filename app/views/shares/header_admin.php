<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Trang quản trị' ?> - Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .admin-header {
            background: #2c3e50;
            padding: 1rem;
            color: white;
        }

        .admin-nav {
            background: #34495e;
            padding: 0.5rem;
        }

        .admin-nav .nav-link {
            color: white;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 4px;
        }

        .admin-nav .nav-link:hover,
        .admin-nav .nav-link.active {
            background: #2980b9;
            color: white;
        }

        .admin-content {
            padding: 2rem 0;
        }

        .logout-btn {
            color: white;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: #e74c3c;
        }

        .pagination .page-link {
            color: #2c3e50;
            border-radius: 4px;
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background-color: #2c3e50;
            border-color: #2c3e50;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #34495e;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Trang Quản Trị</h4>
                <div>
                    <span class="me-3">Xin chào, <?= $_SESSION['username'] ?? 'Admin' ?></span>
                    <a href="<?= ROOT_URL ?>/Account/logout" class="logout-btn">
                        <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="admin-nav">
        <div class="container-fluid">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'product' ? 'active' : '' ?>"
                        href="<?= ROOT_URL ?>/admin/product">
                        <i class="fas fa-box me-2"></i>Quản lý sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'category' ? 'active' : '' ?>"
                        href="<?= ROOT_URL ?>/admin/category">
                        <i class="fas fa-list me-2"></i>Quản lý danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'user' ? 'active' : '' ?>"
                        href="<?= ROOT_URL ?>/admin/user">
                        <i class="fas fa-users me-2"></i>Quản lý người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'order' ? 'active' : '' ?>"
                        href="<?= ROOT_URL ?>/admin/order">
                        <i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= ROOT_URL ?>" target="_blank">
                        <i class="fas fa-home me-2"></i>Xem trang chủ
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="container-fluid">
            <div class="row">


                <!-- Main content -->
                <div class="col-md-10 ms-auto">
                    <!-- Top navbar -->
                    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                        <div class="container-fluid">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav ms-auto">
                                    <li class="nav-item dropdown">

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="<?= ROOT_URL ?>/Account/logout">
                                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>