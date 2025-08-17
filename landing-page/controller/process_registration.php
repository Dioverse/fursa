<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input helper
    function sanitize($input) {
        return htmlspecialchars(trim($input));
    }

    // Handle productInterest (checkbox array)
    $productInterest = isset($_POST['productInterest']) ? implode(',', $_POST['productInterest']) : '';

    // Handle preferredStates (multiple select)
    $preferredStates = isset($_POST['preferredStates']) ? implode(',', $_POST['preferredStates']) : '';

    // Handle uploaded files (signature + KYC documents)
    $uploadDir = 'uploads/';
    $signaturePath = '';
    $kycDocs = [];

    // Save signature file
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        $signatureName = uniqid('sign_') . '_' . basename($_FILES['signature']['name']);
        $signaturePath = $uploadDir . $signatureName;
        move_uploaded_file($_FILES['signature']['tmp_name'], $signaturePath);
    }

    // Save KYC documents
    if (isset($_FILES['kycDocuments'])) {
        foreach ($_FILES['kycDocuments']['name'] as $index => $name) {
            if ($_FILES['kycDocuments']['error'][$index] == 0) {
                $fileName = uniqid("doc_") . '_' . basename($name);
                $filePath = $uploadDir . $fileName;
                move_uploaded_file($_FILES['kycDocuments']['tmp_name'][$index], $filePath);
                $kycDocs[$name] = $filePath;
            }
        }
    }

    // Prepare data array
    $data = [
        // Section 1
        'company_name' => sanitize($_POST['companyName']),
        'registered_name' => sanitize($_POST['registeredName']),
        'rc_number' => sanitize($_POST['rcNumber']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'company_type' => sanitize($_POST['companyType']),
        'other_company_type' => sanitize($_POST['otherCompanyType']),
        'website' => sanitize($_POST['website']),
        'address' => sanitize($_POST['address']),

        // Section 2
        'full_name' => sanitize($_POST['fullName']),
        'position' => sanitize($_POST['position']),
        'mobile' => sanitize($_POST['mobile']),
        'id_type' => sanitize($_POST['idType']),
        'id_number' => sanitize($_POST['idNumber']),

        // Section 3
        'years_in_business' => sanitize($_POST['yearsInBusiness']),
        'product_lines' => sanitize($_POST['productLines']),
        'monthly_capacity' => sanitize($_POST['monthlyCapacity']),
        'regions_covered' => sanitize($_POST['regionsCovered']),
        'sales_staff' => intval($_POST['salesStaff']),
        'has_warehouse' => sanitize($_POST['hasWarehouse']),
        'preferred_region' => sanitize($_POST['preferredRegion']),
        'has_vehicles' => sanitize($_POST['hasVehicles']),
        'vehicle_details' => sanitize($_POST['vehicleDetails']),

        // Section 4
        'product_interest' => $productInterest,
        'other_product_detail' => sanitize($_POST['otherProductDetail']),
        'has_tech_knowledge' => sanitize($_POST['hasTechKnowledge']),
        'willing_to_train' => sanitize($_POST['willingToTrain']),
        'distribution_start' => sanitize($_POST['distributionStart']),
        'preferred_states' => $preferredStates,
        'international_details' => sanitize($_POST['internationalDetails']),
        'brand_campaign' => sanitize($_POST['brandCampaign']),

        // Section 5
        'bank_name' => sanitize($_POST['bankName']),
        'account_name' => sanitize($_POST['accountName']),
        'account_number' => sanitize($_POST['accountNumber']),
        'bvn' => sanitize($_POST['bvn']),
        'oil_gas_partnerships' => sanitize($_POST['oilGasPartnerships']),
        'kyc_documents' => json_encode($kycDocs),
        'signature' => $signaturePath,

        // Section 6
        'declarant_name' => sanitize($_POST['declarantName']),
        'company_name_declaration' => sanitize($_POST['companyNameDeclaration']),
        'declaration_date' => sanitize($_POST['declarationDate'])
    ];

    // Insert into DB
    $inserted = $conn->insertData('distributor_registrations', $data);

    if ($inserted) {
        echo json_encode(['status' => 'success', 'message' => 'Registration submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save data. Please try again.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
