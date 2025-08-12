/**
 * Format currency with Naira symbol
 */
export function formatCurrency(amount) {
  return `₦${amount.toLocaleString('en-NG')}`
}

/**
 * Format date to readable string
 */
export function formatDate(date, format = 'short') {
  const options =
    format === 'short'
      ? { year: 'numeric', month: 'short', day: 'numeric' }
      : { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }

  return new Date(date).toLocaleDateString('en-NG', options)
}

/**
 * Truncate text to specified length
 */
export function truncateText(text, length = 100) {
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}

/**
 * Generate order number
 */
export function generateOrderNumber() {
  const prefix = 'ORD'
  const timestamp = Date.now().toString(36).toUpperCase()
  const random = Math.random().toString(36).substring(2, 5).toUpperCase()
  return `${prefix}-${timestamp}-${random}`
}

/**
 * Calculate discount percentage
 */
export function calculateDiscount(originalPrice, salePrice) {
  const discount = ((originalPrice - salePrice) / originalPrice) * 100
  return Math.round(discount)
}

/**
 * Validate email format
 */
export function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Validate Nigerian phone number
 */
export function isValidPhoneNumber(phone) {
  const phoneRegex = /^(\+234|0)[789][01]\d{8}$/
  return phoneRegex.test(phone.replace(/\s/g, ''))
}

/**
 * Format phone number
 */
export function formatPhoneNumber(phone) {
  const cleaned = phone.replace(/\D/g, '')
  if (cleaned.startsWith('234')) {
    return `+234 ${cleaned.slice(3, 6)} ${cleaned.slice(6, 10)} ${cleaned.slice(10)}`
  }
  if (cleaned.startsWith('0')) {
    return `${cleaned.slice(0, 4)} ${cleaned.slice(4, 7)} ${cleaned.slice(7)}`
  }
  return phone
}

/**
 * Get status color class
 */
export function getStatusColor(status) {
  const colors = {
    pending: 'yellow',
    processing: 'blue',
    shipped: 'indigo',
    delivered: 'purple',
    completed: 'green',
    cancelled: 'red',
    refunded: 'gray',
  }
  return colors[status.toLowerCase()] || 'gray'
}

/**
 * Debounce function
 */
export function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

/**
 * Deep clone object
 */
export function deepClone(obj) {
  return JSON.parse(JSON.stringify(obj))
}

/**
 * Check if object is empty
 */
export function isEmpty(obj) {
  return Object.keys(obj).length === 0
}

/**
 * Generate unique ID
 */
export function generateId() {
  return Date.now().toString(36) + Math.random().toString(36).substr(2)
}
