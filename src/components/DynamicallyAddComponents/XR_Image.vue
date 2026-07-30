<template>
  <div class="image-box">
    <img class="image" :src="data" />
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped>
.image-box {
  display: flex;
  justify-content: center; /* 水平居中 */
  align-items: center; /* 垂直居中 */
  width: 100%; /* 容器宽度 */
  margin-bottom: 20px;
}

.image-box .image {
  max-width: 100%; /* 图片最大宽度为容器宽度 */
  max-height: 100%; /* 图片最大高度为容器高度 */
  object-fit: contain; /* 确保整个图片完整显示在容器内 */
  border-radius: 13px;
}
</style>