<?php include 'header.php';?>

<style>
  .progress {
    border-radius: 30px;
    overflow: hidden;
  }

  .progress-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
  }
</style>

<!-- SUMMARY PREVIEW TO BE FILLED AND CONVERTED TO PDF -->
<div id="formSummaryPreview" class="d-none p-4">
  <h2 class="text-center">Distributor Registration Summary</h2>

  <h5 style="background:#ddd; padding:5px;">Section 1: Business Information</h5>
  <p>Company Name: <span id="summary_companyName"></span></p>
  <p>Registered Name: <span id="summary_registeredName"></span></p>
  <p>RC Number: <span id="summary_rcNumber"></span></p>
  <p>Email: <span id="summary_email"></span></p>
  <p>Phone: <span id="summary_phone"></span></p>
  <p>Company Type: <span id="summary_companyType"></span></p>
  <p>Website: <span id="summary_website"></span></p>
  <p>Address: <span id="summary_address"></span></p>

  <h5 style="background:#ddd; padding:5px;">Section 2: Contact Person</h5>
  <p>Full Name: <span id="summary_fullName"></span></p>
  <p>Position: <span id="summary_position"></span></p>
  <p>Mobile: <span id="summary_mobile"></span></p>
  <p>Means of ID: <span id="summary_idType"></span></p>
  <p>ID Number: <span id="summary_idNumber"></span></p>

  <h5 style="background:#ddd; padding:5px;">Section 3: Distribution Capacity</h5>
  <p>Years in Business: <span id="summary_yearsInBusiness"></span></p>
  <p>Product Lines: <span id="summary_productLines"></span></p>
  <p>Monthly Capacity: <span id="summary_monthlyCapacity"></span></p>
  <p>Regions Covered: <span id="summary_regionsCovered"></span></p>
  <p>Sales Staff: <span id="summary_salesStaff"></span></p>
  <p>Warehouse: <span id="summary_hasWarehouse"></span></p>
  <p>Preferred Region: <span id="summary_preferredRegion"></span></p>
  <p>Distribution Vehicles: <span id="summary_hasVehicles"></span></p>
  <p>Vehicle Details: <span id="summary_vehicleDetails"></span></p>

  <h5 style="background:#ddd; padding:5px;">Section 4: Distribution Strategy & Product Focus</h5>
  <p>Product Interest: <span id="summary_productInterest"></span></p>
  <p>Other Product Detail: <span id="summary_otherProductDetail"></span></p>
  <p>Technical Knowledge: <span id="summary_hasTechKnowledge"></span></p>
  <p>Willing to Train: <span id="summary_willingToTrain"></span></p>
  <p>Distribution Start: <span id="summary_distributionStart"></span></p>
  <p>Preferred States: <span id="summary_preferredStates"></span></p>
  <p>International Details: <span id="summary_internationalDetails"></span></p>
  <p>Brand Campaign: <span id="summary_brandCampaign"></span></p>

  <h5 style="background:#ddd; padding:5px;">Section 5: Banking & KYC Information</h5>
  <p>Bank Name: <span id="summary_bankName"></span></p>
  <p>Account Name: <span id="summary_accountName"></span></p>
  <p>Account Number: <span id="summary_accountNumber"></span></p>
  <p>BVN: <span id="summary_bvn"></span></p>
  <p>Oil & Gas Partnerships: <span id="summary_oilGasPartnerships"></span></p>

  <h5 style="background:#ddd; padding:5px;">Section 6: Declaration & Consent</h5>
  <p>Declarant Name: <span id="summary_declarantName"></span></p>
  <p>Company Name Declaration: <span id="summary_companyNameDeclaration"></span></p>
  <p>Declaration Date: <span id="summary_declarationDate"></span></p>
</div>

<!-- Form -->
<div class="page-header parallaxie">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box">
                    <h1 class="text-anime-style-3" data-cursor="-opaque">Distributor <span>Registration</span></h1>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Distributor Registration Wizard Start -->
