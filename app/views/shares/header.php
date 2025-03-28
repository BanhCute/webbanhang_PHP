<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session trong header
error_log('Session in header: ' . print_r($_SESSION, true));

require_once __DIR__ . '/../../helpers/SessionHelper.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Quản lý sản phẩm'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            padding: 1rem 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-link {
            font-weight: 500;
            padding: 0.7rem 1.2rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin: 0 0.3rem;
            position: relative;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
            background-color: var(--secondary-color);
            border: 2px solid white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: translate(50%, -50%) scale(1);
            }

            50% {
                transform: translate(50%, -50%) scale(1.1);
            }

            100% {
                transform: translate(50%, -50%) scale(1);
            }
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .card {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 991.98px) {
            .navbar-nav {
                padding: 1rem 0;
            }

            .nav-link {
                margin: 0.5rem 0;
            }
        }

        .avatar-placeholder {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }

        .avatar-placeholder:hover {
            border-color: var(--primary-color);
            background-color: #e9ecef;
        }

        .info-section,
        .password-section {
            background-color: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(44, 62, 80, 0.25);
        }

        .input-group .btn-outline-secondary:focus {
            box-shadow: none;
        }

        .badge {
            font-size: 0.9em;
            padding: 0.5em 1em;
        }

        .cart-icon {
            position: relative;
            display: inline-block;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            min-width: 18px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .cart-count.highlight {
            transform: scale(1.5);
            background-color: #28a745;
        }

        @keyframes cartBounce {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .cart-animation {
            animation: cartBounce 0.5s ease;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Top Bar -->
    <div class="bg-dark text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>
                        <i class="fas fa-phone-alt me-2"></i>Hotline: 0123 456 789
                        <span class="mx-3">|</span>
                        <i class="fas fa-envelope me-2"></i>Email: contact@shopanh.com
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small>
                        <i class="fas fa-truck me-2"></i>Miễn phí vận chuyển cho đơn hàng trên 500K
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">

            <a class="navbar-brand" href="/T6-Sang/webbanhang/Product/list">
                <i class="bi bi-shop"></i> Shop Bảo Anh
            </a>


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/T6-Sang/webbanhang/Product/list">
                            <i class="bi bi-shop"></i> Sản phẩm
                        </a>
                    </li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>


                        <a class="nav-link" href="/T6-Sang/webbanhang/admin/product">
                            <i class="fas fa-cog"></i>Trang quản lý
                        </a>


                    <?php endif; ?>
                </ul>
                <ul ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                        // Lấy số lượng giỏ hàng từ database
                        $stmt = $this->db->prepare("
                            SELECT SUM(quantity) as total 
                            FROM carts 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $cartCount = $stmt->fetch()['total'] ?? 0;
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="<?= ROOT_URL ?>/Product/cart">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative cart-icon">
                                        <i class="fas fa-shopping-cart fs-5"></i>
                                        <?php if ($cartCount > 0): ?>
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" <?= (!isset($_SESSION['cart_count']) || $_SESSION['cart_count'] == 0) ? 'style="display: none;"' : '' ?>>
                                                <?= isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="ms-2">Giỏ hàng của tôi</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?= $_SESSION['username'] ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= ROOT_URL ?>/Account/profile">
                                        <i class="fas fa-user-circle me-2"></i>Tài khoản của tôi
                                    </a></li>
                                <li><a class="dropdown-item" href="<?= ROOT_URL ?>/Product/cart">
                                        <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng
                                    </a></li>
                                <li><a class="dropdown-item" href="<?= ROOT_URL ?>/Order/history">
                                        <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= ROOT_URL ?>/Account/logout">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/Account/login">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/Account/register">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sau thẻ nav -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>



    <!-- Thêm script vào cuối file, trước </body> -->
    <script>
        // Khởi tạo biến để theo dõi số lượng giỏ hàng
        let currentCartCount = <?= isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0 ?>;

        // Hàm cập nhật số lượng giỏ hàng
        function updateCartCount(newCount) {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                // Lưu số lượng cũ
                const oldCount = parseInt(cartCountElement.textContent) || 0;
                // Cập nhật số lượng mới
                currentCartCount = newCount;

                // Cập nhật hiển thị
                cartCountElement.textContent = newCount;

                // Thêm hiệu ứng highlight nếu số lượng thay đổi
                if (newCount > oldCount) {
                    cartCountElement.classList.add('highlight');
                    setTimeout(() => {
                        cartCountElement.classList.remove('highlight');
                    }, 1000);
                }
            }
        }
    </script>
</body>

</html>