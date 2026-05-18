import "../../css/faq.css";
import React, { Suspense } from "react";

function FAQ() {
  const FAQChatBot = React.lazy(() => import("./FAQChatBot"));

  return (
    <div>
      <div className="faq">
        <h3>Frequently Asked Questions</h3>

        <Suspense fallback={<div>Loading Chat...</div>}>
          <FAQChatBot />
        </Suspense>

        <div className="cont">
          <h4>What is TinyTrove Marketplace?</h4>
          <p>
            TinyTrove is a platform where users buy and sell pre-loved items. List what you no longer
            need, set a price, and connect with local buyers by phone.
          </p>
        </div>

        <div className="cont">
          <h4>How do I create an announcement?</h4>
          <h5>Follow these simple steps:</h5>
          <ul>
            <li>1: Login or create a free account</li>
            <li>2: Click Publish and fill in your item details</li>
            <li>3: Add photos and set your price</li>
            <li>4: Choose pickup location and contact preferences</li>
            <li>5: Publish your announcement</li>
          </ul>
        </div>

        <div className="cont">
          <h4>What can I sell?</h4>
          <ul>
            <li>Clothing and accessories for all ages</li>
            <li>Electronics and gadgets</li>
            <li>Home furniture and decor</li>
            <li>Books and educational materials</li>
            <li>Sports equipment</li>
            <li>And much more</li>
          </ul>
        </div>

        <div className="cont">
          <h4>How do transactions work?</h4>
          <p>
            Buyers and sellers arrange pickup or delivery directly through the platform. Use secure
            messaging or phone contact to coordinate the exchange.
          </p>
        </div>

        <div className="cont">
          <h4>Is the marketplace safe to use?</h4>
          <p>
            Yes. We prioritize user safety with ratings, secure messaging, and verification options.
            Meet in safe public locations and inspect items before completing transactions.
          </p>
        </div>

        <div className="cont">
          <h4>Are there any fees?</h4>
          <p>
            Creating announcements and browsing is free. A small commission may apply on successful
            sales only.
          </p>
        </div>
      </div>
    </div>
  );
}

export default FAQ;
