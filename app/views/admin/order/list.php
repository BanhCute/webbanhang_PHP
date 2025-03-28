 <?php
    $pageTitle = 'Quản lý đơn hàng';
    $currentPage = 'order';
    require_once ROOT_PATH . '/app/views/shares/header_admin.php';
    ?>

 <div class="container-fluid">
     <div class="d-flex justify-content-between align-items-center mb-4">
         <h2 class="mb-0">
             <i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng
         </h2>
     </div>

     <!-- Bộ lọc đơn hàng -->
     <div class="card mb-4">
         <div class="card-body">
             <form method="GET" action="" class="row g-3">

                 <div class="col-md-3">
                     <label class="form-label">Từ ngày</label>
                     <input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date'] ?? '' ?>">
                 </div>
                 <div class="col-md-3">
                     <label class="form-label">Đến ngày</label>
                     <input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date'] ?? '' ?>">
                 </div>
                 <div class="col-md-3">
                     <label class="form-label">&nbsp;</label>
                     <div class="d-grid">
                         <button type="submit" class="btn btn-primary">
                             <i class="fas fa-search me-2"></i>Lọc
                         </button>
                     </div>
                 </div>
             </form>
         </div>
     </div>

     <!-- Danh sách đơn hàng -->
     <div class="card">
         <div class="card-body">
             <div class="table-responsive">
                 <table class="table table-striped table-hover">
                     <thead>
                         <tr>
                             <th>Mã đơn</th>
                             <th>Khách hàng</th>
                             <th>Số điện thoại</th>
                             <th>Địa chỉ</th>
                             <th>Tổng tiền</th>
                             <th>Ngày đặt</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php foreach ($orders as $order): ?>
                             <tr>
                                 <td>#<?= $order['id'] ?></td>
                                 <td><?= htmlspecialchars($order['name']) ?></td>
                                 <td><?= htmlspecialchars($order['phone']) ?></td>
                                 <td><?= htmlspecialchars($order['address']) ?></td>
                                 <td><?= number_format($order['total_amount']) ?>đ</td>

                                 <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>

                             </tr>
                         <?php endforeach; ?>
                     </tbody>
                 </table>
             </div>

             <!-- Phân trang -->
             <?php if ($totalPages > 1): ?>
                 <nav class="mt-4">
                     <ul class="pagination justify-content-center">
                         <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                             <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                 <a class="page-link" href="?page=<?= $i ?>&status=<?= $_GET['status'] ?? '' ?>&from_date=<?= $_GET['from_date'] ?? '' ?>&to_date=<?= $_GET['to_date'] ?? '' ?>">
                                     <?= $i ?>
                                 </a>
                             </li>
                         <?php endfor; ?>
                     </ul>
                 </nav>
             <?php endif; ?>
         </div>
     </div>
 </div>

 <!-- Modal xem chi tiết đơn hàng -->
 <div class="modal fade" id="orderDetailModal" tabindex="-1">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Chi tiết đơn hàng #<span id="orderIdDetail"></span></h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <div class="modal-body" id="orderDetailContent">
                 <!-- Nội dung chi tiết đơn hàng sẽ được load động -->
             </div>
         </div>
     </div>
 </div>

 <script>
     document.addEventListener('DOMContentLoaded', function() {
         // Xử lý cập nhật trạng thái
         document.querySelectorAll('.status-select').forEach(select => {
             select.addEventListener('change', function() {
                 const orderId = this.dataset.orderId;
                 const status = this.value;

                 fetch(`${ROOT_URL}/admin/updateOrderStatus`, {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/x-www-form-urlencoded',
                         },
                         body: `order_id=${orderId}&status=${status}`
                     })
                     .then(response => response.json())
                     .then(data => {
                         if (data.success) {
                             Swal.fire('Thành công', 'Đã cập nhật trạng thái đơn hàng', 'success');
                         } else {
                             Swal.fire('Lỗi', data.message, 'error');
                         }
                     });
             });
         });

         // Xử lý xem chi tiết đơn hàng
         document.querySelectorAll('.view-details').forEach(button => {
             button.addEventListener('click', function() {
                 const orderId = this.dataset.orderId;
                 document.getElementById('orderIdDetail').textContent = orderId;

                 fetch(`${ROOT_URL}/admin/getOrderDetails/${orderId}`)
                     .then(response => response.json())
                     .then(data => {
                         let html = `
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                         data.items.forEach(item => {
                             html += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${new Intl.NumberFormat('vi-VN').format(item.price)}đ</td>
                            <td>${item.quantity}</td>
                            <td>${new Intl.NumberFormat('vi-VN').format(item.price * item.quantity)}đ</td>
                        </tr>
                    `;
                         });

                         html += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td><strong>${new Intl.NumberFormat('vi-VN').format(data.total)}đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;

                         document.getElementById('orderDetailContent').innerHTML = html;
                         new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
                     });
             });
         });
     });
 </script>

 <?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>