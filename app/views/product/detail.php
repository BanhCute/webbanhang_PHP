<?php
// Bỏ dòng require config, vì nó đã được include từ ProductController
$pageTitle = $product['name'];
$currentPage = 'product';
require_once ROOT_PATH . '/app/views/shares/header.php';
?>
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/T6-Sang/webbanhang/product">Sản phẩm</a></li>
            <li class="breadcrumb-item"><a href="/T6-Sang/webbanhang/product?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <!-- Thêm phần danh mục -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list text-primary me-2"></i>
                        Danh mục sản phẩm
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        // Lấy danh sách danh mục từ controller
                        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
                        $categories = $stmt->fetchAll();
                        foreach ($categories as $category):
                        ?>
                            <div class="col-auto mb-2">
                                <a href="/T6-Sang/webbanhang/product?category=<?= $category['id'] ?>"
                                    class="btn <?= ($category['id'] == $product['category_id']) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                    <?= htmlspecialchars($category['name']) ?>
                                    <?php if ($category['id'] == $product['category_id']): ?>
                                        <i class="fas fa-check ms-1"></i>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phần thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
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

    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Phần hình ảnh sản phẩm -->
                <div class="col-md-6">
                    <div class="position-relative">
                        <img src="/T6-Sang/webbanhang/public/uploads/products/<?= $product['image'] ?? 'no-image.jpg' ?>"
                            class="img-fluid rounded"
                            alt="<?= htmlspecialchars($product['name']) ?>">

                        <!-- Badge trả góp -->
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-primary">Trả góp 0%</span>
                        </div>

                        <!-- Badge giảm giá nếu có -->
                        <?php if (isset($product['discount']) && $product['discount'] > 0): ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-danger">-<?= $product['discount'] ?>%</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Phần thông tin sản phẩm -->
                <div class="col-md-6">
                    <h1><?= htmlspecialchars($product['name']) ?></h1>
                    <div class="price mb-3">
                        <h3 class="text-danger"><?= number_format($product['price']) ?> đ</h3>
                    </div>

                    <!-- Thêm thông tin danh mục -->
                    <div class="mb-3">
                        <span class="text-muted">Danh mục:</span>
                        <span class="badge bg-primary">
                            <?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?>
                        </span>
                    </div>

                    <!-- Giá -->
                    <div class="mb-4">
                        <div class="fs-3 text-danger fw-bold">
                            <?= number_format($product['price']) ?> đ
                        </div>
                        <?php if (isset($product['old_price'])): ?>
                            <div class="text-decoration-line-through text-muted">
                                <?= number_format($product['old_price']) ?> đ
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Thông tin trả góp -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                Thông tin trả góp
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Trả góp lãi suất 0%
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Thời gian duyệt nhanh 5 phút
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    Không cần thế chấp tài sản
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Mô tả ngắn -->
                    <div class="mb-4">
                        <h5>Mô tả sản phẩm</h5>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>

                    <!-- Phần thêm vào giỏ hàng -->
                    <div class="product-actions mt-4">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <!-- Chưa đăng nhập -->
                            <div class="row g-2">
                                <div class="col-12 mb-3">
                                    <div class="quantity-input">
                                        <button type="button" class="btn-minus">-</button>
                                        <input type="text" value="1" class="quantity-value" readonly>
                                        <button type="button" class="btn-plus">+</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary w-100" onclick="requireLogin()">
                                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-danger w-100" onclick="requireLogin()">
                                        <i class="fas fa-bolt me-2"></i>Mua ngay
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Đã đăng nhập -->
                            <div class="row g-2">
                                <div class="col-12 mb-3">
                                    <div class="quantity-input">
                                        <button type="button" class="btn-minus">-</button>
                                        <input type="text" id="quantity" class="quantity-value" value="1" readonly>
                                        <button type="button" class="btn-plus">+</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary w-100" onclick="addToCart('add_to_cart')">
                                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-danger w-100" onclick="addToCart('buy_now')">
                                        <i class="fas fa-bolt me-2"></i>Mua ngay
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="row mt-5">
                <div class="col-12">
                    <!-- Mô tả chi tiết -->
                    <div class="product-section mb-4">
                        <h4 class="section-title bg-light p-3 rounded">Mô tả chi tiết</h4>
                        <div class="section-content p-3 border rounded">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                    </div>

                    <!-- Thông số kỹ thuật -->
                    <div class="product-section">
                        <h4 class="section-title bg-light p-3 rounded">Thông số kỹ thuật</h4>
                        <div class="section-content p-3 border rounded">
                            <table class="table table-striped mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">CPU:</th>
                                        <td><?= $product['cpu'] ?? 'Intel Core i7 12th Gen' ?></td>
                                    </tr>
                                    <tr>
                                        <th>RAM:</th>
                                        <td><?= $product['ram'] ?? '16GB DDR5' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ổ cứng:</th>
                                        <td><?= $product['storage'] ?? '512GB SSD NVMe' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Màn hình:</th>
                                        <td><?= $product['screen'] ?? '13.4 inch 4K (3840 x 2400)' ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Sản phẩm liên quan</h5>
            </div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <div class="col">
                            <div class="card h-100">
                                <img src="/T6-Sang/webbanhang/public/uploads/products/<?= $relatedProduct['image'] ?? 'no-image.jpg' ?>"
                                    class="card-img-top"
                                    alt="<?= htmlspecialchars($relatedProduct['name']) ?>"
                                    style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($relatedProduct['name']) ?></h6>
                                    <div class="text-danger fw-bold">
                                        <?= number_format($relatedProduct['price']) ?> đ
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="/T6-Sang/webbanhang/product/detail/<?= $relatedProduct['id'] ?>"
                                        class="btn btn-outline-primary btn-sm w-100">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Thêm thư viện jQuery và SweetAlert2 ở đầu file -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const ROOT_URL = '<?= ROOT_URL ?>';
    const PRODUCT_ID = <?= $product['id'] ?>;
    const PRODUCT_NAME = '<?= addslashes($product['name']) ?>';
    const PRODUCT_IMAGE = '<?= $product['image'] ?>';

    document.addEventListener('DOMContentLoaded', function() {
        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const quantityInput = document.querySelector('.quantity-value');

        if (minusBtn && plusBtn && quantityInput) {
            minusBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });

            plusBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                quantityInput.value = value + 1;
            });
        }
    });

    // Hàm yêu cầu đăng nhập
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

    // Hàm xử lý thêm vào giỏ hàng
    function addToCart(action) {
        const quantity = document.querySelector('#quantity').value;
        const formData = new FormData();
        formData.append('product_id', PRODUCT_ID);
        formData.append('quantity', quantity);
        formData.append('action', action);

        // Tạo hiệu ứng bay vào giỏ hàng nếu là thêm vào giỏ
        if (action === 'add_to_cart') {
            const imgSrc = `${ROOT_URL}/public/uploads/products/${PRODUCT_IMAGE}`;
            createFlyingImage(imgSrc, document.querySelector('.btn-primary'));
        }

        fetch(`${ROOT_URL}/Product/addToCart`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật số lượng giỏ hàng
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cartCount;
                        cartCount.classList.add('highlight');
                        setTimeout(() => cartCount.classList.remove('highlight'), 1000);
                    }

                    if (action === 'buy_now') {
                        window.location.href = `${ROOT_URL}/Product/cart`;
                    } else {
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể kết nối đến máy chủ'
                });
            });
    }

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
</script>

