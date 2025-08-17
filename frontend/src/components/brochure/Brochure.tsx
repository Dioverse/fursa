import React from "react";
// import "./WhyChooseUs.css";


function Brochure() {
  return (
    <section className="why-choose-us py-5 text-center justify-content-center">
      <div className="container-3 mt--70 mb--30">
        <div className="row">
            <div className="col-lg-12 justify-content-center">
                <div className="title-area-left text-center pl--0">
                    <h2 className="title-center">Click on the Download Button to Download MRS product Brochure</h2>
                    <div className="mt--30 mb--30 button-wrapper"> 
                      <a className="rts-btn btn-primary btn-sm" ><i className="fas fa-download"></i> Download Brochure</a>
                    </div>
                    
                </div>
            </div>

        </div>
       
      </div>
    </section>
  );
}

export default Brochure;