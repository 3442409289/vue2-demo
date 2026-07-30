<template>
  <div>
    <el-upload
      class="avatar-uploader"
      action=""
      :show-file-list="false"
      :before-upload="handleBeforeUpload"
      :on-change="handleChange"
      :auto-upload="false"
    >
      <img
        v-if="currentImageUrl"
        :src="currentImageUrl"
        class="avatar"
        @click.prevent="handleImageClick"
      />
      <i v-else class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
    <!-- 图片预览对话框 -->
    <el-dialog
      :visible.sync="dialogVisible"
      :modal="true"
      :modal-append-to-body="false"
    >
      <img width="100%" :src="currentImageUrl" alt="" />
      <el-button @click="handleReplaceImageClick">更换图片</el-button>
    </el-dialog>
  </div>
</template>

<script>
import { Message, MessageBox } from "element-ui";

export default {
  props: {
    // 外部传入的初始图片地址
    initialImage: {
      type: String,
      default: "",
    },
    // 自定义上传方法，接收 file 参数，返回 Promise<url>
    customUpload: {
      type: Function,
      default: null,
    },
    // 文件大小限制，单位 MB，默认2MB
    maxSize: {
      type: Number,
      default: 2,
    },
    // 允许的文件类型，默认图片格式
    acceptTypes: {
      type: Array,
      default: () => ["image/jpeg", "image/png", "image/gif", "image/webp"],
    },
    // 提示语模板，用于更换确认弹窗
    confirmTitle: {
      type: String,
      default: "提示",
    },
    confirmMessage: {
      type: String,
      default: "当前已有一张图片，是否要更换？",
    },
    confirmButtonText: {
      type: String,
      default: "更换",
    },
    cancelButtonText: {
      type: String,
      default: "取消",
    },
  },
  data() {
    return {
      // 内部维护的当前图片地址
      currentImageUrl: "",
      // 标记是否正在等待用户确认更换
      waitingForConfirm: false,
      // 暂存待上传的文件
      pendingFile: null,
      dialogVisible: false,
    };
  },
  watch: {
    // 监听外部传入的初始图片
    initialImage: {
      handler(newVal) {
        if (newVal) {
          this.currentImageUrl = newVal;
        }
      },
      immediate: true,
    },
  },
  methods: {
    // 点击图片时的处理
    handleImageClick(event) {
      event.stopPropagation();

      if (this.currentImageUrl) {
        this.previewImage();
      }
    },

    handleReplaceImageClick() {
      //用户确认更换，触发文件选择
      this.waitingForConfirm = true;
      // 触发 el-upload 的点击选择文件
      this.$el.querySelector(".el-upload__input").click();
      this.dialogVisible = false;
    },

    // 文件选择后的处理（无论是否通过校验都会触发）
    handleChange(file, fileList) {
      if (!this.handleBeforeUpload(file.raw)) {
        return;
      }

      // 如果是等待确认更换状态，且文件有效
      if (this.waitingForConfirm && file.status !== "ready") {
        this.waitingForConfirm = false;
        return;
      }

      if (file.status === "ready") {
        this.pendingFile = file;
        // 执行上传
        this.doUpload(file);
      }
    },
    // 上传前校验
    handleBeforeUpload(file) {
      // 检查文件类型
      const isValidType = this.acceptTypes.includes(file.type);
      if (!isValidType) {
        Message({
          message: `不支持的文件格式，仅支持：${this.acceptTypes.join(", ")}`,
          type: "error",
          duration: 3000,
        });
        return false;
      }

      // 检查文件大小
      const isLtMaxSize = file.size / 1024 / 1024 > this.maxSize;
      if (isLtMaxSize) {
        Message({
          message: `文件大小不能超过 ${this.maxSize}MB!`,
          type: "error",
          duration: 3000,
        });
        return false;
      }

      return true;
    },
    // 执行上传
    async doUpload(file) {
      // 如果有自定义上传方法
      if (this.customUpload) {
        try {
          const result = await this.customUpload(file.raw);
          const url = typeof result === "string" ? result : result.url;
          this.currentImageUrl = url;
          // 通知父组件图片已更新
          this.$emit("update:initialImage", url);
          this.$emit("image-changed", url);
          Message({
            message: "上传成功",
            type: "success",
            duration: 1500,
          });
        } catch (error) {
          Message({
            message: "上传失败：" + (error.message || "未知错误"),
            type: "error",
            duration: 3000,
          });
        }
      } else {
        // 没有自定义上传方法，使用本地预览
        this.currentImageUrl = URL.createObjectURL(file.raw);
        this.$emit("update:initialImage", this.currentImageUrl);
        this.$emit("image-changed", this.currentImageUrl);
      }

      this.waitingForConfirm = false;
      this.pendingFile = null;
    },
    // 清除当前图片
    clearImage() {
      this.currentImageUrl = "";
      this.$emit("update:initialImage", "");
      this.$emit("image-cleared");
    },
    // 预览图片
    previewImage() {
      if (this.currentImageUrl) {
        this.dialogVisible = true;
      }
    },
  },
};
</script>

<style scoped>
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 148px;
  height: 148px;
  line-height: 148px;
  text-align: center;
}
.avatar {
  width: 146px;
  height: 146px;
  display: block;
  object-fit: cover;
}
</style>