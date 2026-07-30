<template>
  <h3 class="xr-h3" :style="{ color }">
    {{ data }}
  </h3>
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
      default: "#d3117c",
    },
  },
};
</script>

<style scoped>
.xr-h3 {
  text-shadow: 0 0 5px var(--text-luminescence),
    0 0 10px var(--text-luminescence), 0 0 15px var(--text-luminescence),
    0 0 20px var(--text-luminescence);
}
</style>