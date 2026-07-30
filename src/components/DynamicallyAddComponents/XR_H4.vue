<template>
  <h4 class="xr-h4" :style="{ color }">
    {{ data }}
  </h4>
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
      default: "#14c7c7",
    },
  },
};
</script>

<style scoped>
.xr-h4 {
  text-shadow: 0 0 5px var(--text-luminescence),
    0 0 10px var(--text-luminescence), 0 0 15px var(--text-luminescence),
    0 0 20px var(--text-luminescence);
}
</style>