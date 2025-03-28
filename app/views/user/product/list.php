<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Danh sách sản phẩm';
$currentPage = 'product';
require_once ROOT_PATH . '/app/views/shares/header.php';

// Debug session
error_log('Current session data in list view: ' . print_r($_SESSION, true));

// Thêm vào đầu file sau phần navbar
if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>


<div class="container mt-4">
    <!-- Form tìm kiếm và lọc -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" action="<?= ROOT_URL ?>/Product/list" class="row g-2">
                <!-- Ô tìm kiếm -->
                <div class="col-lg-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search"
                            placeholder="Tìm kiếm sản phẩm..."
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                </div>

                <!-- Lọc danh mục -->
                <div class="col-lg-3">
                    <select class="form-select" name="category_id">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"
                                <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Lọc giá -->
                <div class="col-lg-2">
                    <select class="form-select" name="price_range">
                        <option value="">Tất cả giá</option>
                        <option value="0-500000" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '0-500000') ? 'selected' : '' ?>>
                            Dưới 500.000đ
                        </option>
                        <option value="500000-1000000" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '500000-1000000') ? 'selected' : '' ?>>
                            500.000đ - 1.000.000đ
                        </option>
                        <option value="1000000-5000000" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '1000000-5000000') ? 'selected' : '' ?>>
                            1.000.000đ - 5.000.000đ
                        </option>
                        <option value="5000000" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '5000000') ? 'selected' : '' ?>>
                            Trên 5.000.000đ
                        </option>
                    </select>
                </div>

                <!-- Sắp xếp -->
                <div class="col-lg-2">
                    <select class="form-select" name="sort">
                        <option value="">Sắp xếp mặc định</option>
                        <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>
                            Giá tăng dần
                        </option>
                        <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>
                            Giá giảm dần
                        </option>
                        <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>
                            Tên A-Z
                        </option>
                        <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>
                            Tên Z-A
                        </option>
                    </select>
                </div>

                <!-- Nút tìm kiếm và reset -->
                <div class="col-lg-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                        <a href="<?= ROOT_URL ?>/Product/list" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hiển thị sản phẩm -->
    <div class="row">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">Không tìm thấy sản phẩm nào</h4>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="<?= ROOT_URL ?>/public/uploads/products/<?= $product['image'] ?: 'no-image.png' ?>"
                            class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>"
                            style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-truncate">
                                <?= htmlspecialchars($product['name']) ?>
                            </h5>
                            <p class="card-text text-primary fw-bold">
                                <?= number_format($product['price'], 0, ',', '.') ?>đ
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-tag me-1"></i><?= htmlspecialchars($product['category_name']) ?>
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex gap-2">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <!-- Người dùng đã đăng nhập -->
                                    <button type="button"
                                        class="btn btn-outline-primary flex-grow-1 add-to-cart"
                                        data-product-id="<?= $product['id'] ?>"
                                        data-product-name="<?= htmlspecialchars($product['name']) ?>"
                                        data-product-image="<?= $product['image'] ?: 'no-image.png' ?>">
                                        <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                                    </button>
                                    <button type="button"
                                        class="btn btn-primary flex-grow-1 buy-now"
                                        data-product-id="<?= $product['id'] ?>">
                                        <i class="fas fa-bolt me-1"></i> Mua ngay
                                    </button>
                                <?php else: ?>
                                    <!-- Người dùng chưa đăng nhập -->
                                    <button type="button"
                                        class="btn btn-outline-primary flex-grow-1"
                                        onclick="requireLogin()">
                                        <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                                    </button>
                                    <button type="button"
                                        class="btn btn-primary flex-grow-1"
                                        onclick="requireLogin()">
                                        <i class="fas fa-bolt me-1"></i> Mua ngay
                                    </button>
                                <?php endif; ?>
                            </div>
                            <a href="<?= ROOT_URL ?>/Product/detail/<?= $product['id'] ?>"
                                class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-info-circle me-1"></i> Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- Nút Previous -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $this->buildPageUrl($page - 1) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>

                <!-- Các trang -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $this->buildPageUrl($i) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Nút Next -->
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $this->buildPageUrl($page + 1) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script>
    // Định nghĩa ROOT_URL cho JavaScript
    const ROOT_URL = '<?= ROOT_URL ?>';

    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productImage = this.dataset.productImage;
                const imgSrc = `${ROOT_URL}/public/uploads/products/${productImage}`;

                // Tạo hiệu ứng bay vào giỏ hàng
                createFlyingImage(imgSrc, this);

                // Gửi request AJAX
                fetch(`${ROOT_URL}/Product/addToCart`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}&quantity=1&action=add_to_cart`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật số lượng giỏ hàng ngay lập tức
                            const cartCount = document.querySelector('.cart-count');
                            if (cartCount) {
                                // Nếu chưa có số lượng (giỏ hàng trống)
                                if (!cartCount.textContent || cartCount.textContent === '0') {
                                    cartCount.style.display = 'inline-block'; // Hiển thị badge nếu ẩn
                                }
                                cartCount.textContent = data.cartCount;
                                cartCount.classList.add('highlight');
                                setTimeout(() => cartCount.classList.remove('highlight'), 1000);
                            }

                            // Hiển thị thông báo
                            showToast('success', `Đã thêm ${productName} vào giỏ hàng (Số lượng: ${data.cartCount})`);

                            // Thêm hiệu ứng cho icon giỏ hàng
                            const cartIcon = document.querySelector('.cart-icon');
                            if (cartIcon) {
                                cartIcon.classList.add('cart-animation');
                                setTimeout(() => cartIcon.classList.remove('cart-animation'), 500);
                            }
                        } else {
                            showToast('error', data.message || 'Không thể thêm sản phẩm vào giỏ hàng');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Không thể thêm sản phẩm vào giỏ hàng');
                    });
            });
        });

        // Xử lý mua ngay
        document.querySelectorAll('.buy-now').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;

                fetch(`${ROOT_URL}/Product/addToCart`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}&quantity=1&action=buy_now`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = `${ROOT_URL}/Product/cart`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Không thể thực hiện mua ngay');
                    });
            });
        });

        // Tự động submit form khi thay đổi select
        const selects = document.querySelectorAll('select[name="category_id"], select[name="price_range"], select[name="sort"]');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    });

    function createFlyingImage(imgSrc, button) {
        const flyingImg = document.createElement('div');
        flyingImg.className = 'product-fly';
        flyingImg.style.backgroundImage = `url(${imgSrc})`;

        const buttonRect = button.getBoundingClientRect();
        flyingImg.style.top = buttonRect.top + window.scrollY + 'px';
        flyingImg.style.left = buttonRect.left + 'px';

        document.body.appendChild(flyingImg);

        const cart = document.querySelector('.cart-icon');
        const cartRect = cart.getBoundingClientRect();

        requestAnimationFrame(() => {
            flyingImg.style.top = (cartRect.top + window.scrollY) + 'px';
            flyingImg.style.left = cartRect.left + 'px';
            flyingImg.style.transform = 'scale(0.1)';
            flyingImg.style.opacity = '0';
        });

        setTimeout(() => {
            document.body.removeChild(flyingImg);
            cart.classList.add('cart-icon-animate');
            setTimeout(() => cart.classList.remove('cart-icon-animate'), 500);
        }, 800);
    }

    function updateCartCount(count) {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.classList.add('highlight');
            setTimeout(() => cartCount.classList.remove('highlight'), 300);
        }
    }

    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast show align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        // Tạo container nếu chưa có
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        container.appendChild(toast);

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    function requireLogin() {
        Swal.fire({
            title: 'Yêu cầu đăng nhập',
            text: 'Vui lòng đăng nhập để thực hiện chức năng này',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Đăng nhập',
            cancelButtonText: 'Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${ROOT_URL}/Account/login`;
            }
        });
    }
