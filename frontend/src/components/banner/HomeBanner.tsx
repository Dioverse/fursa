import React from 'react';

function HomeBanner() {
  return (
    <div className="banner-section">
      <div className="container-3 w-95">
        <div className="content-wrapper">
          <div className="text-content">
            <p className="small-text text-white">Built on Trust. Focused on Trade. Committed to Growth.</p>
            <h3 className="main-heading text-white mb--50">
              Fursa Energy is a Nigerian energy company and the official 
              <span className="highlight"> super distributor </span>
              of MRS Lubricants.
            </h3>
            <div className="btn-group mt--30">
              <a href="#" className="rts-btn btn-outline-white">Shop Now <i className="fas fa-arrow-right"></i> </a>
              <a href="#" className="rts-btn btn-primary">Become a Distributor</a>
            </div>
          </div>
          <div className="image-content">
            <img src="/assets/images/banner/lubricants.png" alt="MRS Lubricants" className='img-fluid' width={1000} />
          </div>
        </div>
      </div>
    </div>
  );
}

export default HomeBanner;
