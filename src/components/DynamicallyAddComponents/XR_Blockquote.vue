<template>
  <blockquote
    :style="{ color: `${color === '#000000' ? color : 'var(--text-color)'}` }"
  >
    {{ data }}
  </blockquote>
</template>

<script>
export default {
  props: {
    data: {
      type: String,
      default: "文字",
    },
    color: {
      type: String,
      default: "#000",
    },
  },
};
</script>

<style scoped>
blockquote {
  margin: 16px 0;
  padding: 0 15px;
  color: var(--text-color);
  background: var(--bg-color3);
  border-left: 4px solid #d6d6d6;
}
</style>