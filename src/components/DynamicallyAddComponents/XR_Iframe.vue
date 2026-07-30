<template>
  <iframe
    class="xr-iframe"
    :src="data"
    scrolling="no"
    border="0"
    frameborder="no"
    framespacing="0"
    allowfullscreen="true"
  ></iframe>
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
.xr-iframe {
  width: 100%;
  height: 500px;
}
</style>