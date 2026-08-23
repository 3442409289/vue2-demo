<template>
  <div class="menu">
    <div class="menu-item">
      <slot></slot>
    </div>
    <div class="menu-item">
      <Container :title="UserInfo !== undefined ? UserInfo.username : '未登录'">
        <template v-slot:main>
          <div style="min-width: 150px">
            <!-- 登录状态显示 -->
            <template v-if="UserInfo">
              <el-button
                type="text"
                icon="el-icon-user"
                @click="showProfile"
                class="popover-btn"
              >
                个人中心
              </el-button>
              <el-button
                type="text"
                icon="el-icon-switch-button"
                @click="showLogout"
                class="popover-btn"
              >
                退出登录
              </el-button>
            </template>

            <!-- 未登录状态显示 -->
            <template v-else>
              <ItemEx @click="showLogin">立即登录</ItemEx>
            </template>
          </div>
        </template>
        <template v-slot:button>
          <ItemEx class="text">{{
            UserInfo !== undefined ? "用户" : "登录"
          }}</ItemEx>
        </template>
      </Container>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import Store from "@/store"; // 确保路径正确
import ItemEx from "@/components/ItemEx";
import Container from "@/components/Container";
import { Message } from "element-ui"; // 新增导入
export default {
  components: {
    ItemEx,
    Container,
  },
  data() {
    return {};
  },
  created() {},
  computed: {
    UserInfo() {
      const isAuthenticated = Store.getters["auth/isAuthenticated"];
      if (isAuthenticated) {
        return Store.getters["auth/currentUser"];
      }
      return undefined;
    },
  },
  methods: {
    ...mapActions("auth", ["logout"]),
    showLogin() {
      if (this.$route.name !== "Login") {
        this.$router.push({
          name: "Login",
          query: { redirect: this.$route.fullPath },
        });
      } else {
        Message({
          message: "您已在登录页面",
          type: "warning",
          duration: 3000, // 可以自定义显示时长
        });
      }
    },
    showProfile() {
      this.$router.push({ name: "User" });
    },
    async showLogout() {
      await this.logout();
    },
  },
};
</script>

<style scoped>
.menu {
  width: 100%;
  height: 36px;
  display: flex;
  z-index: 2;
  background: var(--bg-color4A);
  align-items: center;
  justify-content: space-between;
  overflow-x: auto;
  gap: 10px;
}
.menu .menu-item {
  display: flex;
}
.text {
  white-space: nowrap;
}
</style>