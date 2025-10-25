

export function handleImageError(e) {
  e.target.src = '/images/oil-droplet.jpg';
};

const storageUrl = import.meta.env.VITE_FILE_BASE_PATH;
export function getImageUrl(path) {
  if (!path) return '/images/oil-droplet.jpg';
  return `${storageUrl}${path.startsWith('/') ? path.slice(1) : path}`
}

export const toUcwords = (text) => {
  if (!text) return ''
  return text
    .toLowerCase()
    .replace(/\b\w/g, char => char.toUpperCase())
}