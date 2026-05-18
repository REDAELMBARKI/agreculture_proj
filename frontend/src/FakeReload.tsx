import { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";

interface FakeReloadProps {
  children: React.ReactNode;
}

export default function FakeReload({ children }: FakeReloadProps) {
  const location = useLocation();
  const [isReloading, setIsReloading] = useState(false);
  const [displayLocation, setDisplayLocation] = useState(location);

  useEffect(() => {
    if (location.pathname !== displayLocation.pathname || location.search !== displayLocation.search) {
      setIsReloading(true);
      
      const timer = setTimeout(() => {
        setDisplayLocation(location);
        setIsReloading(false);
      }, 600);

      return () => clearTimeout(timer);
    }
  }, [location, displayLocation]);

  if (isReloading) {
    return (
      <div
        style={{
          position: "fixed",
          inset: 0,
          backgroundColor: "#ffffff",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          zIndex: 99999,
        }}
      >
        <div
          style={{
            width: "48px",
            height: "48px",
            border: "4px solid #f3f3f3",
            borderTop: "4px solid #1A4D2E",
            borderRadius: "50%",
            animation: "spin 1s linear infinite",
          }}
        />
        <style>{`
          @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
        `}</style>
      </div>
    );
  }

  return <>{children}</>;
}
