import React, { useState, useEffect } from "react";
import "../../css/our_partners.css";

function Our_Partners() {
  return (
    <section className="charities">
      <h2>Partners We’re Proud to Work With</h2>
      <p className="charities_intro">
        We collaborate with agricultural organizations across Morocco to build a 
        more sustainable and connected farming community.
      </p>

      <div className="charity_grid">
        <div className="cont">
          <h3>Green Fields Cooperative</h3>
          <p>
            Supports small-scale farmers by providing access to modern equipment
            and sustainable farming techniques. Working with thousands of farms across
            the Gharb region, they ensure local producers can increase their yield
            and reach new markets with pride and dignity.
          </p>

          <h4>How They Help:</h4>
          <ul>
            <li>Distributes quality seeds and organic fertilizers.</li>
            <li>Runs workshops on modern irrigation systems.</li>
            <li>Empowers local farmers with market insights.</li>
            <li>Builds stronger local food chains.</li>
            <li>Reaches thousands of agricultural households every year.</li>
          </ul>
        </div>

        <div className="cont">
          <h3>Sustainable Agri Network</h3>
          <p>
            Promotes eco-friendly farming practices and water conservation.
            They help farmers implement drip irrigation and soil protection
            strategies to ensure a sustainable future for Moroccan agriculture.
          </p>

          <h4>How They Help:</h4>
          <ul>
            <li>Runs seasonal training on climate-resilient crops.</li>
            <li>Helps farms transition to organic production.</li>
            <li>Engages communities in water-saving initiatives.</li>
          </ul>
        </div>

        <div className="cont">
          <h3>Atlas Farming Alliance</h3>
          <p>
            Connects mountain farmers with regional distributors, ensuring
            fair prices for unique local products like saffron, honey, and nuts.
          </p>

          <h4>Initiatives Include:</h4>
          <ul>
            <li>Fair trade certifications for local cooperatives.</li>
            <li>Logistics support for remote farming areas.</li>
            <li>Promoting biodiversity in the Atlas mountains.</li>
            <li>Encouraging traditional, chemical-free farming.</li>
          </ul>
        </div>

        <div className="cont">
          <h3>AgriTech Morocco</h3>
          <p>
            Focuses on digital transformation in the agricultural sector,
            providing farmers with tools to track weather, soil health, and market prices.
          </p>

          <h4>Initiatives Include:</h4>
          <ul>
            <li>Free soil testing for registered cooperatives.</li>
            <li>Collaborations with innovative agri-tech startups.</li>
            <li>Workshops on precision agriculture.</li>
            <li>Events connecting farmers with tech solutions.</li>
          </ul>
        </div>
      </div>
    </section>
  );
}

export default Our_Partners;
