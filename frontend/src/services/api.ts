import axios from "axios";

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000",
  headers: {
    Accept: "application/json",
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");

  if (token) {
    if (!config.headers) {
      config.headers = {};
    }
    const authValue = `Bearer ${token}`;
    (config.headers as any).Authorization = authValue;
    (config.headers as any)["Authorization"] = authValue;
    if (typeof (config.headers as any).set === "function") {
      (config.headers as any).set("Authorization", authValue);
    }
  }

  return config;
});

export default api;
