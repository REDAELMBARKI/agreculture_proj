import { Routes, Route, useLocation } from "react-router-dom";
import React, { useEffect, Suspense, lazy } from "react";
import axios from "axios";

// Headers & Footer
import Header from "./assets/components/Header.jsx";
import Header_alt from "./assets/components/Header_alt.jsx";
import Footer from "./assets/components/Footer.jsx";

// lazy-load all page components
const Home = lazy(() => import("./assets/components/Home"));
const Sign_up = lazy(() => import("./assets/components/Sign_up"));
const Login = lazy(() => import("./assets/components/Login"));
const FAQ = lazy(() => import("./assets/components/FAQ"));
const FAQChatBot = lazy(() => import("./assets/components/FAQChatBot"));
const Marketplace = lazy(() => import("./assets/components/Marketplace"));
const Product_Details = lazy(() => import("./assets/components/Product_Details"));
const ConversationsList = lazy(() => import("./assets/components/ConversationsList"));
const ChatPage = lazy(() => import("./assets/components/ChatPage"));

// Admin
const Admin_Inventory = lazy(() => import("./assets/components/Admin/Admin_Inventory"));
const Admin_Dashboard = lazy(() => import("./assets/components/Admin/Admin_Dashboard"));
const Data_Reports = lazy(() => import("./assets/components/Admin/Data_Reports"));
const View_Users = lazy(() => import("./assets/components/Admin/View_Users"));

// User
const User_Dashboard = lazy(
  () => import("./assets/components/User/User_Dashboard"),
);
const My_Profile = lazy(
  () => import("./assets/components/User/My_Profile"),
);
const My_Announcements = lazy(
  () => import("./assets/components/User/My_Announcements"),
);
const Add_Announcement = lazy(
  () => import("./assets/components/User/Add_Announcement"),
);

// Footer content pages
const Terms_Conditions = lazy(
  () => import("./assets/components/Footer_Content/Terms_Conditions"),
);
const Privacy_Policy = lazy(
  () => import("./assets/components/Footer_Content/Privacy_Policy"),
);
const Cookie_Policy = lazy(
  () => import("./assets/components/Footer_Content/Cookie_Policy"),
);
const Accessibility = lazy(
  () => import("./assets/components/Footer_Content/Accessibility"),
);


// Not found page
const NotFound = lazy(() => import("./404.jsx"));

export default function Layout() {
  const location = useLocation();
  const path = location.pathname.toLowerCase();

  // Configure axios with token
  useEffect(() => {
    const updateAxiosToken = () => {
      const token = localStorage.getItem('token');
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      } else {
        delete axios.defaults.headers.common['Authorization'];
      }
    };

    updateAxiosToken();
    window.addEventListener('auth-change', updateAxiosToken);

    const authInterceptor = axios.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response && error.response.status === 401) {
          // If token is expired or invalid, log out user
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          localStorage.removeItem('role');
          window.dispatchEvent(new Event('auth-change'));
        }
        return Promise.reject(error);
      }
    );

    return () => {
       axios.interceptors.response.eject(authInterceptor);
       window.removeEventListener('auth-change', updateAxiosToken);
     };
   }, []);

  console.log("Current path:", location);
  // Paths without a header/footer
  const noHeaderFooterPaths = ["/login", "/sign_up"];

  // Paths that use the alternative header
  const altHeaderPaths = [
    "/user_dashboard",
    "/admin_dashboard",
    "/view_users",
    "/data_reports",
    "/my_profile",
    "/add_announcement",
    "/admin_inventory",
    "/my_announcements",
  ];

  const hideHeaderFooter = noHeaderFooterPaths.includes(path);
  const useAltHeader = altHeaderPaths.includes(path);

  useEffect(() => {
    console.log("Current path:", path);
  }, [path]);

  return (
    <>
      {/* Header */}
      {!hideHeaderFooter &&
        (useAltHeader ? <Header_alt size="small" /> : <Header />)}

      {/* Suspense wrapper for lazy-loaded routes */}
      <Suspense fallback={<div>Loading...</div>}>
        <Routes>
          {/* Main pages */}
          <Route path="/" element={<Home />} />
          <Route path="/sign_up" element={<Sign_up />} />
          <Route path="/login" element={<Login />} />
          <Route path="/faq" element={<FAQ />} />
          <Route path="/faq_chatbot" element={<FAQChatBot />} />
          <Route path="/announcements" element={<Marketplace />} />
          <Route path="/announcements/:announcementSlug" element={<Product_Details />} />
          <Route path="/chat" element={<ChatPage />} />
          <Route path="/chat/:conversationSlug" element={<ChatPage />} />
          {/* Admin */}
          <Route path="/admin_dashboard" element={<Admin_Dashboard />} />
          <Route path="/data_reports" element={<Data_Reports />} />
          <Route path="/view_users" element={<View_Users />} />
          <Route path="/admin_inventory" element={<Admin_Inventory />} />

          {/* User */}
          <Route path="/user_dashboard" element={<User_Dashboard />} />
          <Route path="/my_profile" element={<My_Profile />} />
          <Route path="/my_announcements" element={<My_Announcements />} />
          <Route path="/add_announcement" element={<Add_Announcement />} />
          <Route path="/users/:userSlug/announcements/:announcementSlug" element={<Add_Announcement />} />

          {/* Footer items */}
          <Route path="/terms_conditions" element={<Terms_Conditions />} />
          <Route path="/privacy_policy" element={<Privacy_Policy />} />
          <Route path="/cookie_policy" element={<Cookie_Policy />} />
          <Route path="/accessibility" element={<Accessibility />} />

          {/* Catch all */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </Suspense>

      {/* Footer */}
      {!hideHeaderFooter && <Footer />}
    </>
  );
}
