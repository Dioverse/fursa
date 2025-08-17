import React from "react";

const blogs = [
  {
    // bg: "bg-[#012996]", // blue
    bg: "bg-info",
    title: "Choosing the Right Engine Oil: What Retailers Need to Know.",
    subTitle: "When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you're a retailer guiding customers or a car owner trying to make the right decision for your vehicle, understanding the role of engine oil and choosing the right type is essential......",
    img: "assets/images/feature/engine.png",
    author: "Adams Farida",
    date: "12th Of August 2025"
  },
  {
    //bg: "bg-[#F5A623]",yellow-orange
    bg: "bg-warning",
    title: "Choosing the Right Engine Oil: What Retailers Need to Know.",
    subTitle: "When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you're a retailer guiding customers or a car owner trying to make the right decision for your vehicle, understanding the role of engine oil and choosing the right type is essential......",
    img: "assets/images/feature/engine.png",
    author: "Adams Farida",
    date: "12th Of August 2025"
  },
  {
    //bg: "bg-[#3A5F3A]", // green
    bg: "bg-success",
    title: "Choosing the Right Engine Oil: What Retailers Need to Know.",
    subTitle: "When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you're a retailer guiding customers or a car owner trying to make the right decision for your vehicle, understanding the role of engine oil and choosing the right type is essential......",
    img: "assets/images/feature/engine.png",
    author: "Adams Farida",
    date: "12th Of August 2025"
  }
];

function BlogHome() {
  return (
    <section className="hero-section text-white text-center">
      <div className="container-3 w-95 justify-content-center">
        <h3 className="fw-bold text-white mb--30">
          Our Blog
        </h3>

        <div className="row">
            {blogs.map((blog, idx) => (
            <div key={idx} className="col-lg-4 col-md-4 m-0">
                {/* <div className="single-customers-feedback-area bg-white"> */}
                <div className="rounded-4 single-customers-feedback-area p-0 border-0 overflow-hidden shadow bg-white h-100 d-flex flex-column">
                    <div className={`p-4 ${blog.bg}`}>
                        <img className="img-fluid mx-auto d-block" style={{ width: "80%" }} src={blog.img} alt="logo" />
                    </div>
                    <div className="body-content container-3 p-4 flex-grow-1 d-flex flex-column justify-content-between">
                        <h4 className="text-start text-primary">{blog.title}</h4>
                        <p className="text-start" >
                           {blog.subTitle} 
                        </p>
                        <div className="row">
                            <div className="col-lg-7 col-md-7">
                                <p className="text-start">Posted by {blog.author} <br/> <small> {blog.date}</small></p>
                            </div>
                            <div className="col-lg-5">
                                <button className="rts-btn btn-outline-primary btn-sm">Learn More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ))}

        </div>


      </div>
    </section>
  );
}


export default BlogHome