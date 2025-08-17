import React from "react";
// import "./WhyChooseUs.css";


function Partner() {
  return (
    <section className="why-choose-us py-5 text-center">
      <div className="container-3">
        <h2 className="section-title fw-bold mb--30 mt--70">
          Our Strategic Partners
        </h2>
        <p className="section-subtitle mb-5">
          Fursa Energy collaborates with industry leaders to ensure quality, reliability, and reach across every region we serve.
        </p>

        <div className="row justify-content-center">
            <div className="col-12 col-sm-6 col-md-4 mb-4">
              <div className="feature-item p-3">
                <img className="mb-3" src="assets/images/partner/mrs.png"/>
              </div>
            </div>

            <div className="col-12 col-sm-6 col-md-4 mb-4">
              <div className="feature-item p-3">
                <img className="mb-3" src="assets/images/partner/mrs.png"/>
              </div>
            </div>

        </div>
       
      </div>
    </section>
  );
}

export default Partner;