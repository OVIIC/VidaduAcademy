<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('courses:debug', function () {
    $this->info('Debugging Courses...');
    
    try {
        $course = \App\Models\Course::latest()->first();
        
        if (!$course) {
            $this->error('No courses found.');
            return;
        }

        $this->info('Latest Course ID: ' . $course->id);
        $this->info('Title: ' . $course->title);
        $this->info('Raw Attributes:');
        dump($course->getAttributes());

        $this->info('Testing Accessors...');
        
        try {
            $this->info('Thumbnail: ' . $course->thumbnail);
        } catch (\Throwable $e) {
            $this->error('Thumbnail Accessor Failed: ' . $e->getMessage());
        }

        try {
            $this->info('What You Will Learn (Raw): ' . $course->getAttributes()['what_you_will_learn']);
            $this->info('What You Will Learn (Processed): ' . json_encode($course->what_you_will_learn));
        } catch (\Throwable $e) {
            $this->error('WhatYouWillLearn Accessor Failed: ' . $e->getMessage());
        }

        try {
            $this->info('Requirements (Processed): ' . json_encode($course->requirements));
        } catch (\Throwable $e) {
            $this->error('Requirements Accessor Failed: ' . $e->getMessage());
        }

    } catch (\Throwable $e) {
        $this->error('General Failure: ' . $e->getMessage());
        $this->error($e->getTraceAsString());
    }
});