<div id="distributor-form" class="container py-5">
  <div class="card shadow-lg border-0">
    <div class="card-body p-4">
      <h3 class="text-center mb-4">Distributor Registration</h3>
      <!-- Step Progress Indicator -->
    <div class="progress mb-4" style="height: 25px;">
      <div id="progressStep1" class="progress-bar bg-primary" role="progressbar" style="width: 0%">Step 1</div>
      <div id="progressStep2" class="progress-bar bg-secondary" role="progressbar" style="width: 0%">Step 2</div>
      <div id="progressStep3" class="progress-bar bg-secondary" role="progressbar" style="width: 0%">Step 3</div>
      <div id="progressStep4" class="progress-bar bg-secondary" role="progressbar" style="width: 0%">Step 4</div>
      <div id="progressStep5" class="progress-bar bg-secondary" role="progressbar" style="width: 0%">Step 5</div>
    </div>

      <div id="formSummaryContainer">
        <form id="registrationForm">
          <div class="step step-1">
            <h5 class="text-primary mb-3">Section 1: Business Information</h5>
            
              <div class="row">
                
              <div class="mb-3 col-md-6">
                <label class="form-label fw-bold">Company Name:</label>
                <input type="text" class="form-control" name="companyName">
              </div>
              <div class="mb-3 col-md-6">
                <label class="form-label fw-bold">Registered Business Name (if different):</label>
                <input type="text" class="form-control" name="registeredName">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">RC Number:</label>
                <input type="text" class="form-control" name="rcNumber">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Email Address:</label>
                <input type="email" class="form-control" name="email">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Office Phone Number:</label>
                <input type="text" class="form-control" name="phone">
              </div>
            </div>
            
            <div class="row">
              <div class="mb-3 col-md-6">
                <label class="form-label fw-bold">Company Type:</label>
                <select class="form-select" name="companyType" id="companyTypeSelect">
                  <option value="">-- Select Company Type --</option>
                  <option value="Sole Proprietorship">Sole Proprietorship</option>
                  <option value="Partnership">Partnership</option>
                  <option value="Limited Liability Company">Limited Liability Company</option>
                  <option value="Other">Other</option>
                </select>
              </div>

              <div class="mb-3 col-md-6 d-none" id="otherCompanyTypeContainer">
                <label class="form-label fw-bold">Please specify:</label>
                <input type="text" class="form-control" name="otherCompanyType" id="otherCompanyTypeInput">
              </div>
              
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Website (if any):</label>
                <input type="text" class="form-control" name="website">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Business Address:</label>
              <input type="text" class="form-control" name="address">
            </div>
            


            <h5 class="text-primary mb-3">Section 2: Contact Person</h5>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Full Name:</label>
                <input type="text" class="form-control" name="fullName">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Position/Title:</label>
                <input type="text" class="form-control" name="position">
              </div>
            
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Mobile Number:</label>
                <input type="text" class="form-control" name="mobile">
              </div>

              <div class="mb-3 col-md-6">
                <label class="form-label fw-bold">Means of ID:</label>
                <select class="form-select" name="idType" id="idTypeSelect">
                  <option value="">-- Select Means of ID --</option>
                  <option value="National ID">National ID</option>
                  <option value="Driver’s License">Driver’s License</option>
                  <option value="International Passport">International Passport</option>
                  <option value="Voter’s Card">Voter’s Card</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">ID Number:</label>
                <input type="text" class="form-control" name="idNumber">
              </div>
            </div>
            

          </div>

          <div class="step step-2 d-none">
            <h5 class="text-primary mb-3">Section 3: Distribution Capacity</h5>
            <div class="row">
                <div class="mb-3 col-md-2">
                  <label class="form-label fw-bold">Years in Business:</label>
                  <input type="text" class="form-control" name="yearsInBusiness">
                </div>

                <div class="mb-3 col-md-5">
                  <label class="form-label fw-bold">Current Product Lines Distributed (if any):</label>
                  <input type="text" class="form-control" name="productLines">
                </div>

                <div class="mb-3 col-md-5">
                  <label class="form-label fw-bold">Monthly Distribution Capacity (litres or cartons):</label>
                  <input type="text" class="form-control" name="monthlyCapacity">
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label fw-bold">Regions Currently Covered:</label>
                  <input type="text" class="form-control" name="regionsCovered">
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label fw-bold">Number of Sales Staff:</label>
                  <input type="text" class="form-control" name="salesStaff">
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label fw-bold">Existing Warehouse or Storage Facility?</label><br>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="hasWarehouse" value="Yes">
                    <label class="form-check-label">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="hasWarehouse" value="No">
                    <label class="form-check-label">No</label>
                  </div>
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label fw-bold">Preferred Region for Fursa Distribution:</label>
                  <input type="text" class="form-control" name="preferredRegion">
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label fw-bold">Do you have distribution vehicles?</label><br>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="hasVehicles" value="Yes">
                    <label class="form-check-label">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="hasVehicles" value="No">
                    <label class="form-check-label">No</label>
                  </div>
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label fw-bold">If yes, please state number and type:</label>
                  <textarea class="form-control" rows="4" name="vehicleDetails" placeholder="e.g. 2 vans, 1 truck..."></textarea>
                </div>
              </div>
          </div>

          <div class="step step-3 d-none row">
            <h5 class="text-primary mb-3">Section 4: Distribution Strategy & Product Focus</h5>

            <!-- Product Categories -->
            <div class="mb-3 col-md-6">
              <label class="form-label fw-bold">Which category of Fursa/MRS Lubricants are you most interested in? (Please tick all that apply)</label>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="productInterest[]" value="Automotive"><label class="form-check-label">Automotive</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="productInterest[]" value="Industrial"><label class="form-check-label">Industrial</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="productInterest[]" value="Agricultural"><label class="form-check-label">Agricultural</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="productInterest[]" value="Marine"><label class="form-check-label">Marine</label></div>
                </div>
                <div class="col-md-6">
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="productInterest[]" value="Manufacturing"><label class="form-check-label">Manufacturing</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="productInterest[]" value="Commercial Fleet"><label class="form-check-label">Commercial Fleet</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="productInterest[]" value="Motorcycle/Small Engine"><label class="form-check-label">Motorcycle/Small Engine</label></div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="productInterest[]" value="Other" id="otherProductInterest">
                    <label class="form-check-label">Others, please specify</label>
                  </div>
                  <input type="text" class="form-control mt-2 d-none" name="otherProductDetail" id="otherProductDetail">
                </div>
              </div>
            </div>

            <!-- Technical Knowledge -->
            <div class="mb-3 col-md-6">
              <label class="form-label fw-bold">Do You Have Technical Knowledge About Lubricants or a Team That Does?</label><br>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="hasTechKnowledge" value="Yes"><label class="form-check-label">Yes</label></div>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="hasTechKnowledge" value="No"><label class="form-check-label">No</label></div>
            </div>

            <!-- Product Training -->
            <div class="mb-3 col-md-6">
              <label class="form-label fw-bold">Are You Willing to Take Product Training from Fursa?</label><br>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="willingToTrain" value="Yes"><label class="form-check-label">Yes</label></div>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="willingToTrain" value="No"><label class="form-check-label">No</label></div>
            </div>

            <!-- Distribution Onboarding -->
            <div class="mb-3 col-md-6">
              <label class="form-label fw-bold">How Soon Can You Commence Distribution Post-Onboarding?</label><br>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="distributionStart" value="Immediately"><label class="form-check-label">Immediately</label></div>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="distributionStart" value="Within 2 Weeks"><label class="form-check-label">Within 2 Weeks</label></div>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="distributionStart" value="Within a Month"><label class="form-check-label">Within a Month</label></div>
            </div>

            <!-- Preferred States -->
            <div class="mb-3 col-md-6">
              <label class="form-label fw-bold">Preferred States You Would Like to Cover:</label>
              <select data-name="location" class="form-control" multiple name="preferredStates[]" id="preferredStatesSelect">
                <option value="International">International</option>
                <option value="Abia">Abia</option>
                <option value="Adamawa">Adamawa</option>
                <option value="Akwa Ibom">Akwa Ibom</option>
                <option value="Anambra">Anambra</option>
                <option value="Bauchi">Bauchi</option>
                <option value="Bayelsa">Bayelsa</option>
                <option value="Benue">Benue</option>
                <option value="Borno">Borno</option>
                <option value="Cross River">Cross River</option>
                <option value="Delta">Delta</option>
                <option value="Ebonyi">Ebonyi</option>
                <option value="Edo">Edo</option>
                <option value="Ekiti">Ekiti</option>
                <option value="Enugu">Enugu</option>
                <option value="Gombe">Gombe</option>
                <option value="Imo">Imo</option>
                <option value="Jigawa">Jigawa</option>
                <option value="Kaduna">Kaduna</option>
                <option value="Kano">Kano</option>
                <option value="Katsina">Katsina</option>
                <option value="Kebbi">Kebbi</option>
                <option value="Kogi">Kogi</option>
                <option value="Kwara">Kwara</option>
                <option value="Lagos">Lagos</option>
                <option value="Nasarawa">Nasarawa</option>
                <option value="Niger">Niger</option>
                <option value="Ogun">Ogun</option>
                <option value="Ondo">Ondo</option>
                <option value="Osun">Osun</option>
                <option value="Oyo">Oyo</option>
                <option value="Plateau">Plateau</option>
                <option value="Rivers">Rivers</option>
                <option value="Sokoto">Sokoto</option>
                <option value="Taraba">Taraba</option>
                <option value="Yobe">Yobe</option>
                <option value="Zamfara">Zamfara</option>
              </select>
            </div>


            <!-- International Location Textbox -->
            <div class="mb-3 col-md-6 d-none" id="internationalDetailsContainer">
              <label class="form-label fw-bold">Please specify international location(s):</label>
              <input type="text" class="form-control" name="internationalDetails" id="internationalDetailsInput">
            </div>


            <!-- Campaign Participation -->
            <div class="mb-3 col-md-6">
              <label class="form-label fw-bold">Would You Participate in Brand Promotions/Co-Funding Local Campaigns?</label><br>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="brandCampaign" value="Yes"><label class="form-check-label">Yes</label></div>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="brandCampaign" value="No"><label class="form-check-label">No</label></div>
              <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="brandCampaign" value="Depends on Arrangement"><label class="form-check-label">Depends on Arrangement</label></div>
            </div>
          </div>

          <?php
          $requiredDocuments = [
            'CAC Certificate',
            'Form C07 (List of Directors)',
            'MEMART',
            'Utility Bill (Office Address, not older than 3 months)',
            'Business Tax Identification Number (TIN)',
            'ID of Contact Person',
            'Letter of Introduction from a Referee'
          ];
          ?>

          <div class="step step-4 d-none">
            <h5 class="text-primary mb-4">Section 5: Banking & KYC Information</h5>

            <!-- Bank Details -->
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Bank Name</label>
                <input type="text" class="form-control" name="bankName" placeholder="Enter Bank Name">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Account Name</label>
                <input type="text" class="form-control" name="accountName" placeholder="Enter Account Name">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Account Number</label>
                <input type="text" class="form-control" name="accountNumber" placeholder="Enter Account Number">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">BVN of Contact Person/Director <span class="text-muted">(For KYC Only)</span></label>
                <input type="text" class="form-control" name="bvn" placeholder="Enter BVN">
              </div>
            </div>

            <!-- Oil & Gas Partnerships -->
            <div class="mb-4">
              <label class="form-label fw-bold">Any current partnerships with Oil & Gas or Lubricant Companies? <span class="text-muted">(Briefly explain)</span></label>
              <textarea class="form-control" name="oilGasPartnerships" rows="4" placeholder="e.g. Yes, with XYZ Lubricants..."></textarea>
            </div>

            <!-- Required Document Uploads -->
            <div class="mb-4 border p-4 rounded bg-light row">
              <h6 class="fw-bold mb-3">Upload the following documents:</h6>

              <?php foreach ($requiredDocuments as $index => $doc): ?>
                <div class="mb-3 col-md-6">
                  <label class="form-label fw-semibold"><?php echo htmlspecialchars($doc); ?></label>
                  <input type="file" class="form-control" name="kycDocuments[<?php echo $index; ?>]" required>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          

          <div class="step step-5 d-none">
            <h5 class="text-primary mb-4">Section 6: Declaration & Consent</h5>

            <p>
              I, <strong><input type="text" class="form-control d-inline w-auto" name="declarantName" placeholder="Your Full Name" required></strong>,
              acting on behalf of <strong><input type="text" class="form-control d-inline w-auto" name="companyNameDeclaration" placeholder="Company Name" required></strong>,
              declare that the information provided herein is accurate and complete to the best of my knowledge.
            </p>

            <p>
              I understand that Fursa Energy may carry out verification and reserve the right to reject or revoke distributorship if any information is found to be false or misleading.
            </p>

            <div class="row mt-4">
              <!-- Signature Upload -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Signature (Upload Image or Draw):</label>
                <input type="file" name="signature" class="form-control" accept="image/*" required>
                <small class="form-text text-muted">You can upload a signature image (PNG/JPG).</small>
              </div>

              <!-- Date -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Date:</label>
                <input type="date" class="form-control" name="declarationDate" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
          </div>

          
          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" id="prevStep" disabled>Previous</button>
            <button type="button" class="btn btn-primary" id="nextStep">Next</button>
          </div>
        </form>
        <button type="button" class="btn btn-success mt-3 d-none"  id="downloadPdfBtn">Download PDF Summary</button>

      </div>
    </div>
  </div>
