/**
 * Format currency with Naira symbol
 */
export function formatCurrency(amount) {
  return `₦${amount.toLocaleString('en-NG')}`
}

export const formatAmount = (amount, dp=2, locale = 'en-US') => {
  if (isNaN(amount)) { return 0};
  const options = {};

  // Only apply decimal formatting if dp is provided
  if (typeof dp === 'number') {
    options.minimumFractionDigits = dp;
    options.maximumFractionDigits = dp;
  }

  return Number(parseFloat(amount)).toLocaleString(locale, options);
};


const storageUrl = import.meta.env.VITE_STORAGE_URL;
export function getImageUrl(path) {
  if (!path) return '/images/oil-droplet.jpg';
  return `${storageUrl}${path.startsWith('/') ? path.slice(1) : path}`
}

export function getLink (src) {
  if (!src) return "#"
  return `${storageUrl}${src.startsWith('/') ? src.slice(1) : src}`
}

export function handleImageError(e) {
  e.target.src = '/images/oil-droplet.jpg';
};

export const toUcwords = (text) => {
  if (!text) return ''
  return text
    .toLowerCase()
    .replace(/\b\w/g, char => char.toUpperCase())
}

export const toNumber = (v) => (v === null || v === undefined ? 0 : Number(v))

// Utility functions
export const priceToLocale = (val) => {
  if (val == null) return '0';
  return Number(val).toLocaleString('en-NG');
};

export const getBasePrice = (product) => {
  return product.price;
};

export const getDisplayPrice = (product) => {
  const price = getBasePrice(product);
  if (product.discount) {
    return priceToLocale(product.discounted_price);
  }
  return priceToLocale(price);
};

export const discountLabel = (product) => {
  const d = product.discount;
  if (!d) return '';
  if (d.type === 'percentage') return `${d.value}%`;
  return `₦${d.value}`;
};

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

export function formatDeliveryLabel(s) {
  return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

export function splitFullName(name = '') {
  const trimmed = name.trim()

  if (!trimmed) {
    return { first_name: '', last_name: '' }
  }

  const parts = trimmed.split(/\s+/)

  if (parts.length === 1) {
    return {
      first_name: parts[0],
      last_name: parts[0],   // or '' if you want empty
    }
  }

  return {
    first_name: parts[0],
    last_name: parts.slice(1).join(' '),
  }
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
