// store/modules/auth.js
import api from "@/utils/api";
import { Message } from "element-ui"; // 新增导入

const state = {
  token: sessionStorage.getItem("token") || localStorage.getItem("token") || "",
  user: JSON.parse(
    sessionStorage.getItem("user") || localStorage.getItem("user") || "null"
  ),
  isAuthenticated: !!(
    sessionStorage.getItem("token") || localStorage.getItem("token")
  ),
  userPermissions: JSON.parse(
    sessionStorage.getItem("userPermissions") ||
      localStorage.getItem("userPermissions") ||
      "[]"
  ),
  tokenCheckIntervalId: null,
};

const mutations = {
  SET_TOKEN(state, token) {
    state.token = token;
    state.isAuthenticated = true;
  },
  SET_USER_PERMISSIONS(state, permissions) {
    state.userPermissions = permissions;
  },
  SET_USER(state, user) {
    state.user = user;
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

function safeParseStorage(storage, key, defaultValue = null) {
  try {
    const item = storage.getItem(key);
    if (item === null || item.trim() === "") {
      return defaultValue; // 返回 null 而不是 defaultValue
    }
    return JSON.parse(item);
  } catch (error) {
    console.error(`解析 ${key} 时出错:`, error);
    storage.removeItem(key);
    return defaultValue; // 返回 null 而不是 defaultValue
  }
}

const actions = {
  async login({ commit, dispatch }, credentials) {
    try {
      const response = await api.post("/login/index.php", credentials);
      const { token, user } = response.data;

      // 根据 rememberMe 决定存储方式
      if (credentials.rememberMe) {
        // 记住我 → 存 localStorage（关闭浏览器不清除）
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));
        localStorage.setItem(
          "userPermissions",
          JSON.stringify(user.permissions)
        );
      } else {
        // 不记住 → 存 sessionStorage（关闭标签页/浏览器就清除）
        sessionStorage.setItem("token", token);
        sessionStorage.setItem("user", JSON.stringify(user));
        sessionStorage.setItem(
          "userPermissions",
          JSON.stringify(user.permissions)
        );
      }

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

    // 清除所有存储
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    localStorage.removeItem("userPermissions");
    sessionStorage.removeItem("token");
    sessionStorage.removeItem("user");
    sessionStorage.removeItem("userPermissions");

    // 提交mutation
    commit("LOGOUT");
  },

  initializeAuth({ commit, dispatch }) {
    const token =
      sessionStorage.getItem("token") || localStorage.getItem("token");

    const user =
      safeParseStorage(sessionStorage, "user", null) ??
      safeParseStorage(localStorage, "user", null);

    const permissions =
      safeParseStorage(sessionStorage, "userPermissions", null) ??
      safeParseStorage(localStorage, "userPermissions", []) ??
      [];

    if (token) {
      commit("SET_TOKEN", token);
      commit("SET_USER", user);
      commit("SET_USER_PERMISSIONS", permissions);
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
