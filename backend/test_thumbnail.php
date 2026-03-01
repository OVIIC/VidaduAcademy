<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$course = \App\Models\Course::first();
echo "Thumbnail in DB: " . $course->getRawOriginal('thumbnail') . "\n";
echo "Thumbnail accessed: " . $course->thumbnail . "\n";
echo "Thumbnail JSON: " . collect($course->toArray())->get('thumbnail') . "\n";