</div>
<!-- Distributor Registration Wizard End -->
<?php include 'footer.php';?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
  function generateSummaryData() {
    const fieldMap = [
      "companyName", "registeredName", "rcNumber", "email", "phone", "companyType", "website", "address",
      "fullName", "position", "mobile", "idType", "idNumber",
      "yearsInBusiness", "productLines", "monthlyCapacity", "regionsCovered", "salesStaff",
      "hasWarehouse", "preferredRegion", "hasVehicles", "vehicleDetails",
      "otherProductDetail", "hasTechKnowledge", "willingToTrain", "distributionStart",
      "internationalDetails", "brandCampaign", "bankName", "accountName", "accountNumber",
      "bvn", "oilGasPartnerships", "declarantName", "companyNameDeclaration", "declarationDate"
    ];

    fieldMap.forEach(field => {
      const value = document.querySelector(`[name="${field}"]`)?.value || '';
      document.getElementById(`summary_${field}`).textContent = value;
    });

  // Special: Checkboxes (productInterest)
    const interests = Array.from(document.querySelectorAll('[name="productInterest[]"]:checked'))
      .map(i => i.value).join(', ');
      document.getElementById("summary_productInterest").textContent = interests;

      // Multi-select: preferredStates
      const selectedStates = Array.from(document.querySelector('[name="preferredStates[]"]').selectedOptions)
        .map(opt => opt.value).join(', ');
      document.getElementById("summary_preferredStates").textContent = selectedStates;

      document.getElementById("formSummaryPreview").classList.remove("d-none");
      document.getElementById("downloadPdfBtn").classList.remove("d-none");
}

