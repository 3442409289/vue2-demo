// utils/api.js
import axios from "axios";
import store from "@/store";
import { Message } from "element-ui"; // 新增导入

const api = axios.create({
  baseURL: process.env.VUE_APP_API_BASE_URL || "/api",
  timeout: 10000,
});

// 请求拦截器
api.interceptors.request.use(
  (config) => {
    const token = store.getters["auth/authToken"];
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// 响应拦截器
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // token过期或无效，清除登录状态
      store.dispatch("auth/logout");
      Message({
        message: error.response?.data?.error || "Token检查失败",
        type: "warning",
        duration: 5000, // 可以自定义显示时长
      });
    }
    return Promise.reject(error);
  }
);

export default api;
