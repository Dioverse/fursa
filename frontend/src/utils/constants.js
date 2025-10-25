export const APP_NAME = 'Fursa Energy'
export const APP_VERSION = '1.0.0'
export const API_VERSION = 'v1'

export const ORDER_STATUS = {
  PENDING: 'pending',
  PROCESSING: 'processing',
  SHIPPED: 'shipped',
  DELIVERED: 'delivered',
  COMPLETED: 'completed',
  CANCELLED: 'cancelled',
  REFUNDED: 'refunded',
}

export const PAYMENT_METHODS = {
  CARD: 'card',
  PAYPAL: 'paypal',
  BANK_TRANSFER: 'bank_transfer',
  CASH_ON_DELIVERY: 'cod',
}

export const PRODUCT_CATEGORIES = [
  { id: 'motor-oil', name: 'Motor Oil', icon: 'car' },
  { id: 'heavy-duty', name: 'Heavy Duty Oil', icon: 'truck' },
  { id: 'industrial', name: 'Industrial & Agricultural', icon: 'industry' },
  { id: 'gear-oil', name: 'Gear Oil', icon: 'cog' },
  { id: 'hydraulic', name: 'Hydraulic & Grease', icon: 'oil-can' },
  { id: 'transmission', name: 'Transmission Fluids', icon: 'exchange-alt' },
  { id: 'marine', name: 'Marine', icon: 'anchor' },
]

export const NIGERIAN_STATES = [
  'Abia',
  'Adamawa',
  'Akwa Ibom',
  'Anambra',
  'Bauchi',
  'Bayelsa',
  'Benue',
  'Borno',
  'Cross River',
  'Delta',
  'Ebonyi',
  'Edo',
  'Ekiti',
  'Enugu',
  'FCT',
  'Gombe',
  'Imo',
  'Jigawa',
  'Kaduna',
  'Kano',
  'Katsina',
  'Kebbi',
  'Kogi',
  'Kwara',
  'Lagos',
  'Nasarawa',
  'Niger',
  'Ogun',
  'Ondo',
  'Osun',
  'Oyo',
  'Plateau',
  'Rivers',
  'Sokoto',
  'Taraba',
  'Yobe',
  'Zamfara',
]

export const ID_TYPES = [
  { value: 'nin', label: 'National ID (NIN)' },
  { value: 'drivers', label: "Driver's License" },
  { value: 'passport', label: 'International Passport' },
  { value: 'voters', label: "Voter's Card" },
]

export const COMPANY_TYPES = [
  { value: 'limited', label: 'Limited Liability Company' },
  { value: 'plc', label: 'Public Limited Company' },
  { value: 'partnership', label: 'Partnership' },
  { value: 'sole', label: 'Sole Proprietorship' },
]

export const CURRENCY = '₦'
export const VAT_RATE = 0.075 // 7.5%
export const FREE_SHIPPING_THRESHOLD = 50000
export const SHIPPING_FEE = 2500