// PDF Generation
document.getElementById("downloadPdfBtn").addEventListener("click", () => {
  generateSummaryData()
  const summary = document.getElementById("formSummaryPreview");

  html2canvas(summary, { scale: 2 }).then(canvas => {
    const imgData = canvas.toDataURL("image/png");
    const pdf = new jspdf.jsPDF("p", "mm", "a4");
    const width = pdf.internal.pageSize.getWidth();
    const height = (canvas.height * width) / canvas.width;

    pdf.addImage(imgData, "PNG", 0, 0, width, height);
    pdf.save("Distributor_Registration_Summary.pdf");
  });
});
</script>



<script>
  $(document).ready(function () {
    $('#preferredStatesSelect').select2({
      placeholder: "Select Mulitple states",
      width: '100%',
      closeOnSelect: false,
    });

    $('#preferredStatesSelect').on('change', function () {
      const selected = $(this).val() || [];
      if (selected.includes('International')) {
        $('#internationalDetailsContainer').removeClass('d-none');
      } else {
        $('#internationalDetailsContainer').addClass('d-none');
        $('#internationalDetailsInput').val('');
      }
    });
  });
</script>



<script>
  document.getElementById('preferredStatesSelect').addEventListener('change', function () {
    const selectedOptions = Array.from(this.selectedOptions).map(option => option.value);
    const intlContainer = document.getElementById('internationalDetailsContainer');
    
    if (selectedOptions.includes('International')) {
      intlContainer.classList.remove('d-none');
    } else {
      intlContainer.classList.add('d-none');
      document.getElementById('internationalDetailsInput').value = '';
    }
  });

  document.getElementById('companyTypeSelect').addEventListener('change', function () {
    const otherInputContainer = document.getElementById('otherCompanyTypeContainer');
    if (this.value === 'Other') {
      otherInputContainer.classList.remove('d-none');
    } else {
      otherInputContainer.classList.add('d-none');
      document.getElementById('otherCompanyTypeInput').value = '';
    }
  });
