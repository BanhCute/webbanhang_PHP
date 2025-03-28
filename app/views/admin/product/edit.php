<?php
$pageTitle = 'Sửa sản phẩm';
$currentPage = 'admin-product';
require_once ROOT_PATH . '/app/views/shares/header_admin.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-edit text-primary me-2"></i>Sửa sản phẩm</h5>
            <a href="<?= ROOT_URL ?>/admin/product" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= ROOT_URL ?>/admin/product/update" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" name="name"
                                value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá</label>
                                    <input type="number" class="form-control" name="price"
                                        value="<?= $product['price'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>"
                                                <?= ($category['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Ảnh sản phẩm</label>
                            <div class="card">
                                <div class="card-body text-center">
                                    <img id="preview"
                                        src="<?= !empty($product['image']) ? ROOT_URL . '/public/uploads/products/' . $product['image'] : ROOT_URL . '/public/images/no-image.png' ?>"
                                        class="img-fluid mb-2"
                                        style="max-height: 200px; object-fit: contain;">
                                    <input type="file" class="form-control" name="image"
                                        accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Chọn ảnh mới để thay đổi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    #preview {
        transition: all 0.3s ease;
        border-radius: 8px;
        background-color: #f8f9fa;
        padding: 10px;
        opacity: 1;
    }

    .img-upload-container {
        position: relative;
        overflow: hidden;
    }

    .img-upload-container:hover::after {
        content: 'Click để thay đổi ảnh';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px;
        font-size: 12px;
        text-align: center;
    }
</style>

<?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>