<!-- CSS để ngăn việc nhập trực tiếp vào input số lượng -->
<style>
    #qty {
        -webkit-appearance: none;
        -moz-appearance: textfield;
    }

    #qty::-webkit-outer-spin-button,
    #qty::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<!-- Thêm SweetAlert2 vào đầu trang -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<style>
    .swal2-popup .swal2-actions {
        gap: 10px;
    }

    .swal2-confirm {
        background-color: #28a745 !important;
    }

    .swal2-cancel {
        background-color: #6c757d !important;
    }

    .badge-cart {
        position: relative;
        top: -8px;
        margin-left: 3px;
        font-size: 0.75em;
    }

    /* Style cho tabs */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        color: #495057;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        margin-right: 5px;
        padding: 10px 20px;
    }

    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: 500;
    }

    /* Style cho nội dung tab */
    .tab-content {
        background-color: #fff;
        min-height: 200px;
    }

    .product-description {
        line-height: 1.6;
    }

    .product-specification th {
        background-color: #f8f9fa;
        font-weight: 500;
    }

    /* Đảm bảo nội dung tab luôn hiển thị đúng */
    .tab-pane.active {
        display: block !important;
        opacity: 1 !important;
    }

    .tab-pane {
        transition: opacity 0.3s ease-in-out;
    }

    .tab-pane.fade {
        opacity: 0;
    }

    .tab-pane.fade.show {
        opacity: 1;
    }

    .nav-tabs .nav-link {
        color: #495057;
        cursor: pointer;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 500;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 500;
        margin-bottom: 0;
        border: 1px solid #dee2e6;
    }

    .section-content {
        background: #fff;
        border-color: #dee2e6;
    }

    .product-section {
        margin-bottom: 30px;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 500;
    }

    .quantity-input {
        display: inline-flex;
        align-items: center;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
    }

    .quantity-input button {
        width: 40px;
        height: 38px;
        background: none;
        border: none;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        transition: all 0.2s;
    }

    .quantity-input button:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
    }

    .quantity-input .quantity-value {
        width: 50px;
        height: 38px;
        border: none;
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        text-align: center;
        font-size: 14px;
        padding: 0;
        -moz-appearance: textfield;
        background: #fff;
    }

    .quantity-value::-webkit-outer-spin-button,
    .quantity-value::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .quantity-value:focus {
        outline: none;
    }

    /* Thêm hiệu ứng khi hover vào cả group */
    .quantity-input:hover {
        border-color: #0d6efd;
    }

    .swal2-popup .btn {
        margin: 5px;
        padding: 8px 20px;
        border-radius: 4px;
    }

    .swal2-popup .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .swal2-popup .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
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

    /* Thêm style cho toast */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
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
</style>

<?php require_once ROOT_PATH . '/app/views/shares/footer.php'; ?>