</script>

<script>
  let currentStep = 1;
  const form = document.getElementById('registrationForm');
  const steps = form.querySelectorAll('.step');
  const indicators = document.querySelectorAll('.step-indicator'); // assumes step indicators are marked with this class

  document.addEventListener('DOMContentLoaded', () => {
    const savedData = localStorage.getItem('distributorForm');
    if (savedData) {
      const formData = JSON.parse(savedData);
      for (const name in formData) {
        const field = form.elements[name];
        if (field) field.value = formData[name];
      }
    }
    updateStepIndicator(currentStep);
    updateStepProgressBar(currentStep);
  });

  document.getElementById('nextStep').addEventListener('click', () => {
    saveToLocalStorage();
    if (currentStep < steps.length) {
      steps[currentStep - 1].classList.add('d-none');
      steps[currentStep].classList.remove('d-none');
      currentStep++;
      updateButtons();
      updateStepIndicator(currentStep);
      updateStepProgressBar(currentStep);
    }
  });

  document.getElementById('prevStep').addEventListener('click', () => {
    if (currentStep > 1) {
      steps[currentStep - 1].classList.add('d-none');
      steps[currentStep - 2].classList.remove('d-none');
      currentStep--;
      updateButtons();
      updateStepIndicator(currentStep);
      updateStepProgressBar(currentStep);
    }
  });

  function updateButtons() {
    document.getElementById('prevStep').disabled = currentStep === 1;
    document.getElementById('nextStep').textContent = currentStep === steps.length ? 'Submit' : 'Next';
  }

  function saveToLocalStorage() {
    const data = {};
    Array.from(form.elements).forEach(input => {
      if (input.name) data[input.name] = input.value;
    });
    localStorage.setItem('distributorForm', JSON.stringify(data));
  }

  function updateStepIndicator(current) {
    const indicators = document.querySelectorAll('.step-indicator');
    indicators.forEach((step, index) => {
      const icon = step.querySelector('i');
      step.classList.remove('text-success', 'text-primary', 'text-muted');

      if (index + 1 < current) {
        step.classList.add('text-success');
        if (icon) icon.className = 'bi bi-check-circle-fill';
      } else if (index + 1 === current) {
        step.classList.add('text-primary');
        if (icon) icon.className = 'bi bi-dot';
      } else {
        step.classList.add('text-muted');
        if (icon) icon.className = 'bi bi-circle';
      }
    });
  }

  function updateStepProgressBar(currentStep) {
    const totalSteps = 5; // Change this if you have more or fewer steps

    for (let i = 1; i <= totalSteps; i++) {
      const stepBar = document.getElementById(`progressStep${i}`);
      stepBar.classList.remove('bg-primary', 'bg-success', 'bg-secondary');

      if (i < currentStep) {
        stepBar.classList.add('bg-success');
        stepBar.style.width = `${100 / totalSteps}%`;
        stepBar.textContent = `Step ${i}`;
      } else if (i === currentStep) {
        stepBar.classList.add('bg-primary');
        stepBar.style.width = `${100 / totalSteps}%`;
        stepBar.textContent = `Step ${i}`;
      } else {
        stepBar.classList.add('bg-secondary');
        stepBar.style.width = `${100 / totalSteps}%`;
        stepBar.textContent = `Step ${i}`;
      }
    }
  }
</script>

