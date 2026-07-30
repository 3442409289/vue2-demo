<template>
  <div>
    <div v-if="config !== undefined">
      <el-table :data="config" style="width: 100%">
        <el-table-column prop="id" label="id">
          <template slot-scope="id">
            <el-input
              v-model="id.row[id.column.property]"
              placeholder="id"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="username" label="username">
          <template slot-scope="username">
            <el-input
              v-model="username.row[username.column.property]"
              placeholder="username"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="email" label="email">
          <template slot-scope="email">
            <el-input
              v-model="email.row[email.column.property]"
              placeholder="email"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="password" label="password">
          <template slot-scope="password">
            <el-input
              v-model="password.row[password.column.property]"
              placeholder="password"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="permissions" label="permissions">
          <template slot-scope="permissions">
            <el-input
              v-model="permissions.row[permissions.column.property]"
              placeholder="permissions"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="created_at">
          <template slot-scope="created_at">
            <el-input
              v-model="created_at.row[created_at.column.property]"
              placeholder="created_at"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="操作" width="100">
          <template slot-scope="scope">
            <el-button
              @click.native.prevent="active()"
              type="text"
              size="small"
            >
              修改
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <Loading v-else style="position: relative" />
    <el-input
      v-model="user_id"
      @keyup.enter.native="LoadData()"
      placeholder="user_id"
    ></el-input>
  </div>
</template>

<script>
import List from "./NavigationManager/List";
import Loading from "@/components/Loading";
import api from "@/utils/api";
import Store from "@/store"; // 确保路径正确
export default {
  components: {
    List,
    Loading,
  },
  data() {
    return {
      UserInfo: undefined,
      config: undefined,
      user_id: 1,
    };
  },
  created() {
    this.Init();
  },
  methods: {
    Init() {
      const isAuthenticated = Store.getters["auth/isAuthenticated"];
      if (isAuthenticated) {
        this.UserInfo = Store.getters["auth/currentUser"];
      } else {
        this.UserInfo = undefined;
      }
    },
    async active() {
      const response = await api.post("/mysql/index.php", {
        mode: "User",
        type: 1,
        id: this.UserInfo.id,
        data: this.config[0],
      });
      if (!response.data.code) {
        this.$toast({ message: "保存数据成功" });
      }
    },
    async LoadData() {
      const response = await api.post("/mysql/index.php", {
        mode: "User",
        type: 0,
        id: this.user_id,
      });
      this.config = Array(response.data);
    },
  },
};
</script>

<style>
</style>