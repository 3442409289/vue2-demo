// store/index.js
import Vue from "vue";
import Vuex from "vuex";
import auth from "./modules/auth";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    auth,
  },
  state: {
    user: {
      role: "", // 初始角色为空字符串或其他默认值如 'guest'
    },
  },
  mutations: {
    // 用于同步更新用户角色
    SET_USER_ROLE(state, role) {
      state.user.role = role;
    },
  },
  actions: {
    // 可进行异步操作，如从API获取用户角色后提交mutation
    updateUserRole({ commit }, role) {
      commit("SET_USER_ROLE", role);
    },
    // 示例：异步获取用户角色
    async fetchUserRole({ commit }) {
      try {
        // 假设有一个API接口获取用户信息
        const response = await axios.get("/api/user-info");
        const userRole = response.data.role; // 根据后端返回数据结构调整
        commit("SET_USER_ROLE", userRole);
      } catch (error) {
        console.error("获取用户角色失败:", error);
        // 可以根据需要处理错误，例如设置一个默认角色
        commit("SET_USER_ROLE", "guest");
      }
    },
  },
  getters: {
    // 可以定义getters方便获取用户角色或进行派生计算
    userRole: (state) => state.user.role,
    // 例如：检查当前用户是否是管理员
    isAdmin: (state) => state.user.role === "admin",
  },
});
