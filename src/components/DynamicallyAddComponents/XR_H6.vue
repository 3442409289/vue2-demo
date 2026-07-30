<template>
  <h6 class="xr-h6" :style="{ color }">
    {{ data }}
  </h6>
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
      default: "#64cb68",
    },
  },
};
</script>

<style scoped>
.xr-h6 {
  text-shadow: 0 0 5px var(--text-luminescence),
    0 0 10px var(--text-luminescence), 0 0 15px var(--text-luminescence),
    0 0 20px var(--text-luminescence);
}
</style>