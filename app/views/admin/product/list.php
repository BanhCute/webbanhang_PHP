<?php
$pageTitle = 'Quản lý sản phẩm';
$currentPage = 'product';
require_once ROOT_PATH . '/app/views/shares/header_admin.php';
?>

<div class="container mt-4">
    <h2>Quản lý sản phẩm</h2>

    <!-- Form lọc sản phẩm -->
    <div class="row mb-3">
        <div class="col-md-6">
            <form action="" method="GET" class="form-inline">
                <div class="input-group">
                    <select name="category_id" class="form-control">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"
                                <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Lọc</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-right">
            <a href="<?= ROOT_URL ?>/admin/product/add" class="btn btn-success">Thêm sản phẩm mới</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <table class="table">
        <thead>
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
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td>
                            <?php
                            $imagePath = $product['image']
                                ? ROOT_URL . '/public/uploads/products/' . $product['image']
                                : ROOT_URL . '/public/images/no-image.png';
                            ?>
                            <img src="<?= $imagePath ?>"
                                class="rounded"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </td>
                        <td><?= $product['name'] ?></td>
                        <td><?= number_format($product['price']) ?> đ</td>
                        <td><?= $product['category_name'] ?></td>
                        <td>
                            <a href="<?= ROOT_URL ?>/admin/product/edit/<?= $product['id'] ?>"
                                class="btn btn-warning btn-sm">Sửa</a>
                            <a href="<?= ROOT_URL ?>/admin/product/delete/<?= $product['id'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Không có sản phẩm nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function deleteProduct(id) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            window.location.href = '<?= ROOT_URL ?>/admin/product/delete/' + id;
        }
    }
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>