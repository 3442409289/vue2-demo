<template>
  <div>
    <div v-if="UserInfo !== undefined">
      <el-descriptions title="用户信息" :column="1" size="small" :colon="false">
        <el-descriptions-item>
          <Loading v-if="loading" style="position: relative" />
          <UploadImageEx
            v-else
            :initialImage="ProfilePicture"
            :customUpload="uploadProfilePicture"
          />
        </el-descriptions-item>
        <el-descriptions-item label="用户ID" contentStyle="">{{
          UserInfo.id
        }}</el-descriptions-item>
        <el-descriptions-item label="用户名">{{
          UserInfo.username
        }}</el-descriptions-item>
        <el-descriptions-item label="邮箱">{{
          UserInfo.email
        }}</el-descriptions-item>
        <el-descriptions-item label="备注">
          <el-tag size="small" v-for="(item, i) in mappedString" :key="i">{{
            item
          }}</el-tag>
          <!-- 管理员专属按钮 -->
          <el-button
            v-if="isAdmin"
            size="small"
            type="danger"
            @click="$router.push({ name: 'Root' })"
          >
            管理员后台
          </el-button>
        </el-descriptions-item>
        <el-descriptions-item>
          <Loading v-if="loading" style="position: relative" />
          <UploadImageListExVue
            v-else
            :initialImages="PhotoWall"
            :customUpload="uploadPhotoWall"
            :customRemove="RemovePhotoWall"
          />
        </el-descriptions-item>
      </el-descriptions>
    </div>
    <div v-else>加载中...</div>
  </div>
</template>

<script>
import UploadImageEx from "@/components/UploadImageEx.vue";
import UploadImageListExVue from "@/components/UploadImageListEx.vue";
import Loading from "@/components/Loading.vue";
import Store from "@/store"; // 确保路径正确
import api from "@/utils/api";
export default {
  components: {
    UploadImageEx,
    UploadImageListExVue,
    Loading,
  },
  data() {
    return {
      UserInfo: undefined,
      mappingRules: {
        user: "普通用户",
        "*": "超级管理员",
      },
      ProfilePicture: "",
      PhotoWall: [],
      loading: true,
    };
  },
  computed: {
    // 计算属性会根据 originalString 的变化自动更新
    mappedString() {
      if (this.UserInfo !== undefined) {
        const arr = [];
        const permissions = JSON.parse(this.UserInfo.permissions);
        for (let index = 0; index < permissions.length; index++) {
          arr.push(this.mappingRules[permissions[index]]);
        }
        return arr;
      }
    },
    isAdmin() {
      if (this.UserInfo !== undefined) {
        const permissions = JSON.parse(this.UserInfo.permissions);
        for (let index = 0; index < permissions.length; index++) {
          if (permissions[index].includes("*")) {
            return true;
          }
        }
      }
      return false;
    },
  },
  async created() {
    this.Init();
    this.LoadData();
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
    async LoadData() {
      this.loading = true;

      const response1 = await api.post("/mysql/index.php", {
        mode: "Picture",
        type: 0,
        user_id: this.UserInfo.id,
      });
      const response2 = await api.post("/mysql/index.php", {
        mode: "PhotoWall",
        type: 0,
        user_id: this.UserInfo.id,
      });
      this.ProfilePicture = response1.data.image_data;
      this.PhotoWall.push({ url: response2.data.image_data1 });
      this.PhotoWall.push({ url: response2.data.image_data2 });
      this.PhotoWall.push({ url: response2.data.image_data3 });

      this.loading = false;
    },
    async uploadProfilePicture(file) {
      const base64Data = await this.fileToBase64(file);
      // 例如上传到阿里云OSS
      const response = await api.post("/mysql/index.php", {
        mode: "Picture",
        type: 1,
        user_id: this.UserInfo.id,
        data: {
          image_data: base64Data, // 直接把 Base64 字符串传给后端
        },
      });
      return base64Data; // 返回图片URL
    },
    async uploadPhotoWall(file, index) {
      const base64Data = await this.fileToBase64(file);
      // 例如上传到阿里云OSS
      const response = await api.post("/mysql/index.php", {
        mode: "PhotoWall",
        type: 1,
        user_id: this.UserInfo.id,
        data: {
          index: index,
          image_data: base64Data, // 直接把 Base64 字符串传给后端
        },
      });
      return base64Data; // 返回图片URL
    },
    async RemovePhotoWall(index) {
      const response = await api.post("/mysql/index.php", {
        mode: "PhotoWall",
        type: 1,
        user_id: this.UserInfo.id,
        data: {
          index: index,
          image_data: null, // 写NULL删除
        },
      });
    },
    fileToBase64(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file); // 读取文件为 DataURL（即 Base64 格式）
        reader.onload = () => resolve(reader.result); // 读取成功，返回 Base64 字符串
        reader.onerror = (error) => reject(error); // 读取失败
      });
    },
  },
};
</script>

<style>
</style>