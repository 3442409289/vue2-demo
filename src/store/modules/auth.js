// store/modules/auth.js
import api from "@/utils/api";
import { Message } from "element-ui"; // 新增导入

const state = {
  token: localStorage.getItem("token") || "",
  user: JSON.parse(localStorage.getItem("user")) || null,
  isAuthenticated: !!localStorage.getItem("token"),
  userPermissions: JSON.parse(localStorage.getItem("userPermissions")) || [], // 初始化为空数组
  tokenCheckIntervalId: null, // 新增，用于保存定时器ID
};

const mutations = {
  SET_TOKEN(state, token) {
    state.token = token;
    state.isAuthenticated = true;
    // 保存到localStorage
    localStorage.setItem("token", token);
  },
  SET_USER_PERMISSIONS(state, permissions) {
    state.userPermissions = permissions;
    // 保存到localStorage
    localStorage.setItem("userPermissions", JSON.stringify(permissions));
  },
  SET_USER(state, user) {
    state.user = user;
    // 保存到localStorage
    localStorage.setItem("user", JSON.stringify(user));
  },
  LOGOUT(state) {
    state.token = "";
    state.user = null;
    state.isAuthenticated = false;
    state.userPermissions = [];
  },
  SET_TOKEN_CHECK_INTERVAL_ID(state, intervalId) {
    state.tokenCheckIntervalId = intervalId;
  },
  CLEAR_TOKEN_CHECK_INTERVAL(state) {
    if (state.tokenCheckIntervalId) {
      clearInterval(state.tokenCheckIntervalId);
      state.tokenCheckIntervalId = null;
    }
  },
};

function safeParseLocalStorage(key, defaultValue = null) {
  const item = localStorage.getItem(key);
  // 检查是否为 null 或空字符串
  if (item === null || item.trim() === "") {
    return defaultValue;
  }
  try {
    return JSON.parse(item);
  } catch (error) {
    console.error(`解析 localStorage 项 "${key}" 时出错:`, error);
    // 可选：移除损坏的数据以避免后续错误
    localStorage.removeItem(key);
    return defaultValue;
  }
}

const actions = {
  async login({ commit, dispatch }, credentials) {
    try {
      const response = await api.post("/login/index.php", credentials);

      const { token, user } = response.data;

      // 提交mutation
      commit("SET_TOKEN", token);
      commit("SET_USER", user);
      commit("SET_USER_PERMISSIONS", user.permissions); // 提交权限

      Message({
        message: "登陆成功",
        type: "success",
        duration: 5000, // 可以自定义显示时长
      });

      return Promise.resolve(response);
    } catch (error) {
      return Promise.reject(error);
    }
  },

  logout({ commit, dispatch }) {
    dispatch("stopTokenCheck"); // 停止轮询

    // 清除localStorage
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    localStorage.removeItem("userPermissions");

    // 提交mutation
    commit("LOGOUT");
  },

  initializeAuth({ commit, dispatch }) {
    const token = localStorage.getItem("token");
    const user = safeParseLocalStorage("user", null);
    const permissions = safeParseLocalStorage("userPermissions", []);

    if (token) {
      commit("SET_TOKEN", token);
      commit("SET_USER", user);
      commit("SET_USER_PERMISSIONS", permissions); // 设置权限

      // 初始化时如果存在token，也启动检查
      dispatch("startTokenCheck");
    }
  },

  async startTokenCheck({ commit, state, dispatch }) {
    // 先停止可能存在的旧定时器
    dispatch("stopTokenCheck");

    const CHECK_INTERVAL = 60000; // 每60秒检查一次，可根据需要调整

    // 调用你的验证接口
    await api.post("/auth/auth.php");

    const intervalId = setInterval(async () => {
      if (!state.token) return; // 如果没有token，则不需要检查

      try {
        // 调用你的验证接口
        await api.post("/auth/auth.php");
      } catch (error) {}
    }, CHECK_INTERVAL);

    commit("SET_TOKEN_CHECK_INTERVAL_ID", intervalId);
  },

  // 停止Token检查轮询
  stopTokenCheck({ commit, state }) {
    if (state.tokenCheckIntervalId) {
      clearInterval(state.tokenCheckIntervalId);
      commit("CLEAR_TOKEN_CHECK_INTERVAL");
    }
  },
};

const getters = {
  isAuthenticated: (state) => state.isAuthenticated,
  currentUser: (state) => state.user,
  authToken: (state) => state.token,
  hasPermission: (state) => (requiredPermission) => {
    if (state.userPermissions.includes("*")) {
      return true;
    }
    return state.userPermissions.includes(requiredPermission);
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
