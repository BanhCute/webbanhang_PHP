<?php
$pageTitle = 'Thống kê doanh thu';
$currentPage = 'dashboard';
require_once ROOT_PATH . '/app/views/shares/header_admin.php';
?>

<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-line me-2"></i>Thống kê doanh thu</h2>
        <div class="date-filter">
            <div class="btn-group">
                <button class="btn btn-outline-primary" data-period="day">Hôm nay</button>
                <button class="btn btn-outline-primary active" data-period="month">Tháng này</button>
                <button class="btn btn-outline-primary" data-period="year">Năm nay</button>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng doanh thu</div>
                            <div class="h5 mb-0 font-weight-bold"><?= number_format($overview['total_revenue']) ?> đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold"><?= number_format($overview['total_orders']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Giá trị trung bình</div>
                            <div class="h5 mb-0 font-weight-bold"><?= number_format($overview['avg_order_value']) ?> đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu theo tháng</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bố doanh thu theo danh mục</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biểu đồ doanh thu theo tháng
        const monthlyData = <?= json_encode($monthlyStats) ?>;
        const labels = monthlyData.map(item => 'Tháng ' + item.month);
        const revenues = monthlyData.map(item => item.revenue);

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenues,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Biểu đồ phân bố theo danh mục
        const categoryData = <?= json_encode($categoryStats) ?>;
        const categoryLabels = categoryData.map(item => item.category_name);
        const categoryRevenues = categoryData.map(item => item.revenue);

        new Chart(document.getElementById('categoryPieChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryRevenues,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>