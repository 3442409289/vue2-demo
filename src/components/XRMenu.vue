<template>
  <div :class="['menu']">
    <div class="menu_button" @click="$emit('active')">
      <el-popover
        placement="bottom"
        :title="UserInfo !== undefined ? UserInfo.username : '未登录'"
        width="200"
        trigger="click"
        :close-delay="800"
        :visible-arrow="false"
        popper-class="popover-content"
        transition="el-zoom-in-top"
      >
        <!-- 动态内容区域 -->
        <template slot:content>
          <div>
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

        <el-link
          :underline="false"
          slot="reference"
          style="color: var(--text-color); font-size: 16px"
          >{{ UserInfo !== undefined ? "用户" : "未登录" }}</el-link
        >
      </el-popover>
    </div>
    <div
      class="box"
      :style="{ height: `${isExpand ? height : defaultHeight}px` }"
    >
      <slot name="box"></slot>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import ItemEx from "@/components/ItemEx";
import Store from "@/store"; // 确保路径正确
export default {
  components: {
    ItemEx,
  },
  props: {
    height: {
      type: Number,
      required: true,
    },
    defaultHeight: {
      type: Number,
      default: 0,
    },
    isExpand: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      UserInfo: undefined,
    };
  },
  created() {
    this.Init();
  },
  methods: {
    ...mapActions("auth", ["logout"]),
    Init() {
      const isAuthenticated = Store.getters["auth/isAuthenticated"];
      if (isAuthenticated) {
        this.UserInfo = Store.getters["auth/currentUser"];
      } else {
        this.UserInfo = undefined;
      }
    },
    showLogin() {
      this.$router.push({ name: "Login" });
    },
    showProfile() {
      this.$router.push({ name: "User" });
    },
    async showLogout() {
      await this.logout();
      this.$router.go(0);
    },
  },
};
</script>

<style>
.popover-btn,
.login-btn {
  font-family: var(--Current-Font);
}
.menu {
  width: 100%;
  z-index: 2;
}
.menu_button {
  transition: 0.1s;
  background: var(--bg-color5);
  overflow: hidden;
}
.menu_button:hover {
  cursor: pointer;
}
.box {
  width: 100%;
  transition: var(--time2A);
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}
.popover-content {
  background: var(--bg-color4A);
  border: 1px solid #525252;
}
.popover-content .el-popover__title {
  color: var(--text-color);
}
@media screen and (min-width: 769px) {
  .menu_button {
    height: 5px;
  }
  .menu_button:hover {
    height: 30px;
    line-height: 30px;
  }
}
@media screen and (max-width: 768px) {
  .menu_button {
    background: var(--bg-color5A);
    height: 30px;
    line-height: 30px;
  }
}
</style>