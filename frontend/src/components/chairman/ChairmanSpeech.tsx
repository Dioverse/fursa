import React from "react";

function ChairmanMessage() {
  return (
    <section className="chairman-message py-5">
      <div className="container-3">
        <h2 className="text-center fw-bold mb--50 mt--70">Message from the chairman</h2>

        <div className="row align-items-center">
          <div className="col-md-4 text-center mb-4 mb-md-0">
            <div className="thumbnail-left">
                <img src="assets/images/about/chairman.png" alt="Chairman" className="img-fluid rounded-4 shadow-sm" />
            </div>
            {/* <img src="assets/images/about/chairman.png" alt="Chairman" className="img-fluid rounded-4 shadow-sm" /> */}
          </div>

          <div className="col-lg-8 pl--60 pl_md--10 pt_md--30 pl_sm--10 pt_sm--30">
            <h4 className="fw-semibold mb-1 mb--30">Chairman’s Message</h4>
            <p>
              At FURSA, we believe that quality engine oil is not just a product — it’s a promise of performance,
              protection, and reliability. Our mission is to provide motorists, mechanics, and businesses with oil
              solutions they can truly depend on. <br/> <br/>
              As you explore our range of fully synthetic, high-mileage, and conventional oils, know that each product
              is backed by our commitment to excellence and customer satisfaction.
              <br />
              <br />
              Thank you for being part of the FURSA journey.
              <br />
              <br />
              <span>Sincerely,<br />The Chairman<br />FURSA</span>
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}

export default ChairmanMessage