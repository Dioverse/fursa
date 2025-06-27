<?php include "./header.php" ?>
<style>
    .contact-info-box .icon-box {
    width: 40px;
    flex-shrink: 0;
    display: flex;
    justify-content: center;
    align-items: start;
    margin-top: 5px;
}

.contact-info-box .icon-box i {
    font-size: 1.5rem;
    color: #ffffff !important;
    transition: color 0.3s ease;
}

.contact-info-box {
    background: var(--accent-color);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    color: #ffffff;
}

.contact-info-box .section-title h2, .contact-info-box .section-title h3 {
    color: #ffffff;
}

.contact-info-body h4 {
    margin-bottom: 4px;
    color: #ffffff;
    font-size: 20px;
    font-weight: 600;
}

.contact-info-body p {
    margin: 0;
    font-size: 0.95rem;
    color: #ffffff
}

.contact-info-body a {
    color: black;
    text-decoration: none;
}
.contact-info-item, .contact-us-form{
    width: 100% !important;
}


</style>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-2" data-cursor="-opaque">Contact <span>us</span></h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">contact us</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Page Contact Us Start -->
    <div class="page-contact-us">
        <div class="container">
            <div class="row">
                <!-- Contact Info Column -->
                <div class="col-lg-5 contact-info-column">
                    <div class="contact-info-box d-flex flex-column gap-4">
                        <div class="section-title mb-4">
                            <h3 class="wow fadeInUp">Contact Info</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">Reach out <span>to us</span></h2>
                        </div>

                        <!-- Phone -->
                        <div class="contact-info-item d-flex align-items-start gap-3">
                            <div class="icon-box">
                                <i class="fas fa-phone fa-lg text-primary"></i>
                            </div>
                            <div class="contact-info-body">
                                <h4>Phone</h4>
                                <p><a href="tel:+2348012345678">+234 801 234 5678</a></p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="contact-info-item d-flex align-items-start gap-3">
                            <div class="icon-box">
                                <i class="fas fa-envelope fa-lg text-primary"></i>
                            </div>
                            <div class="contact-info-body">
                                <h4>Email</h4>
                                <p><a href="mailto:info@fursaenergy.com">info@fursaenergy.com</a></p>
                            </div>
                        </div>

                        <!-- Lagos Office -->
                        <div class="contact-info-item d-flex align-items-start gap-3">
                            <div class="icon-box">
                                <i class="fas fa-map-marker-alt fa-lg text-primary"></i>
                            </div>
                            <div class="contact-info-body">
                                <h4>Lagos Office</h4>
                                <p>6 Olumo Street, Parkview Estate, Ikoyi, Lagos</p>
                            </div>
                        </div>

                        <!-- Abuja Office -->
                        <div class="contact-info-item d-flex align-items-start gap-3">
                            <div class="icon-box">
                                <i class="fas fa-map-marker-alt fa-lg text-primary"></i>
                            </div>
                            <div class="contact-info-body">
                                <h4>Abuja Office</h4>
                                <p>4 Morija Close, Wuse 2, Abuja</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form Column -->
                <div class="col-lg-7">
                    <div class="contact-us-form">
                        <!-- Section Title -->
                        <div class="section-title mb-4">
                            <h3 class="wow fadeInUp">Get in Touch</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">Send us a <span>message</span></h2>
                        </div>

                        <!-- Form Start -->
                        <form id="contactForm" action="#" method="POST" data-toggle="validator" class="wow fadeInUp" data-wow-delay="0.2s">
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    <input type="text" name="fname" class="form-control" placeholder="First name" required>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <input type="text" name="lname" class="form-control" placeholder="Last name" required>
                                </div>
                                <div class="form-group col-md-12 mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                                </div>
                                <div class="form-group col-md-12 mb-3">
                                    <input type="text" name="phone" class="form-control" placeholder="Phone" required>
                                </div>
                                <div class="form-group col-md-12 mb-4">
                                    <textarea name="message" class="form-control" rows="4" placeholder="Your message..." required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn-default"><span>Submit Message</span></button>
                                </div>
                            </div>
                        </form>
                        <!-- Form End -->
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page Contact Us End -->
<?php include "./footer.php" ?>