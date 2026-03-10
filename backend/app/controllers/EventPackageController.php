<?php

namespace App\Controllers;

use App\Models\EventPackage;

class EventPackageController {
    private $model;

    public function __construct() {
        $this->model = new EventPackage();
    }

    private function handleImageUpload($fileKey = 'image') {
        if (empty($_FILES[$fileKey]['tmp_name'])) return null;
        $file = $_FILES[$fileKey];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) return null;
        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('pkg_', true) . '.' . strtolower($ext);
        $dir  = rtrim(UPLOADS_BASE_PATH, '/') . '/event-packages/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        move_uploaded_file($file['tmp_name'], $dir . $name);
        return 'uploads/event-packages/' . $name;
    }

    public function getAll() {
        \App\Middleware\AuthMiddleware::handle();
        $packages = $this->model->getAll();
        return response($packages, 200);
    }

    public function getActive() {
        \App\Middleware\AuthMiddleware::handle();
        $packages = $this->model->getActive();
        return response($packages, 200);
    }

    public function getPublic() {
        $packages = $this->model->getActive();
        return response($packages, 200);
    }

    public function create() {
        \App\Middleware\AuthMiddleware::handle();

        $isMultipart = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart') !== false;
        if ($isMultipart) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }
        if (!$data) return error('Invalid data', 400);

        if (empty($data['package_name'])) {
            return error('Package name is required', 400);
        }
        if (!isset($data['price']) || $data['price'] === '') {
            return error('Price is required', 400);
        }
        if (empty($data['max_guests'])) {
            return error('Max guests is required', 400);
        }

        $imageUrl = $this->handleImageUpload('image');

        $insertData = [
            'package_name' => trim($data['package_name']),
            'price'        => (float)($data['price']),
            'max_guests'   => (int)($data['max_guests']),
            'max_per_dish' => (int)($data['max_per_dish'] ?? 0),
            'description'  => trim($data['description'] ?? ''),
            'image_url'    => $imageUrl,
            'is_active'    => 1,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $id = $this->model->create($insertData);
        $package = $this->model->findById($id);
        return response($package, 201);
    }

    public function update($id) {
        \App\Middleware\AuthMiddleware::handle();

        $package = $this->model->findById($id);
        if (!$package) return error('Package not found', 404);

        $isMultipart = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart') !== false;
        if ($isMultipart) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }
        if (!$data) return error('Invalid data', 400);

        $imageUrl = $this->handleImageUpload('image');
        if (!$imageUrl && !empty($data['remove_image'])) {
            $imageUrl = null;
        } elseif (!$imageUrl) {
            $imageUrl = $package['image_url']; // keep existing
        }

        $updateData = [
            'package_name' => trim($data['package_name'] ?? $package['package_name']),
            'price'        => (float)($data['price'] ?? $package['price']),
            'max_guests'   => (int)($data['max_guests'] ?? $package['max_guests']),
            'max_per_dish' => (int)($data['max_per_dish'] ?? $package['max_per_dish']),
            'description'  => trim($data['description'] ?? $package['description']),
            'image_url'    => $imageUrl,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->model->update($id, $updateData);
        $updated = $this->model->findById($id);
        return response($updated, 200);
    }

    public function delete($id) {
        \App\Middleware\AuthMiddleware::handle();

        $package = $this->model->findById($id);
        if (!$package) return error('Package not found', 404);

        $this->model->delete($id);
        return response(['message' => 'Package deleted'], 200);
    }
}

