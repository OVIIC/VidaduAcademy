<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VidaduAcademy Backend</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">VidaduAcademy</h1>
                <p class="text-gray-600 mb-6">Backend API is running successfully!</p>
                
                <div class="space-y-4">
                    <a href="/api/health" 
                       class="block w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                        API Health Check
                    </a>
                    
                    <a href="/api/courses" 
                       class="block w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 transition">
                        View Courses API
                    </a>
                    
                    <a href="/test" 
                       class="block w-full bg-purple-600 text-white py-2 px-4 rounded hover:bg-purple-700 transition">
                        Test Endpoint
                    </a>
                    
                    <a href="/telescope" 
                       class="block w-full bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700 transition">
                        Laravel Telescope
                    </a>
                    
                    <a href="/admin" 
                       class="block w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">
                        Admin Panel
                    </a>
                </div>
                
                <div class="mt-6 text-sm text-gray-500">
                    <p>Environment: <?php echo e(app()->environment()); ?></p>
                    <p>Laravel: <?php echo e(app()->version()); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/hermes/Documents/GitHub/VidaduAcademy/backend/resources/views/welcome.blade.php ENDPATH**/ ?>