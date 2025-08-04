"use client";

import React, { useEffect, useState } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay } from 'swiper/modules';




const CustomerFeedback = () => {
    return (
        <div className="rts-cuystomers-feedback-area rts-section-gap2">
            <div className="container-3">
                <div className="row">
                    <div className="col-lg-12 justify-content-center">
                        <div className="title-area-left text-center pl--0">
                            <h2 className="title-center">See What Clients Are Saying</h2>
                            <h6 className='sub-title mb-1'>We are very proud of the service we provide and stand by every product we carry.</h6>
                            <p>Read our testimonials from our happy customers.</p>
                        </div>
                    </div>
                </div>

                <div className="row mt--50">
                    <div className="col-lg-12">
                        <div className="customers-feedback-area-main-wrapper">
                            <div className="rts-caregory-area-one">
                                <div className="row">
                                    <div className="col-lg-12">
                                        <div className="category-area-main-wrapper-one">
                                            <Swiper
                                                modules={[Autoplay]}
                                                scrollbar={{
                                                    hide: true,
                                                }}
                                                autoplay={{
                                                    delay: 3000, // Delay between transitions (3 seconds)
                                                    disableOnInteraction: false, // Continue autoplay after user interactions
                                                }}
                                                loop={true}
                                                className="mySwiper-category-1"
                                                breakpoints={{
                                                    0: { slidesPerView: 1, spaceBetween: 10 },
                                                    480: { slidesPerView: 1, spaceBetween: 10 },
                                                    640: { slidesPerView: 1, spaceBetween: 10 },
                                                    840: { slidesPerView: 2, spaceBetween: 10 },
                                                    1140: { slidesPerView: 3, spaceBetween: 30 }, // 👈 Update this to 3 slides per view
                                                }}
                                            >
                                                <SwiperSlide>   
                                                    <div className="single-customers-feedback-area bg_primary">
                                                        <div className="body-content">
                                                            <p className="disc text-white mt--20">
                                                                “According to the council of supply chain professionals the council of
                                                                logistics management logistics is the process of planning, implementing
                                                                and controlling procedures”
                                                            </p>
                                                        </div>
                                                        <div className="justify-content-center border-0 mb--0 mt--30">
                                                            <div className="text-center text-white">
                                                                <div className="">
                                                                    <h4 className="title text-white">Ahmed Bello, Fleet Manager, <br/> North Logistics Ltd </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </SwiperSlide><SwiperSlide>
                                                    <div className="single-customers-feedback-area bg_primary">
                                                        <div className="body-content">
                                                            <p className="disc text-white mt--20">
                                                                “According to the council of supply chain professionals the council of
                                                                logistics management logistics is the process of planning, implementing
                                                                and controlling procedures”
                                                            </p>
                                                        </div>
                                                        <div className="justify-content-center border-0 mb--0 mt--30">
                                                            <div className="text-center text-white">
                                                                <div className="">
                                                                    <h4 className="title text-white">Ahmed Bello, Fleet Manager, <br/> North Logistics Ltd </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </SwiperSlide><SwiperSlide>
                                                    <div className="single-customers-feedback-area bg_primary">
                                                        <div className="body-content">
                                                            <p className="disc text-white mt--20">
                                                                “According to the council of supply chain professionals the council of
                                                                logistics management logistics is the process of planning, implementing
                                                                and controlling procedures”
                                                            </p>
                                                        </div>
                                                        <div className="justify-content-center border-0 mb--0 mt--30">
                                                            <div className="text-center text-white">
                                                                <div className="">
                                                                    <h4 className="title text-white">Ahmed Bello, Fleet Manager, <br/> North Logistics Ltd </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </SwiperSlide><SwiperSlide>
                                                    <div className="single-customers-feedback-area bg_primary">
                                                        <div className="body-content">
                                                            <p className="disc text-white mt--20">
                                                                “According to the council of supply chain professionals the council of
                                                                logistics management logistics is the process of planning, implementing
                                                                and controlling procedures”
                                                            </p>
                                                        </div>
                                                        <div className="justify-content-center border-0 mb--0 mt--30">
                                                            <div className="text-center text-white">
                                                                <div className="">
                                                                    <h4 className="title text-white">Ahmed Bello, Fleet Manager, <br/> North Logistics Ltd </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </SwiperSlide>
                                            </Swiper>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {/* rts-caregory-area-one end */}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CustomerFeedback;
