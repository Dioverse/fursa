import React from 'react';

function FooterOne() {
    return (
        <footer className="rts-footer-area pt--80 bg-dark text-white pt-5 pb-4">
            <div className="container-3 p-4 w-95 justify-content-center pb--50">
                <div className="row">
                    {/* Contact Info */}
                    <div className="col-md-4 mb-4">
                        <a href="/" className="logo-area mb-6">
                            <img src="/assets/images/logo/logo-03.svg" alt="logo-main" className="logo" />
                        </a>
                        <p className='text-white lead mb--20 mt--20'>
                            <img src="assets/images/icons/mail.png" alt="email" width={16} className="me-2" />
                            info@fursaenergy.com
                        </p>
                        <p className='text-white lead mb--20 mt-0'>
                            <img src="assets/images/icons/phone.png" alt="phone" width={16} className="me-2" />
                            +234-XXX-000-0000
                        </p>
                        <p className='text-white mb--10 lead'>
                            <img src="assets/images/icons/location.svg" alt="location" width={16} className="me-2" />
                            4, Morija Close, Wuse 2, Abuja
                        </p>
                        <p className='text-white lead'>
                            <span className="me-2"> &nbsp;&nbsp;&nbsp; </span>
                            6, Olumo Street, Parkview Estate, Ikoyi, Lagos
                        </p>
                    </div>

                    {/* Quick Link */}
                    <div className="col-md-2 mb-4">
                        <h4 className="mb-3 text-white">Quick Link</h4>
                        <ul className="list-unstyled">
                            <li><a href="#" className="text-white">Home</a></li>
                            <li><a href="#" className="text-white">About Us</a></li>
                            <li><a href="#" className="text-white">Contact Us</a></li>
                            <li><a href="#" className="text-white">Shop</a></li>
                        </ul>
                    </div>

                    {/* Help */}
                    <div className="col-md-2 mb-4">
                        <h4 className="mb-3 text-white">Help</h4>
                        <ul className="list-unstyled">
                            <li><a href="#" className="text-white text-decoration-none">FAQ</a></li>
                            <li><a href="#" className="text-white text-decoration-none">Terms & Condition</a></li>
                            <li><a href="#" className="text-white text-decoration-none">Privacy Policy</a></li>
                            <li><a href="#" className="text-white text-decoration-none">Refund Policy</a></li>
                        </ul>
                    </div>

                    {/* Newsletter */}
                    <div className="col-md-4">
                        <h4 className="mb-3 text-white">Our Newsletter</h4>
                        <div className="d-flex">
                            <input type="email" className="form-control border-white border-2 me-2 text-white" style={{ backgroundColor: 'transparent', border: 'solid 2px #fefefe', borderRadius: '6px' }} placeholder="Enter Your Email..." />
                            <button className="rts-btn" style={{ position: 'relative', left: '-15%'}}>
                                <img src="assets/images/icons/send.svg" alt="send" width={20} />
                            </button>
                        </div>
                        <div className="mt--20">
                            <a href="#" className="me-4 border-2 border-white">
                                <i className="fa-light fas-linkedin text-lg text-white" />
                            </a>
                            <a href="#" className="me-4">
                                <img src="assets/images/icons/linkedin.svg" alt="linkedin" width={40} />
                            </a>
                            <a href="#" className="me-4">
                                <img src="assets/images/icons/facebook.svg" alt="facebook" width={40} />
                            </a>
                            <a href="#" className="me-4">
                                <img src="assets/images/icons/twitter.svg" alt="twitter" width={40} />
                            </a>
                            <a href="#">
                                <img src="assets/images/icons/instagram.svg" alt="instagram" width={40} />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
}

export default FooterOne;
