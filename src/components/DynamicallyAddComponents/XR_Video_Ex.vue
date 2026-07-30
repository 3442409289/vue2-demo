<template>
  <div class="xr-video-ex">
    <XRArtPlayer :url="data" />
  </div>
</template>

<script>
import XRArtPlayer from "@/components/XRArtPlayer";
export default {
  components: {
    XRArtPlayer,
  },
  props: {
    data: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped>
.xr-video-ex {
  width: 100%;
  height: 500px;
}
</style>