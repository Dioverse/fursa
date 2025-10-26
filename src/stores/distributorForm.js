// stores/distributorForm.js
import { defineStore } from 'pinia'
import { ref, reactive } from 'vue'
import { useAuthStore } from './auth'

export const useDistributorFormStore = defineStore('distributorForm', () => {
  const auth = useAuthStore()
  // All form data persists across steps
  const formData = reactive({
    businessInfo: {
      company_name: '',
      registered_name: '',
      rc_number: '',
      email: '',
      office_phone: '',
      company_type: '',
      website: '',
      business_address: '',
      documents: {} // { utility_bill: File }
    },
    contactPerson: {
      contact_full_name: '',
      contact_position: '',
      contact_mobile: '',
      means_of_id: '',
      id_number: '',
      years_in_business: '',
      id_of_contact: null // File
    },
    distributionCapacity: {
      current_product_lines: '',
      monthly_capacity: '',
      regions_covered: '',
      number_of_sales_staff: '',
      preferred_region: '',
      has_warehouse: null,
      has_vehicles: null,
      vehicle_details: '',
      preferred_states: []
    },
    productFocus: {
      product_categories: [],
      has_technical_knowledge: null,
      willing_to_train: null,
      distribution_start_time: '',
      promo_participation: '',
      documents: {}, // { cac, form_c07, memart, tin, referee, signature }
      declarant_name: '',
      declaration_date: new Date().toISOString().split('T')[0],
      other_specify: '',
      product_categories_display: []
    },
    bankingKYC: {
      bank_name: '',
      account_number: '',
      account_name: '',
      bvn: '',
      partnerships: ''
    }
  })

    if (auth.user) {
        formData.businessInfo.email = auth.user.email || ''
        formData.businessInfo.office_phone = auth.user.phone || ''
    }

  // Update form data
  const updateFormData = (section, data) => {
    Object.assign(formData[section], data)
  }

  // Add file to documents
  const addFile = (section, fileName, file) => {
    if (!formData[section].documents) {
      formData[section].documents = {}
    }
    formData[section].documents[fileName] = file
  }

  // Add file to direct property (like id_of_contact)
  const addDirectFile = (section, propertyName, file) => {
    formData[section][propertyName] = file
  }

  // Get all files across all sections
  const getAllFiles = () => {
    const files = {}
    
    // Business info utility bill
    if (formData.businessInfo.documents?.utility_bill) {
      files.utility_bill = formData.businessInfo.documents.utility_bill
    }
    
    // Contact person ID
    if (formData.contactPerson.id_of_contact) {
      files.id_of_contact = formData.contactPerson.id_of_contact
    }
    
    // Product focus documents
    if (formData.productFocus.documents) {
      Object.assign(files, formData.productFocus.documents)
    }
    
    return files
  }

  // Reset store
  const resetFormData = () => {
    Object.assign(formData, {
      businessInfo: {
        company_name: '',
        registered_name: '',
        rc_number: '',
        email: '',
        office_phone: '',
        company_type: '',
        website: '',
        business_address: '',
        documents: {}
      },
      contactPerson: {
        contact_full_name: '',
        contact_position: '',
        contact_mobile: '',
        means_of_id: '',
        id_number: '',
        years_in_business: '',
        id_of_contact: null
      },
      distributionCapacity: {
        current_product_lines: '',
        monthly_capacity: '',
        regions_covered: '',
        number_of_sales_staff: '',
        preferred_region: '',
        has_warehouse: null,
        has_vehicles: null,
        vehicle_details: '',
        preferred_states: []
      },
      productFocus: {
        product_categories: [],
        has_technical_knowledge: null,
        willing_to_train: null,
        distribution_start_time: '',
        promo_participation: '',
        documents: {},
        declarant_name: '',
        declaration_date: new Date().toISOString().split('T')[0],
        other_specify: '',
        product_categories_display: []
      },
      bankingKYC: {
        bank_name: '',
        account_number: '',
        account_name: '',
        bvn: '',
        partnerships: ''
      }
    })
  }

  return {
    formData,
    updateFormData,
    addFile,
    addDirectFile,
    getAllFiles,
    resetFormData
  }
})