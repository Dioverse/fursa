import BannerOne from "@/components/banner/BannerOne";
import AboutBanner from "@/components/banner/AboutBanner";
import FeatureOne from "@/components/feature/FeatureOne";
import HeaderOne from "@/components/header/HeaderOne";
import DiscountProduct from "@/components/product/DiscountProduct";
import FeatureProduct from "@/components/product/FeatureProduct";
import WeeklyBestSelling from "@/components/product/WeeklyBestSelling";
import FeatureDiscount from "@/components/product/FeatureDiscount";
import TrandingProduct from "@/components/product/TrandingProduct";
import BlogOne from "@/components/blog/BlogOne";
import FooterOne from "@/components/footer/FooterOne";
import { CartProvider } from "@/components/header/CartContext";
import { WishlistProvider } from "@/components/header/WishlistContext";
import { ToastContainer, toast } from 'react-toastify';
import CategoryBb from '../components/banner/CategoryBb';
import AboutOne from "@/components/about/AboutOne";
import Chooseus from "@/components/whychooseus/Chooseus"
import HeroSection from "@/components/hero/HeroSection";
import ChairmanSpeech from "@/components/chairman/ChairmanSpeech";
import Partner from "@/components/partner/Partners";
import BusinessModel from "@/components/businessmodel/BusinessModel";
import CustomerFeedback from "@/components/testimonials/TestimonilsOne";
import BlogHome from "@/components/blog/BlogHome";
import Brochure from "@/components/brochure/Brochure";


export default function Home() {
  return (
    <WishlistProvider>
      <CartProvider>
        <div className="demo-one">
          
        <ToastContainer position="top-right" autoClose={3000} />
          <HeaderOne />
          {/* <BannerOne /> */}
          <AboutBanner />
          {/* <CategoryBb /> */}
          <CategoryBb />
          {/* <FeatureOne /> */}
          <FeatureProduct />
          {/* About section */}
          <AboutOne/>

          {/* Why choose us */}
          <Chooseus />

          {/* Herosection */}
          <HeroSection />

          {/* Chairman speech */}
          <ChairmanSpeech />

          {/* Strategic partners */}
          <Partner/>

          {/* Business type model */}
          <BusinessModel />

          {/* Testimonial */}
          <CustomerFeedback />

          {/* Our blog */}
          <BlogHome />

          {/* Brochure */}
          <Brochure />

          {/* <DiscountProduct /> */}
          {/* <WeeklyBestSelling /> */}
          {/* <FeatureDiscount />
          <TrandingProduct />
          <BlogOne /> */}
          <FooterOne />
        </div>
      </CartProvider>
      </WishlistProvider>
  );
}
