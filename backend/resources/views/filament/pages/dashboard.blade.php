<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Stats Cards -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900">Courses</h3>
                <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Course::count() }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900">Users</h3>
                <p class="text-3xl font-bold text-green-600">{{ \App\Models\User::count() }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900">Categories</h3>
                <p class="text-3xl font-bold text-purple-600">{{ \App\Models\Category::count() }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900">Lessons</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ \App\Models\Lesson::count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Welcome to VidaduAcademy Admin</h3>
            <p class="text-gray-600">Manage your courses, users, and content from this dashboard.</p>
        </div>
    </div>
</x-filament-panels::page>
