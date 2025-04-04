<?php
$pageTitle = "Thêm sản phẩm mới";
$currentPage = 'product';
require_once ROOT_PATH . '/app/views/shares/header_admin.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Thêm sản phẩm mới</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="<?= ROOT_URL ?>/admin/product/save" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Tên sản phẩm</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Mô tả</label>
                    <div class="col-sm-10">
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Giá</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input type="number" name="price" class="form-control" required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Danh mục</label>
                    <div class="col-sm-10">
                        <select name="category_id" class="form-control" required>
                            <option value="">Chọn danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Hình ảnh</label>
                    <div class="col-sm-10">
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Thêm sản phẩm
                        </button>
                        <a href="<?= ROOT_URL ?>/admin/product" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>