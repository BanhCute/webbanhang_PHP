<?php
$pageTitle = 'Danh sách sản phẩm';
$currentPage = 'product';
require_once ROOT_PATH . '/app/views/shares/header.php';
?>

<div class="container">
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-box text-primary me-2"></i>Danh sách sản phẩm</h5>
            <a href="/T6-Sang/webbanhang/product/add" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm sản phẩm
            </a>
        </div>

        <div class="card-body border-bottom">
            <div class="row g-3">
                <!-- Tìm kiếm theo tên -->
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text"
                            id="searchInput"
                            class="form-control"
                            placeholder="Nhập tên sản phẩm để tìm...">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>

                <!-- Lọc theo danh mục -->
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Tất cả danh mục</option>
                        <?php
                        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
                        $categories = $stmt->fetchAll();
                        foreach ($categories as $category):
                        ?>
                            <option value="<?= $category['id'] ?>"
                                <?= (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Lọc theo giá -->
                <div class="col-md-3">
                    <select class="form-select" id="priceFilter">
                        <option value="">Sắp xếp theo giá</option>
                        <option value="asc" <?= (isset($_GET['price']) && $_GET['price'] == 'asc') ? 'selected' : '' ?>>
                            Giá thấp đến cao
                        </option>
                        <option value="desc" <?= (isset($_GET['price']) && $_GET['price'] == 'desc') ? 'selected' : '' ?>>
                            Giá cao đến thấp
                        </option>
                    </select>
                </div>

                <!-- Nút lọc -->
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100" id="filterButton">
                        <i class="fas fa-filter me-2"></i>Lọc
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card h-100 product-card">
                                <!-- Ảnh sản phẩm có link -->
                                <a href="/T6-Sang/webbanhang/product/detail/<?= $product['id'] ?>" class="position-relative">
                                    <img src="<?= ROOT_URL ?>/public/uploads/products/<?= htmlspecialchars($product['image']) ?>"
                                        class="card-img-top"
                                        style="height: 200px; object-fit: cover;"
                                        alt="<?= htmlspecialchars($product['name']) ?>">

                                    <!-- Badge trả góp -->


                                    <?php if (isset($product['discount']) && $product['discount'] > 0): ?>
                                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                                            <span class="badge bg-danger">-<?= $product['discount'] ?>%</span>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <div class="card-body">
                                    <!-- Tên sản phẩm có link -->
                                    <h6 class="card-title mb-2">
                                        <a href="/T6-Sang/webbanhang/product/detail/<?= $product['id'] ?>"

                                            class="text-decoration-none text-dark">
                                            <i class="fas fa-box"></i>
                                            <?= htmlspecialchars($product['name']) ?>
                                        </a>
                                    </h6>

                                    <!-- Giá -->
                                    <div class="mb-2">
                                        <div class="text-danger fw-bold fs-5">
                                            <i class="fas fa-dollar-sign"></i>
                                            <?= number_format($product['price']) ?> đ
                                        </div>
                                    </div>

                                    <!-- Danh mục -->
                                    <div class="mb-2">
                                        <span class=" bg-info">
                                            <i class="fas fa-tag"></i>

                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </span>
                                    </div>

                                    <!-- Các nút thao tác -->
                                    <div class="btn-group w-100" role="group">
                                        <a href="/T6-Sang/webbanhang/product/detail/<?= $product['id'] ?>"
                                            class="btn btn-info btn-sm text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/T6-Sang/webbanhang/product/edit/<?= $product['id'] ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="xoaSanPham(<?= $product['id'] ?>)"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="mb-0">Chưa có sản phẩm nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .card-img-top {
        transition: transform 0.2s;
    }

    .product-card:hover .card-img-top {
        transform: scale(1.05);
    }
</style>

<script>
    function xoaSanPham(id) {
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            window.location.href = '/T6-Sang/webbanhang/product/delete/' + id;
        }
    }

    $(document).ready(function() {
        // Xử lý tìm kiếm theo tên
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".product-card").filter(function() {
                $(this).closest('.col').toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Xử lý lọc theo danh mục và giá
        $("#filterButton").click(function() {
            var category = $("#categoryFilter").val();
            var price = $("#priceFilter").val();
            var url = '/T6-Sang/webbanhang/product';
            var params = [];

            if (category) {
                params.push('category=' + category);
            }
            if (price) {
                params.push('price=' + price);
            }

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            window.location.href = url;
        });

        // Tự động submit khi thay đổi select
        $("#categoryFilter, #priceFilter").change(function() {
            $("#filterButton").click();
        });
    });
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer.php'; ?>