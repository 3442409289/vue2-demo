<template>
  <div>
    <div class="upload-container">
      <!-- 固定渲染 maxCount 个空位 -->
      <div
        v-for="(item, index) in slotList"
        :key="index"
        class="upload-slot"
        @click="handleSlotClick(index)"
      >
        <!-- 有图片时显示图片 -->
        <img v-if="item.url" :src="item.url" class="slot-image" />
        <!-- 没有图片时显示加号 -->
        <i v-else class="el-icon-plus slot-placeholder"></i>

        <!-- 删除按钮（有图片时显示） -->
        <span
          v-if="item.url"
          class="slot-delete"
          @click.stop="handleRemove(index)"
        >
          <i class="el-icon-delete"></i>
        </span>
      </div>

      <!-- 隐藏的文件上传 input -->
      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/png,image/gif,image/webp"
        style="display: none"
        @change="handleFileSelected"
      />
    </div>

    <!-- 图片预览对话框 -->
    <el-dialog
      :visible.sync="dialogVisible"
      :modal="true"
      :modal-append-to-body="false"
    >
      <img width="100%" :src="dialogImageUrl" alt="" />
      <el-button @click="handleReplaceImageClick">更换图片</el-button>
    </el-dialog>
  </div>
</template>

<script>
import { Message } from "element-ui";

export default {
  props: {
    // 最大上传数量，默认3
    maxCount: {
      type: Number,
      default: 3,
    },
    // 外部传入的初始图片列表，格式：[{ url: 'xxx' }, ...]
    initialImages: {
      type: Array,
      default: () => [],
    },
    // 自定义上传方法，接收 (file, index) 参数，返回 Promise
    customUpload: {
      type: Function,
      default: null,
    },
    customRemove: {
      type: Function,
      default: null,
    },
  },
  data() {
    return {
      // 固定长度的插槽列表
      slotList: [],
      // 当前点击的插槽索引
      activeSlotIndex: -1,
      dialogImageUrl: "",
      dialogVisible: false,
    };
  },
  watch: {
    // 监听外部传入的初始图片变化
    initialImages: {
      handler(newVal) {
        this.initSlots(newVal);
      },
      immediate: true,
      deep: true,
    },
  },
  methods: {
    // 初始化插槽
    initSlots(images) {
      // 创建固定长度的插槽列表
      this.slotList = [];
      for (let i = 0; i < this.maxCount; i++) {
        if (images && images[i]) {
          this.slotList.push({ url: images[i].url });
        } else {
          this.slotList.push({ url: "" });
        }
      }
    },

    // 点击插槽
    handleSlotClick(index) {
      this.activeSlotIndex = index;

      // 如果该位置已有图片，先弹出确认框
      if (this.slotList[index].url) {
        this.previewImage(this.slotList[index].url);
      } else {
        // 空位直接触发文件选择
        this.$refs.fileInput.click();
      }
    },

    handleReplaceImageClick() {
      this.$refs.fileInput.click();
      this.dialogVisible = false;
    },

    // 文件选择后的处理
    async handleFileSelected(event) {
      const file = event.target.files[0];
      if (!file) return;

      // 检查文件类型
      const validTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
      if (!validTypes.includes(file.type)) {
        Message({
          message: "不支持的文件格式，仅支持 jpeg、png、gif、webp",
          type: "error",
          duration: 3000,
        });
        event.target.value = "";
        return;
      }

      // 检查文件大小（2MB）
      if (file.size / 1024 / 1024 > 2) {
        Message({
          message: "图片大小不能超过 2MB",
          type: "error",
          duration: 3000,
        });
        event.target.value = "";
        return;
      }

      // 执行上传
      await this.doUpload(file);

      // 重置文件输入
      event.target.value = "";
    },

    // 执行上传
    async doUpload(file) {
      if (this.customUpload) {
        try {
          // 传入文件对象和当前索引
          const result = await this.customUpload(file, this.activeSlotIndex);
          const url = result.url || result;

          // 更新指定位置的图片
          this.slotList[this.activeSlotIndex].url = url;

          // 通知父组件
          this.emitUpdate();

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
        const url = URL.createObjectURL(file);
        this.slotList[this.activeSlotIndex].url = url;
        this.emitUpdate();
      }
    },

    // 删除指定位置的图片
    handleRemove(index) {
      this.$confirm("确定要删除这张图片吗？", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning",
      })
        .then(async () => {
          // 清空该位置的图片，保留空位
          if (this.customRemove) {
            try {
              // 传入文件对象和当前索引
              await this.customRemove(index);

              this.slotList[index].url = "";
              this.emitUpdate();

              Message({
                message: "删除成功",
                type: "success",
                duration: 1500,
              });
            } catch (error) {
              Message({
                message: "删除失败：" + (error.message || "未知错误"),
                type: "error",
                duration: 3000,
              });
            }
          } else {
            this.slotList[index].url = "";
            this.emitUpdate();
          }
        })
        .catch(() => {});
    },

    // 通知父组件更新
    emitUpdate() {
      // 提取有图片的项组成数组
      const images = this.slotList
        .filter((item) => item.url)
        .map((item) => ({ url: item.url }));

      this.$emit("update:initialImages", images);
    },

    // 预览图片
    previewImage(url) {
      if (url) {
        this.dialogImageUrl = url;
        this.dialogVisible = true;
      }
    },
  },
};
</script>

<style scoped>
.upload-container {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.upload-slot {
  width: 120px;
  height: 120px;
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: #fafafa;
  transition: border-color 0.3s;
}

.upload-slot:hover {
  border-color: #409eff;
}

.upload-slot:hover .slot-delete {
  display: flex;
}

.slot-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  cursor: pointer;
}

.slot-placeholder {
  font-size: 32px;
  color: #8c939d;
}

.slot-delete {
  position: absolute;
  top: 0;
  right: 0;
  width: 24px;
  height: 24px;
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  display: none;
  align-items: center;
  justify-content: center;
  border-radius: 0 6px 0 6px;
  cursor: pointer;
  z-index: 1;
}

.slot-delete:hover {
  background: rgba(255, 0, 0, 0.7);
}
</style>