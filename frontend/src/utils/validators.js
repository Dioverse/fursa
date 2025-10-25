import * as yup from 'yup'

// Login validation schema
export const loginSchema = yup.object({
  email: yup.string().email('Invalid email format').required('Email is required'),
  password: yup
    .string()
    .min(6, 'Password must be at least 6 characters')
    .required('Password is required'),
})

// Registration validation schema
export const registerSchema = yup.object({
  firstName: yup
    .string()
    .min(2, 'First name must be at least 2 characters')
    .required('First name is required'),
  lastName: yup
    .string()
    .min(2, 'Last name must be at least 2 characters')
    .required('Last name is required'),
  email: yup.string().email('Invalid email format').required('Email is required'),
  password: yup
    .string()
    .min(8, 'Password must be at least 8 characters')
    .matches(/[a-z]/, 'Password must contain at least one lowercase letter')
    .matches(/[A-Z]/, 'Password must contain at least one uppercase letter')
    .matches(/[0-9]/, 'Password must contain at least one number')
    .required('Password is required'),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref('password'), null], 'Passwords must match')
    .required('Please confirm your password'),
  terms: yup.boolean().oneOf([true], 'You must accept the terms and conditions'),
})

// Address validation schema
export const addressSchema = yup.object({
  fullName: yup.string().required('Full name is required'),
  phone: yup
    .string()
    .matches(/^(\+234|0)[789][01]\d{8}$/, 'Invalid Nigerian phone number')
    .required('Phone number is required'),
  address: yup.string().required('Address is required'),
  city: yup.string().required('City is required'),
  state: yup.string().required('State is required'),
  postalCode: yup.string(),
})

// Distributor registration schemas
export const businessInfoSchema = yup.object({
  companyName: yup.string().required('Company name is required'),
  rcNumber: yup.string().required('RC number is required'),
  email: yup.string().email('Invalid email').required('Email is required'),
  phone: yup.string().required('Phone number is required'),
  companyType: yup.string().required('Company type is required'),
  address: yup.string().required('Business address is required'),
})

export const contactPersonSchema = yup.object({
  fullName: yup.string().required('Full name is required'),
  position: yup.string().required('Position is required'),
  mobile: yup.string().required('Mobile number is required'),
  idType: yup.string().required('ID type is required'),
  idNumber: yup.string().required('ID number is required'),
})

// Custom validators
export const validators = {
  email(value) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(value)
  },

  phone(value) {
    const phoneRegex = /^(\+234|0)[789][01]\d{8}$/
    return phoneRegex.test(value.replace(/\s/g, ''))
  },

  rcNumber(value) {
    // RC number format: RC-123456
    const rcRegex = /^RC-?\d{6,}$/
    return rcRegex.test(value)
  },

  nin(value) {
    // Nigerian NIN is 11 digits
    return /^\d{11}$/.test(value)
  },

  url(value) {
    try {
      new URL(value)
      return true
    } catch {
      return false
    }
  },

  password(value) {
    // At least 8 characters, one uppercase, one lowercase, one number
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/
    return passwordRegex.test(value)
  },
}
