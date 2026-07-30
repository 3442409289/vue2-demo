<template>
  <div class="loading-spinner">
    <div class="spinner"></div>
    <p v-if="text">{{ text }}</p>
  </div>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
    },
    size: {
      type: Number,
      default: 50, // 默认大小（px）
    },
    color: {
      type: String,
      default: "var(--color2B)", // 默认颜色
    },
  },
};
</script>

<style scoped>
.loading-spinner {
  position: fixed;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}
.spinner {
  width: v-bind('size + "px"');
  height: v-bind('size + "px"');
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top-color: v-bind(color);
  animation: spin 1s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>