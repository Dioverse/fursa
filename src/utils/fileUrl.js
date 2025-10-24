// Utility functions to build absolute URLs for files/images consistently
// Supports:
// - Absolute URLs (http/https) and data URLs (data:)
// - Relative storage paths (e.g., products/..., public/products/...)
// - Optional base URL via VITE_FILES_BASE_URL or VITE_FILE_BASE_URL

const ABSOLUTE_URL_REGEX = /^(?:https?:)?\/\//i

export const fileBase = () => (
  import.meta.env.VITE_FILES_BASE_URL ||
  import.meta.env.VITE_FILE_BASE_URL ||
  // legacy var (will be undefined unless exposed by Vite config)
  import.meta.env.FILE_BASE_PATH ||
  ''
)

export const isAbsoluteUrl = (u) => ABSOLUTE_URL_REGEX.test(u || '') || (u || '').startsWith('data:')

const ensureLeadingSlash = (p) => (p?.startsWith('/') ? p : `/${p}`)

// Normalize backend storage paths
// - Strip any leading public/
// - Prefix /storage for product files
// - Ensure a single leading slash
export const normalizeStoragePath = (p) => {
  if (!p) return ''
  let path = String(p)
  // Remove leading "public/" if present
  path = path.replace(/^\/?public\//i, '')
  // Add /storage/ prefix for product paths not already under /storage
  if (path.startsWith('products/')) {
    path = `/storage/${path}`
  }
  // If already starts with storage/, add leading slash
  if (path.startsWith('storage/')) {
    path = `/${path}`
  }
  return ensureLeadingSlash(path)
}

// Build a full URL for a given path or URL
export const buildFileUrl = (pathOrUrl) => {
  if (!pathOrUrl) return ''
  const val = String(pathOrUrl)
  if (isAbsoluteUrl(val)) return val

  let base = (fileBase() || '').trim()
  const normalized = normalizeStoragePath(val)

  // If base includes trailing slash, remove it (we always have leading slash on normalized)
  if (base.endsWith('/')) base = base.replace(/\/+$/, '')

  // Avoid double /storage when base already ends with /storage
  if (base.toLowerCase().endsWith('/storage') && normalized.startsWith('/storage/')) {
    return `${base}${normalized.replace(/^\/storage/, '')}`
  }

  // Join
  return base ? `${base}${normalized}` : normalized
}

// Extract a usable path from various image representations
export const extractImagePath = (img) => {
  if (!img) return ''
  if (typeof img === 'string') return img
  if (typeof img === 'object') {
    return img.url || img.path || img.src || img.image || ''
  }
  return ''
}

// Get a product's primary image URL (product.image or first from product.images)
export const productPrimaryImageUrl = (product) => {
  if (!product) return ''
  const direct = extractImagePath(product.image)
  if (direct) return buildFileUrl(direct)
  const first = extractImagePath(product.images?.[0])
  if (first) return buildFileUrl(first)
  return ''
}
