<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title mb-4">Xác nhận đăng xuất</h3>
                    <p class="mb-4">Bạn có chắc chắn muốn đăng xuất không?</p>
                    <form method="POST">
                        <input type="hidden" name="confirm_logout" value="yes">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                            <a href="/T6-Sang/webbanhang/Product/list" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>