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
          <h4>What is AgriMarket?</h4>
          <p>
            AgriMarket is a local marketplace where farmers can sell or trade fresh crops, livestock,
            and agricultural equipment directly to buyers.
          </p>
        </div>

        <div className="cont">
          <h4>How do I list an item?</h4>
          <p>
            Simply create an account, click on 'Add Announcement', and fill in the details about your
            agricultural product.
          </p>
        </div>

        <div className="cont">
          <h4>Is AgriMarket free to use?</h4>
          <p>
            Yes, listing and browsing items on AgriMarket is completely free for all users.
          </p>
        </div>
      </div>
    </div>
  );
}

export default FAQ;
