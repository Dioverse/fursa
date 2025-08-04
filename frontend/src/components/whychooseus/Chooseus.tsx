import React from "react";
// import "./WhyChooseUs.css";

const features = [
  {
    icon: "assets/images/icons/pole.png", // Replace with <img src="..." /> or SVG later
    text: "Backed by Africa’s most advanced lubricant blending facility",
  },
  {
    icon: "assets/images/icons/pole.png",
    text: "Backed by Africa’s most advanced lubricant blending facility",
  },
  {
    icon: "assets/images/icons/pole.png",
    text: "Backed by Africa’s most advanced lubricant blending facility",
  },
  {
    icon: "assets/images/icons/truck.png",
    text: "Strong focus on digital onboarding, retail activation, and price-competitive flagship products",
  },
  {
    icon: "assets/images/icons/headphones.png",
    text: "Integrated marketing and after-sales support",
  },
];

function WhyChooseUs() {
  return (
    <section className="why-choose-us py-5 text-center">
      <div className="container-3 w-95 justify-content-center">
        <h2 className="section-title fw-bold mb--30">
          Why Choose Fursa Energy as Your Trusted <br /> Lubricant Distributor.
        </h2>
        <p className="section-subtitle mb-5">
          Fursa Energy is more than a lubricant distributor — we are a trusted partner for 
          businesses across Nigeria. Backed by MRS Lubricants, one of Africa’s leading blending plants, 
          we deliver high-performance products that meet global standards.
        </p>

        <div className="row justify-content-center">
          {features.map((item, index) => (
            <div key={index} className="col-12 col-sm-6 col-md-4 mb-4">
              <div className="feature-item p-3">
                <div className="icon mb-3"><img src={item.icon} className="img-fluid w-50" alt="" /></div>
                <p className="mb-0">{item.text}</p>
              </div>
            </div>
          ))}

        </div>

        <div className="mt--30 mb--30 button-wrapper">
            <button className="rts-btn btn-primary rounded-sm">View More <i className="fas fa-arrow-right"></i> </button>
        </div>
       
      </div>
    </section>
  );
}

export default WhyChooseUs;