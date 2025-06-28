<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SecurityService $securityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->securityService = new SecurityService();
    }

    /** @test */
    public function it_detects_xss_patterns()
    {
        $maliciousInputs = [
            '<script>alert("XSS")</script>',
            'javascript:alert(1)',
            '<img src="x" onerror="alert(1)">',
            '<iframe src="javascript:alert(1)"></iframe>',
            'onload="alert(1)"',
            '<svg onload=alert(1)>',
        ];

        foreach ($maliciousInputs as $input) {
            $this->assertTrue(
                $this->securityService->detectSuspiciousActivity($input, 'test-agent', '127.0.0.1'),
                "Failed to detect XSS pattern: {$input}"
            );
        }
    }

    /** @test */
    public function it_detects_sql_injection_patterns()
    {
        $maliciousInputs = [
            "' OR '1'='1",
            "'; DROP TABLE users; --",
            "' UNION SELECT * FROM users --",
            "admin'--",
            "' OR 1=1 #",
            "1; DELETE FROM users WHERE 1=1",
        ];

        foreach ($maliciousInputs as $input) {
            $this->assertTrue(
                $this->securityService->detectSuspiciousActivity($input, 'test-agent', '127.0.0.1'),
                "Failed to detect SQL injection pattern: {$input}"
            );
        }
    }

    /** @test */
    public function it_detects_path_traversal_patterns()
    {
        $maliciousInputs = [
            '../../../etc/passwd',
            '..\\windows\\system32\\drivers\\etc\\hosts',
            '....//....//....//etc/passwd',
            '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd',
        ];

        foreach ($maliciousInputs as $input) {
            $this->assertTrue(
                $this->securityService->detectSuspiciousActivity($input, 'test-agent', '127.0.0.1'),
                "Failed to detect path traversal pattern: {$input}"
            );
        }
    }

    /** @test */
    public function it_allows_safe_input()
    {
        $safeInputs = [
            'Hello World',
            'user@example.com',
            'Valid password123!',
            'Normal text content',
            'https://example.com',
        ];

        foreach ($safeInputs as $input) {
            $this->assertFalse(
                $this->securityService->detectSuspiciousActivity($input, 'test-agent', '127.0.0.1'),
                "Incorrectly flagged safe input as suspicious: {$input}"
            );
        }
    }

    /** @test */
    public function it_sanitizes_html_content()
    {
        $dirtyHtml = '<script>alert("XSS")</script><p>Safe content</p><img src="x" onerror="alert(1)">';
        $cleanHtml = $this->securityService->sanitizeHtml($dirtyHtml);

        $this->assertStringNotContainsString('<script>', $cleanHtml);
        $this->assertStringNotContainsString('onerror', $cleanHtml);
        $this->assertStringContainsString('<p>Safe content</p>', $cleanHtml);
    }

    /** @test */
    public function it_validates_file_types()
    {
        // Test allowed file types
        $allowedFiles = [
            'document.pdf',
            'image.jpg',
            'image.jpeg',
            'image.png',
            'image.gif',
            'document.doc',
            'document.docx',
            'spreadsheet.xls',
            'spreadsheet.xlsx',
            'presentation.ppt',
            'presentation.pptx',
            'text.txt',
        ];

        foreach ($allowedFiles as $filename) {
            $this->assertTrue(
                $this->securityService->isAllowedFileType($filename),
                "Incorrectly rejected allowed file: {$filename}"
            );
        }

        // Test disallowed file types
        $disallowedFiles = [
            'script.exe',
            'malware.bat',
            'virus.com',
            'trojan.scr',
            'backdoor.php',
            'shell.jsp',
            'exploit.asp',
        ];

        foreach ($disallowedFiles as $filename) {
            $this->assertFalse(
                $this->securityService->isAllowedFileType($filename),
                "Incorrectly allowed dangerous file: {$filename}"
            );
        }
    }

    /** @test */
    public function it_validates_file_sizes()
    {
        // Test file within size limit (5MB)
        $this->assertTrue($this->securityService->isValidFileSize(1024 * 1024)); // 1MB
        $this->assertTrue($this->securityService->isValidFileSize(5 * 1024 * 1024)); // 5MB

        // Test file exceeding size limit
        $this->assertFalse($this->securityService->isValidFileSize(6 * 1024 * 1024)); // 6MB
        $this->assertFalse($this->securityService->isValidFileSize(10 * 1024 * 1024)); // 10MB
    }

    /** @test */
    public function it_detects_suspicious_user_agents()
    {
        $suspiciousAgents = [
            'sqlmap/1.0',
            'Nikto',
            'Mozilla/5.0 (compatible; Nmap Scripting Engine',
            'masscan',
            'ZmEu',
            'w3af.org',
        ];

        foreach ($suspiciousAgents as $agent) {
            $this->assertTrue(
                $this->securityService->detectSuspiciousActivity('normal input', $agent, '127.0.0.1'),
                "Failed to detect suspicious user agent: {$agent}"
            );
        }
    }

    /** @test */
    public function it_validates_email_format()
    {
        $validEmails = [
            'user@example.com',
            'test.email+tag@domain.co.uk',
            'user123@test-domain.com',
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(
                $this->securityService->validateInput(['email' => $email], ['email' => 'required|email']),
                "Valid email rejected: {$email}"
            );
        }

        $invalidEmails = [
            'invalid-email',
            '@domain.com',
            'user@',
            'user..double.dot@domain.com',
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(
                $this->securityService->validateInput(['email' => $email], ['email' => 'required|email']),
                "Invalid email accepted: {$email}"
            );
        }
    }
}