</script>

<style>
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Sửa lại style cho badge category */
    .badge.bg-primary {
        position: relative;
        z-index: 3;
        transition: all 0.3s ease;
    }

    .product-card:hover .badge.bg-primary {
        transform: translateY(-5px);
    }

    /* Container cho ảnh sản phẩm */
    .card-img-container {
        overflow: hidden;
        position: relative;
    }

    .card-img-top {
        transition: transform 0.3s ease;
        height: 200px;
        object-fit: cover;
    }

    .product-card:hover .card-img-top {
        transform: scale(1.05);
    }

    /* Style cho card body */
    .card-body {
        position: relative;
        z-index: 2;
        background: white;
    }

    .card-title {
        height: 48px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    /* Style cho giá và badge */
    .price-badge-container {
        position: relative;
        z-index: 2;
        background: white;
    }

    .text-danger.fw-bold {
        font-size: 1.1rem;
    }

    /* Style cho card footer */
    .card-footer {
        position: relative;
        z-index: 2;
        background: white !important;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        padding: 1rem;
    }

    /* Style cho buttons */
    .input-group {
        flex-wrap: nowrap;
    }

    .input-group .form-control {
        width: 50px;
        text-align: center;
        padding: 0.375rem 0.5rem;
    }

    .input-group .btn {
        padding: 0.375rem 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Badge cart */
    .badge-cart {
        position: relative;
        top: -8px;
        margin-left: 3px;
        font-size: 0.75em;
    }

    /* SweetAlert styles */
    .swal2-popup .swal2-actions {
        gap: 10px;
    }

    .swal2-confirm {
        background-color: #28a745 !important;
    }

    .swal2-cancel {
        background-color: #6c757d !important;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .product-card {
            margin-bottom: 1rem;
        }

        .card-footer .d-flex {
            flex-direction: column;
        }

        .card-footer .d-flex .input-group {
            margin-bottom: 0.5rem;
        }
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

    .cart-icon-animate {
        animation: cartBounce 0.5s ease-in-out;
    }

    .product-fly {
        position: fixed;
        z-index: 9999;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        pointer-events: none;
        transition: all 0.8s cubic-bezier(0.25, 0.75, 0.25, 0.75);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    @keyframes highlight {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
            background-color: #28a745;
        }

        100% {
            transform: scale(1);
        }
    }

    .cart-count.highlight {
        animation: highlight 0.3s ease-in-out;
    }

    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }

    .cart-count {
        transition: all 0.3s ease;
    }

    .cart-count.highlight {
        transform: scale(1.5);
        color: #28a745;
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

    /* Thêm style cho cart count */
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

    .cart-icon {
        position: relative;
        display: inline-block;
    }
</style>

<?php require_once ROOT_PATH . '/app/views/shares/footer.php'; ?>