// Security utilities for frontend
import DOMPurify from 'dompurify'

class SecurityManager {
  constructor() {
    this.setupDOMPurify()
    this.setupCSPViolationHandler()
  }

  /**
   * Configure DOMPurify for safe HTML sanitization
   */
  setupDOMPurify() {
    // Configure DOMPurify
    DOMPurify.setConfig({
      ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'b', 'i', 'a', 'ul', 'ol', 'li'],
      ALLOWED_ATTR: ['href', 'target', 'rel'],
      ALLOW_DATA_ATTR: false,
      ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i
    })
  }

  /**
   * Sanitize HTML content to prevent XSS
   */
  sanitizeHtml(html) {
    if (!html || typeof html !== 'string') return ''
    return DOMPurify.sanitize(html, { 
      RETURN_DOM_FRAGMENT: false,
      RETURN_DOM: false 
    })
  }

  /**
   * Sanitize text input
   */
  sanitizeText(text) {
    if (!text || typeof text !== 'string') return ''
    
    // Remove control characters and normalize
    return text
      .replace(/[\x00-\x1F\x7F]/g, '') // Remove control characters
      .replace(/\s+/g, ' ') // Normalize whitespace
      .trim()
      .substring(0, 1000) // Limit length
  }

  /**
   * Validate and sanitize URL
   */
  sanitizeUrl(url) {
    if (!url || typeof url !== 'string') return ''
    
    try {
      const urlObj = new URL(url)
      
      // Only allow safe protocols
      const allowedProtocols = ['http:', 'https:', 'mailto:', 'tel:']
      if (!allowedProtocols.includes(urlObj.protocol)) {
        return ''
      }
      
      return urlObj.toString()
    } catch (e) {
      return ''
    }
  }

  /**
   * Validate file before upload
   */
  validateFile(file, options = {}) {
    const defaults = {
      maxSize: 2 * 1024 * 1024, // 2MB
      allowedTypes: ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'],
      allowedExtensions: ['jpg', 'jpeg', 'png', 'webp']
    }
    
    const config = { ...defaults, ...options }
    const errors = []
    
    if (!file) {
      errors.push('No file selected')
      return { valid: false, errors }
    }
    
    // Check file size
    if (file.size > config.maxSize) {
      errors.push(`File size must be less than ${this.formatFileSize(config.maxSize)}`)
    }
    
    // Check MIME type
    if (!config.allowedTypes.includes(file.type)) {
      errors.push(`File type ${file.type} is not allowed`)
    }
    
    // Check file extension
    const extension = file.name.split('.').pop()?.toLowerCase()
    if (!extension || !config.allowedExtensions.includes(extension)) {
      errors.push(`File extension .${extension} is not allowed`)
    }
    
    // Check file name for suspicious patterns
    if (this.hasSuspiciousFileName(file.name)) {
      errors.push('File name contains suspicious characters')
    }
    
    return {
      valid: errors.length === 0,
      errors
    }
  }

  /**
   * Check for suspicious patterns in file names
   */
  hasSuspiciousFileName(fileName) {
    const suspiciousPatterns = [
      /\.\./,           // Directory traversal
      /[<>:"|?*]/,      // Invalid filename characters
      /\.php$/i,        // PHP files
      /\.exe$/i,        // Executable files
      /\.bat$/i,        // Batch files
      /\.cmd$/i,        // Command files
      /\.scr$/i,        // Screen saver files
      /\.vbs$/i,        // VBScript files
      /^\./,            // Hidden files starting with dot
    ]
    
    return suspiciousPatterns.some(pattern => pattern.test(fileName))
  }

  /**
   * Format file size for display
   */
  formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes'
    
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  /**
   * Validate form data before submission
   */
  validateFormData(data, rules = {}) {
    const errors = {}
    
    for (const [field, value] of Object.entries(data)) {
      const fieldRules = rules[field] || {}
      const fieldErrors = []
      
      // Required validation
      if (fieldRules.required && (!value || value.toString().trim() === '')) {
        fieldErrors.push(`${field} is required`)
      }
      
      if (value && value.toString().trim() !== '') {
        // Length validation
        if (fieldRules.minLength && value.length < fieldRules.minLength) {
          fieldErrors.push(`${field} must be at least ${fieldRules.minLength} characters`)
        }
        
        if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
          fieldErrors.push(`${field} must be no more than ${fieldRules.maxLength} characters`)
        }
        
        // Pattern validation
        if (fieldRules.pattern && !fieldRules.pattern.test(value)) {
          fieldErrors.push(`${field} format is invalid`)
        }
        
        // Email validation
        if (fieldRules.email && !this.isValidEmail(value)) {
          fieldErrors.push(`${field} must be a valid email address`)
        }
        
        // URL validation
        if (fieldRules.url && !this.isValidUrl(value)) {
          fieldErrors.push(`${field} must be a valid URL`)
        }
        
        // Suspicious content check
        if (this.hasSuspiciousContent(value.toString())) {
          fieldErrors.push(`${field} contains suspicious content`)
        }
      }
      
      if (fieldErrors.length > 0) {
        errors[field] = fieldErrors
      }
    }
    
    return {
      valid: Object.keys(errors).length === 0,
      errors
    }
  }

  /**
   * Check for suspicious content in user input
   */
  hasSuspiciousContent(content) {
    const suspiciousPatterns = [
      /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, // Script tags
      /javascript:/gi,                                        // JavaScript URLs
      /on\w+\s*=/gi,                                         // Event handlers
      /\beval\s*\(/gi,                                       // eval() calls
      /\bdocument\.(cookie|domain)/gi,                       // Document manipulation
      /\bwindow\.(location|open)/gi,                         // Window manipulation
      /<iframe\b[^>]*>/gi,                                   // Iframe tags
      /<object\b[^>]*>/gi,                                   // Object tags
      /<embed\b[^>]*>/gi,                                    // Embed tags
    ]
    
    return suspiciousPatterns.some(pattern => pattern.test(content))
  }

  /**
   * Validate email format
   */
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email) && email.length <= 254
  }

  /**
   * Validate URL format
   */
  isValidUrl(url) {
    try {
      const urlObj = new URL(url)
      return ['http:', 'https:'].includes(urlObj.protocol)
    } catch (e) {
      return false
    }
  }

  /**
   * Setup CSP violation reporting
   */
  setupCSPViolationHandler() {
    if (typeof window !== 'undefined') {
      document.addEventListener('securitypolicyviolation', (e) => {
        console.warn('CSP Violation:', {
          blockedURI: e.blockedURI,
          violatedDirective: e.violatedDirective,
          originalPolicy: e.originalPolicy,
          documentURI: e.documentURI
        })
        
        // Report to analytics or security service
        this.reportSecurityViolation('csp_violation', {
          blockedURI: e.blockedURI,
          violatedDirective: e.violatedDirective,
          documentURI: e.documentURI
        })
      })
    }
  }

  /**
   * Report security violations to backend
   */
  async reportSecurityViolation(type, details) {
    try {
      // Don't report in development
      if (import.meta.env.DEV) return
      
      await fetch('/api/security/violations', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          type,
          details,
          timestamp: new Date().toISOString(),
          userAgent: navigator.userAgent,
          url: window.location.href
        })
      })
    } catch (error) {
      console.error('Failed to report security violation:', error)
    }
  }

  /**
   * Generate secure random string
   */
  generateSecureToken(length = 32) {
    const array = new Uint8Array(length)
    crypto.getRandomValues(array)
    return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('')
  }

  /**
   * Encrypt sensitive data for local storage
   */
  async encryptForStorage(data, key) {
    try {
      const encoder = new TextEncoder()
      const dataBuffer = encoder.encode(JSON.stringify(data))
      const keyBuffer = encoder.encode(key)
      
      const cryptoKey = await crypto.subtle.importKey(
        'raw',
        keyBuffer,
        { name: 'AES-GCM' },
        false,
        ['encrypt']
      )
      
      const iv = crypto.getRandomValues(new Uint8Array(12))
      const encrypted = await crypto.subtle.encrypt(
        { name: 'AES-GCM', iv },
        cryptoKey,
        dataBuffer
      )
      
      return {
        data: Array.from(new Uint8Array(encrypted)),
        iv: Array.from(iv)
      }
    } catch (error) {
      console.error('Encryption failed:', error)
      return null
    }
  }

  /**
   * Decrypt data from local storage
   */
  async decryptFromStorage(encryptedData, key) {
    try {
      const encoder = new TextEncoder()
      const decoder = new TextDecoder()
      const keyBuffer = encoder.encode(key)
      
      const cryptoKey = await crypto.subtle.importKey(
        'raw',
        keyBuffer,
        { name: 'AES-GCM' },
        false,
        ['decrypt']
      )
      
      const decrypted = await crypto.subtle.decrypt(
        { name: 'AES-GCM', iv: new Uint8Array(encryptedData.iv) },
        cryptoKey,
        new Uint8Array(encryptedData.data)
      )
      
      return JSON.parse(decoder.decode(decrypted))
    } catch (error) {
      console.error('Decryption failed:', error)
      return null
    }
  }
}

// Create singleton instance
const securityManager = new SecurityManager()

// Vue composable for security
export function useSecurity() {
  const sanitizeHtml = (html) => securityManager.sanitizeHtml(html)
  const sanitizeText = (text) => securityManager.sanitizeText(text)
  const sanitizeUrl = (url) => securityManager.sanitizeUrl(url)
  const validateFile = (file, options) => securityManager.validateFile(file, options)
  const validateFormData = (data, rules) => securityManager.validateFormData(data, rules)
  const generateSecureToken = (length) => securityManager.generateSecureToken(length)
  
  return {
    sanitizeHtml,
    sanitizeText,
    sanitizeUrl,
    validateFile,
    validateFormData,
    generateSecureToken
  }
}

export default securityManager
