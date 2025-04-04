<?php
class HomeController
{
    public function index()
    {
        // Chuyển hướng đến trang danh sách sản phẩm
        header('Location: ' . ROOT_URL . '/Product/list');
        exit;
    }
}
