import React from "react";

function HeroSection() {
  return (
    <section className="hero-section text-white text-center">
      <div className="container">
        <span className="badge text-dark bg-light px-3 py-1 mb-3 small rounded-pill">
          ⚡ AI-Powered Engine Analysis
        </span>

        <h1 className="fw-bold text-white mb-3">
          Get to Know the Best Lube <br /> <span className="highlight">for Your Engine</span>
        </h1>

        <p className="mb-4 text_light-1">
          Answer a few quick questions about your vehicle and driving habits. Our AI will <br/> analyze your needs and
          recommend the perfect lubricant for optimal engine <br/> performance.
        </p>

        <div className="d-flex justify-content-center gap-3 flex-wrap mb-4">
          <button className="rts-btn btn-white d-flex align-items-center gap-2">
            <span>🧪</span> Start Engine Assessment →
          </button>
          <button className="rts-btn btn-outline-white px-4 py-2 d-flex align-items-center gap-2">
            <span>📦</span> View All Products
          </button>
        </div>

        <div className="stats d-flex justify-content-center gap-4 text-white small flex-wrap">
          <div>
            <strong>3 min</strong> <br /> Quick Assessment
          </div>
          <div>
            <strong>98%</strong> <br /> Accuracy Rate
          </div>
          <div>
            <strong>5000+</strong> <br /> Happy Customers
          </div>
        </div>
      </div>
    </section>
  );
}


export default HeroSection