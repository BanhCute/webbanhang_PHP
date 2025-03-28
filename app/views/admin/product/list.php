<?php
$pageTitle = 'Quản lý sản phẩm';
$currentPage = 'product';
require_once ROOT_PATH . '/app/views/shares/header_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">Quản lý sản phẩm</h5>
            <a href="<?= ROOT_URL ?>/admin/product/add" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm
            </a>
        </div>
        <div class="card-body">
            <!-- Thêm phần hiển thị thông báo -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Form lọc -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="GET" action="<?= ROOT_URL ?>/admin/product" class="d-flex gap-2">
                        <select name="category_id" class="form-select w-auto">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"
                                    <?= isset($_GET['category_id']) && $_GET['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                        <?php if (isset($_GET['category_id']) && $_GET['category_id']): ?>
                            <a href="<?= ROOT_URL ?>/admin/product" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Xóa lọc
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Bảng sản phẩm -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Danh mục</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td>
                                    <img src="<?= ROOT_URL ?>/public/uploads/products/<?= $product['image'] ?: 'no-image.png' ?>"
                                        class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div>
                                    <small class="text-muted"><?= substr($product['description'], 0, 50) ?>...</small>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?= number_format($product['price'], 0, ',', '.') ?> đ
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($product['category_name']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= ROOT_URL ?>/admin/product/edit/<?= $product['id'] ?>"
                                        class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteProduct(<?= $product['id'] ?>)"
                                        class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $queryString = http_build_query($queryParams);
                        $baseUrl = ROOT_URL . '/admin/product' . ($queryString ? '?' . $queryString . '&' : '?');
                        ?>

                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>page=<?= $page - 1 ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>page=<?= $page + 1 ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function deleteProduct(id) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            window.location.href = '<?= ROOT_URL ?>/admin/product/delete/' + id;
        }
    }
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>