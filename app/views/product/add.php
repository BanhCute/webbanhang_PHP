<?php
$pageTitle = 'Thêm sản phẩm mới';
$currentPage = 'product';
require_once ROOT_PATH . '/app/views/shares/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>Thêm sản phẩm mới</h5>
            <a href="/T6-Sang/webbanhang/product" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= ROOT_URL ?>/Product/save" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá</label>
                                    <input type="number" class="form-control" name="price" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>">
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
                                    <img id="preview" src="<?= ROOT_URL ?>/public/images/no-image.png"
                                        class="img-fluid mb-2"
                                        style="max-height: 200px; object-fit: contain;">
                                    <input type="file" class="form-control" name="image"
                                        accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Chọn ảnh để xem trước</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm sản phẩm
                    </button>
                    <a href="/T6-Sang/webbanhang/product" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
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
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '<?= ROOT_URL ?>/public/images/no-image.png';
        }
    }

    // Thêm hiệu ứng khi hover vào ảnh
    document.getElementById('preview').addEventListener('mouseover', function() {
        this.style.transform = 'scale(1.05)';
        this.style.transition = 'transform 0.3s ease';
    });

    document.getElementById('preview').addEventListener('mouseout', function() {
        this.style.transform = 'scale(1)';
    });
</script>

<style>
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    #preview {
        transition: transform 0.3s ease;
        border-radius: 8px;
        background-color: #f8f9fa;
        padding: 10px;
    }
</style>

<?php require_once ROOT_PATH . '/app/views/shares/footer.php'; ?>