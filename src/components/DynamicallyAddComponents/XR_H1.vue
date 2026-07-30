<template>
  <h1 class="xr-h1" :style="{ color }">
    {{ data }}
  </h1>
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
      default: "#000000",
    },
  },
};
</script>

<style scoped>
.xr-h1 {
  text-shadow: 0 0 5px var(--text-luminescence),
    0 0 10px var(--text-luminescence), 0 0 15px var(--text-luminescence),
    0 0 20px var(--text-luminescence);
}
</style>