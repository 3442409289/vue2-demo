<template>
  <div>
    <el-upload
      action=""
      list-type="picture-card"
      :on-preview="handlePictureCardPreview"
      :on-remove="handleRemove"
      :file-list="list"
      :limit="3"
      :on-exceed="handleLimit"
    >
      <i class="el-icon-plus"></i>
    </el-upload>
    <el-dialog :visible.sync="dialogVisible">
      <img width="100%" :src="dialogImageUrl" alt="" />
    </el-dialog>
  </div>
</template>

<script>
import { Message } from "element-ui"; // 新增导入
export default {
  data() {
    return {
      list: [],
      dialogImageUrl: "",
      dialogVisible: false,
    };
  },
  methods: {
    handleRemove(file, fileList) {
      console.log(file, fileList);
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
      console.log(this.list);
    },
    handleLimit(files, fileList) {
      Message({
        message: files[0].name + "上传失败，最多上传3张图片",
        type: "warning",
        duration: 5000, // 可以自定义显示时长
      });
    },
  },
};
</script>

<style>
</style>