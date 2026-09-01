<?php
$filePath = '/home/ubuntu/projects/localio-live/app/Http/Controllers/Admin/CategoriesController.php';
$lines = file($filePath);
foreach ($lines as $index => $line) {
    if (strpos($line, '$category_data') !== false) {
        echo ($index + 1) . ": " . $line;
    }
}
