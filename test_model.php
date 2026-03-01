<?php
require 'backend/vendor/autoload.php';
$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$course = new \App\Models\Course();
$course->fill(['title' => 'Test', 'thumbnail' => 'thumbnails/01KJNT2.jpg']);
// Manually set attributes that aren't fillable if needed
$course->id = 1;

echo json_encode($course->toArray(), JSON_UNESCAPED_SLASHES);
