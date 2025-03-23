<?php
$pageTitle = 'Thanh toán';
?>
<!DOCTYPE html>
<html>

<head>
    <script>
        const ROOT_URL = '<?= ROOT_URL ?>';
    </script>
</head>

<body>
    <div class="container py-5">
        <h2 class="mb-4">Thông tin đặt hàng</h2>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="checkoutForm" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ tên *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>


                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ giao hàng *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>



                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Đặt hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Đơn hàng của bạn</h5>
                        <?php if (!empty($cartItems)): ?>
                            <?php
                            $totalItems = count($cartItems);
                            $counter = 0;
                            foreach ($cartItems as $item):
                                $counter++;
                            ?>
                                <div class="d-flex mb-3 align-items-center">
                                    <div class="flex-shrink-0" style="width: 80px;">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="<?= ROOT_URL ?>/public/uploads/products/<?= htmlspecialchars($item['image']) ?>"
                                                class="img-fluid rounded"
                                                alt="<?= htmlspecialchars($item['name']) ?>"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                SL: <?= $item['quantity'] ?> x <?= number_format($item['price']) ?>đ
                                            </small>
                                            <strong><?= number_format($item['price'] * $item['quantity']) ?>đ</strong>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($counter < $totalItems): ?>
                                    <hr class="my-2">
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between mb-2">
                                <div>Tạm tính:</div>
                                <strong><?= number_format($totalAmount) ?>đ</strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <div>Phí vận chuyển:</div>
                                <strong>0đ</strong>
                            </div>

                            <hr class="my-2">

                            <div class="d-flex justify-content-between">
                                <h5 class="mb-0">Tổng cộng:</h5>
                                <h5 class="text-primary mb-0"><?= number_format($totalAmount) ?>đ</h5>
                            </div>
                        <?php else: ?>
                            <p class="text-center mb-0">Giỏ hàng trống</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Thêm style cho phần hiển thị sản phẩm */
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .img-fluid {
            transition: transform 0.2s;
            border: 1px solid #eee;
        }

        .img-fluid:hover {
            transform: scale(1.05);
        }

        hr {
            opacity: 0.15;
        }

        .text-primary {
            color: #007bff !important;
        }

        .product-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }

        /* Animation cho giá tiền */
        @keyframes highlight {
            from {
                color: #28a745;
            }

            to {
                color: #007bff;
            }
        }

        .price-highlight {
            animation: highlight 0.5s ease-in-out;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkoutForm = document.getElementById('checkoutForm');
            if (!checkoutForm) {
                console.error('Không tìm thấy form checkout');
                return;
            }

            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const nameInput = document.getElementById('name');
                const phoneInput = document.getElementById('phone');
                const addressInput = document.getElementById('address');

                if (!nameInput || !phoneInput || !addressInput) {
                    console.error('Không tìm thấy các trường input cần thiết');
                    return;
                }

                const name = nameInput.value.trim();
                const phone = phoneInput.value.trim();
                const address = addressInput.value.trim();

                if (!name || !phone || !address) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng điền đầy đủ thông tin giao hàng!'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận đặt hàng',
                    text: 'Bạn có chắc chắn muốn đặt hàng?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Đặt hàng',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(checkoutForm);

                        fetch(`${ROOT_URL}/Product/placeOrder`, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    window.location.href = `${ROOT_URL}/Product/orderConfirmation/${data.order.id}`;
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: data.message || 'Có lỗi xảy ra khi đặt hàng'
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
                });
            });
        });
    </script>
</body>

</html>