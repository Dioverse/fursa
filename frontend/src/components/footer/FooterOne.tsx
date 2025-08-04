import React from 'react';

function FooterOne() {
    return (
        <footer className="footer bg-dark text-white pt-5 pb-4">
            <div className="container-3">
                <div className="row">
                    {/* Contact Info */}
                    <div className="col-md-3 mb-4">
                        <a href="/" className="logo-area mb--40">
                            <img src="/assets/images/logo/logo-03.svg" alt="logo-main" className="logo" />
                        </a>
                        <p className='text-white lead'>
                            <img src="assets/images/icons/mail.png" alt="email" width={16} className="me-2" />
                            info@fursaenergy.com
                        </p>
                        <p className='text-white lead'>
                            <img src="assets/images/icons/phone.png" alt="phone" width={16} className="me-2" />
                            +234-XXX-000-0000
                        </p>
                        <p className='text-white mb--20 lead'>
                            <img src="assets/images/icons/location.svg" alt="location" width={16} className="me-2" />
                            4, Morija Close, Wuse 2, Abuja <br/> 6, Olumo Street, Parkview Estate, Ikoyi, Lagos
                        </p>
                        <p className='text-white lead'>
                            <img alt="location" width={16} className="me-2" />
                            6, Olumo Street, Parkview Estate, Ikoyi, Lagos
                        </p>
                    </div>

                    {/* Quick Link */}
                    <div className="col-md-2 mb-4">
                        <h5 className="mb-3 text-white">Quick Link</h5>
                        <ul className="list-unstyled">
                            <li><a href="#" className="text-white">Home</a></li>
                            <li><a href="#" className="text-white">About Us</a></li>
                            <li><a href="#" className="text-white">Contact Us</a></li>
                            <li><a href="#" className="text-white">Shop</a></li>
                        </ul>
                    </div>

                    {/* Help */}
                    <div className="col-md-2 mb-4">
                        <h5 className="mb-3 text-white">Help</h5>
                        <ul className="list-unstyled">
                            <li><a href="#" className="text-white text-decoration-none">FAQ</a></li>
                            <li><a href="#" className="text-white text-decoration-none">Terms & Condition</a></li>
                            <li><a href="#" className="text-white text-decoration-none">Privacy Policy</a></li>
                            <li><a href="#" className="text-white text-decoration-none">Refund Policy</a></li>
                        </ul>
                    </div>

                    {/* Newsletter */}
                    <div className="col-md-5">
                        <h5 className="mb-3 text-white">Our Newsletter</h5>
                        <div className="d-flex">
                            <input type="email" className="form-control me-2" placeholder="Enter Your Email..." />
                            <button className="btn btn-outline-light">
                                <img src="assets/images/icons/linkedin.svg" alt="send" width={16} />
                            </button>
                        </div>
                        <div className="mt-3">
                            <a href="#" className="me-3">
                                <i className="fa fa-linkedin text-lg text-white"></i>
                                <img src="assets/images/icons/linkedin.svg" alt="linkedin" width={20} />
                            </a>
                            <a href="#" className="me-3">
                                <img src="assets/images/icons/facebook.svg" alt="facebook" width={20} />
                            </a>
                            <a href="#" className="me-3">
                                <img src="assets/images/icons/twitter.svg" alt="twitter" width={20} />
                            </a>
                            <a href="#">
                                <img src="assets/images/icons/instagram.svg" alt="instagram" width={20} />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
}

export default FooterOne;
