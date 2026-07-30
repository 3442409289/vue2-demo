<template>
  <div class="Profile">
    <el-container style="height: 100%">
      <el-aside
        class="aside"
        width="200px"
        style="background-color: rgb(238, 241, 246)"
      >
        <el-menu :default-active="$route.path" router>
          <el-submenu index="1">
            <template slot="title"
              ><i class="el-icon-message"></i>账户</template
            >
            <el-menu-item-group>
              <template slot="title">用户信息</template>
              <el-menu-item index="/Profile/User">账号</el-menu-item>
              <el-menu-item index="/Profile/Articles">文章</el-menu-item>
              <el-menu-item @click.native="UserLogout">退出登录</el-menu-item>
            </el-menu-item-group>
          </el-submenu>
        </el-menu>
      </el-aside>

      <el-container>
        <el-main>
          <router-view></router-view>
        </el-main>
      </el-container>
    </el-container>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  methods: {
    ...mapActions("auth", ["logout"]),
    async UserLogout() {
      await this.logout();
      this.$router.go(0);
    },
  },
};
</script>

<style>
/* 全局样式 */
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f5f5f5;
}

.Profile {
  overflow: auto;
  height: 100%;
  cursor: auto;
}

.aside {
  transition: 0.5s;
  margin-left: -150px;
}

.aside:hover {
  margin-left: 0px;
}
</style>