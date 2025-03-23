<?php
$pageTitle = 'Giỏ hàng của tôi';
$currentPage = 'cart';
?>

<!-- Thêm SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<?php require_once ROOT_PATH . '/app/views/shares/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">
        <i class="fas fa-shopping-cart text-primary me-2"></i>Giỏ hàng của tôi
    </h1>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h3 class="text-muted mb-4">Giỏ hàng của bạn đang trống</h3>
            <a href="<?= ROOT_URL ?>/Product/list" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Danh sách sản phẩm -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cartItems as $index => $item): ?>
                            <div class="row mb-4 align-items-center cart-item" data-product-id="<?= $item['product_id'] ?>">
                                <div class="col-md-2">
                                    <img src="<?= ROOT_URL ?>/public/uploads/products/<?= htmlspecialchars($item['image']) ?>"
                                        class="img-fluid rounded"
                                        alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <h5 class="mb-2"><?= htmlspecialchars($item['name']) ?></h5>
                                    <p class="text-muted mb-0">Đơn giá: <?= number_format($item['price']) ?>đ</p>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary decrease-quantity">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center quantity-input"
                                            value="<?= $item['quantity'] ?>" min="1">
                                        <button type="button" class="btn btn-outline-secondary increase-quantity">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong class="item-subtotal"><?= number_format($item['subtotal']) ?>đ</strong>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-link text-danger remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if ($index < count($cartItems) - 1): ?>
                                <hr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Tổng đơn hàng -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Tổng đơn hàng</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <strong class="cart-subtotal"><?= number_format($total) ?>đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span>Tổng cộng:</span>
                            <strong class="text-danger fs-5 cart-total"><?= number_format($total) ?>đ</strong>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="<?= ROOT_URL ?>/Product/checkout" class="btn btn-primary">
                                <i class="fas fa-credit-card me-2"></i>Tiến hành thanh toán
                            </a>
                            <a href="<?= ROOT_URL ?>/Product/list" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .cart-container {
        position: relative;
        z-index: 1;
    }

    .cart-summary {
        position: sticky;
        top: 20px;
        z-index: 0;
    }

    .product-row {
        background: #fff;
        position: relative;
        z-index: 2;
    }

    .quantity-input {
        width: 60px !important;
        text-align: center;
    }

    .quantity-input::-webkit-inner-spin-button,
    .quantity-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .btn-quantity {
        padding: 0.375rem 0.75rem;
    }

    .cart-item {
        transition: all 0.3s ease;
    }

    .cart-item:hover {
        background-color: #f8f9fa;
    }

    .remove-item {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .remove-item:hover {
        opacity: 1;
    }

    .btn-outline-secondary {
        padding: 0.375rem 0.75rem;
    }

    .btn-outline-secondary i {
        font-size: 0.875rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ROOT_URL = '<?= ROOT_URL ?>';

        // Xử lý tăng số lượng
        document.querySelectorAll('.increase-quantity').forEach(button => {
            button.addEventListener('click', function() {
                const container = this.closest('.cart-item');
                const input = container.querySelector('.quantity-input');
                const productId = container.dataset.productId;
                input.value = parseInt(input.value) + 1;
                updateCartItem(productId, input.value);
            });
        });

        // Xử lý giảm số lượng
        document.querySelectorAll('.decrease-quantity').forEach(button => {
            button.addEventListener('click', function() {
                const container = this.closest('.cart-item');
                const input = container.querySelector('.quantity-input');
                const productId = container.dataset.productId;
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    updateCartItem(productId, input.value);
                }
            });
        });

        // Xử lý nhập số lượng trực tiếp
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const container = this.closest('.cart-item');
                const productId = container.dataset.productId;
                let value = parseInt(this.value);
                if (value < 1) value = 1;
                this.value = value;
                updateCartItem(productId, value);
            });
        });

        // Xử lý xóa sản phẩm
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const container = this.closest('.cart-item');
                const productId = container.dataset.productId;

                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: 'Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateCartItem(productId, 0);
                    }
                });
            });
        });

        function updateCartItem(productId, quantity) {
            fetch(`${ROOT_URL}/Product/updateCart`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật số lượng trong header
                        const cartCountElement = document.querySelector('.cart-count');
                        if (cartCountElement) {
                            cartCountElement.textContent = data.cartCount;

                            // Hiển thị hoặc ẩn badge dựa vào số lượng
                            if (data.cartCount > 0) {
                                cartCountElement.style.display = 'inline-block';
                            } else {
                                cartCountElement.style.display = 'none';
                            }

                            // Thêm hiệu ứng highlight
                            cartCountElement.classList.add('highlight');
                            setTimeout(() => {
                                cartCountElement.classList.remove('highlight');
                            }, 1000);
                        }

                        // Tải lại trang để cập nhật thông tin giỏ hàng
                        location.reload();
                    } else {
                        Swal.fire('Lỗi', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Lỗi', 'Không thể cập nhật giỏ hàng', 'error');
                });
        }
    });
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer.php'; ?>