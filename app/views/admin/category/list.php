<?php
$pageTitle = 'Quản lý danh mục';
$currentPage = 'category';
require_once ROOT_PATH . '/app/views/shares/header_admin.php';
?>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Quản lý danh mục</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus-circle me-2"></i>Thêm danh mục
        </button>
    </div>
    <div class="card-body">
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

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Số sản phẩm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category['id'] ?></td>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td>
                                <span class="badge bg-info"><?= $category['product_count'] ?? 0 ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm me-1"
                                    onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteCategory(<?= $category['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Nút Previous -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= ROOT_URL ?>/admin/category?page=<?= max($page - 1, 1) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <!-- Các trang -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= ROOT_URL ?>/admin/category?page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Nút Next -->
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= ROOT_URL ?>/admin/category?page=<?= min($page + 1, $totalPages) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Thêm danh mục -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= ROOT_URL ?>/admin/category/save" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa danh mục -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa danh mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= ROOT_URL ?>/admin/category/update" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_category_id">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" name="name" id="edit_category_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editCategory(id, name) {
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = name;
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    }

    function deleteCategory(id) {
        if (confirm('Bạn có chắc chắn muốn xóa danh mục này?')) {
            window.location.href = '<?= ROOT_URL ?>/admin/category/delete/' + id;
        }
    }
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>