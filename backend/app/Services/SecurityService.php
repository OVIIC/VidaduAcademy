<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use HTMLPurifier;
use HTMLPurifier_Config;

class SecurityService
{
    protected HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', storage_path('app/purifier'));
        $config->set('HTML.Allowed', 'p,b,strong,i,em,u,a[href],ul,ol,li,br,span');
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube\.com/embed/|player\.vimeo\.com/video/)%');
        
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Sanitize HTML input to prevent XSS
     */
    public function sanitizeHtml(string $input): string
    {
        return $this->purifier->purify($input);
    }

    /**
     * Sanitize file name to prevent directory traversal
     */
    public function sanitizeFileName(string $fileName): string
    {
        // Remove directory separators and null bytes
        $fileName = str_replace(['/', '\\', "\0"], '', $fileName);
        
        // Remove dangerous extensions
        $dangerousExtensions = ['php', 'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($extension, $dangerousExtensions)) {
            $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.txt';
        }
        
        // Limit length
        return substr($fileName, 0, 255);
    }

    /**
     * Validate course data with security rules
     */
    public function validateCourseData(array $data): array
    {
        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-_.,!?]+$/'],
            'description' => ['required', 'string', 'max:5000'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'difficulty_level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'what_you_will_learn' => ['nullable', 'array', 'max:20'],
            'what_you_will_learn.*' => ['string', 'max:255'],
            'requirements' => ['nullable', 'array', 'max:20'],
            'requirements.*' => ['string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048']
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validated = $validator->validated();

        // Sanitize HTML content
        if (isset($validated['description'])) {
            $validated['description'] = $this->sanitizeHtml($validated['description']);
        }
        
        if (isset($validated['short_description'])) {
            $validated['short_description'] = $this->sanitizeHtml($validated['short_description']);
        }

        return $validated;
    }

    /**
     * Validate user profile data
     */
    public function validateUserProfileData(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^[\+]?[0-9\s\-\(\)]+$/', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'youtube_channel' => ['nullable', 'url', 'max:255', 'regex:/^https:\/\/(www\.)?youtube\.com\/.*$/'],
            'content_niche' => ['nullable', 'string', 'max:100'],
            'goals' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024']
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validated = $validator->validated();

        // Sanitize text fields
        foreach (['bio', 'content_niche', 'goals'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = $this->sanitizeHtml($validated[$field]);
            }
        }

        return $validated;
    }

    /**
     * Check for suspicious patterns in user input
     */
    public function detectSuspiciousActivity(string $input, string $userAgent = '', string $ip = ''): bool
    {
        // Input patterns
        $suspiciousPatterns = [
            // SQL injection patterns
            '/\b(union|select|insert|update|delete|drop|create|alter)\b/i',
            '/\'.*\s+(or|and)\s+/i',
            '/--/i',
            '/#/i',
            '/\|\|/i',
            '/1\s*=\s*1/i',
            
            // XSS patterns
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript\s*:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>/i',
            '/<img[^>]*onerror/i',
            '/<svg[^>]*onload/i',
            
            // Path traversal patterns
            '/\.\.\//i',
            '/\.\.\\\/i',
            '/\.\.%2f/i',
            '/\.\.%5c/i',
            '/%2e%2e%2f/i',
            '/%2e%2e%5c/i',
            
            // Code execution patterns
            '/eval\s*\(/i',
            '/base64_decode/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
        ];

        // User agent patterns
        $suspiciousUserAgents = [
            '/sqlmap/i',
            '/nikto/i',
            '/nmap/i',
            '/masscan/i',
            '/zmeu/i',
            '/w3af/i',
            '/acunetix/i',
            '/nessus/i',
            '/openvas/i',
            '/burp/i',
        ];

        // Check input patterns
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                \Log::warning('Suspicious activity detected in input', [
                    'pattern' => $pattern,
                    'input' => substr($input, 0, 200),
                    'user_agent' => $userAgent,
                    'ip' => $ip,
                    'timestamp' => now()
                ]);
                return true;
            }
        }

        // Check user agent patterns
        foreach ($suspiciousUserAgents as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                \Log::warning('Suspicious user agent detected', [
                    'pattern' => $pattern,
                    'user_agent' => $userAgent,
                    'ip' => $ip,
                    'timestamp' => now()
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Generate secure random token
     */
    public function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate and sanitize search query
     */
    public function sanitizeSearchQuery(string $query): string
    {
        // Remove special characters that could be used for injection
        $query = preg_replace('/[^\w\s\-\.\,\!\?]/', '', $query);
        
        // Limit length
        $query = substr(trim($query), 0, 100);
        
        // Remove multiple spaces
        $query = preg_replace('/\s+/', ' ', $query);
        
        return $query;
    }

    /**
     * Check if file upload is safe
     */
    public function validateFileUpload($file): bool
    {
        if (!$file || !$file->isValid()) {
            return false;
        }

        // Check file size (2MB max)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return false;
        }

        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }

        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        // Check if file is actually an image
        try {
            $imageInfo = getimagesize($file->getPathname());
            if (!$imageInfo) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Check if file type is allowed
     */
    public function isAllowedFileType(string $filename): bool
    {
        $allowedExtensions = [
            'pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 
            'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'webp'
        ];
        
        $dangerousExtensions = [
            'exe', 'bat', 'com', 'cmd', 'scr', 'pif', 'php', 
            'asp', 'jsp', 'js', 'vbs', 'sh', 'pl', 'py'
        ];
        
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($extension, $allowedExtensions) && !in_array($extension, $dangerousExtensions);
    }

    /**
     * Check if file size is valid
     */
    public function isValidFileSize(int $sizeInBytes): bool
    {
        $maxSize = 5 * 1024 * 1024; // 5MB
        return $sizeInBytes <= $maxSize;
    }

    /**
     * Validate input using Laravel validator
     */
    public function validateInput(array $data, array $rules): bool
    {
        $validator = Validator::make($data, $rules);
        return !$validator->fails();
    }
}
