<template>
  <h2 class="xr-h2" :style="{ color }">
    {{ data }}
  </h2>
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
      default: "red",
    },
  },
};
</script>

<style scoped>
.xr-h2 {
  text-shadow: 0 0 5px var(--text-luminescence),
    0 0 10px var(--text-luminescence), 0 0 15px var(--text-luminescence),
    0 0 20px var(--text-luminescence);
}
</style>