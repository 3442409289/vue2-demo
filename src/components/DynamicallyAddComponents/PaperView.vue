<template>
  <DynamicallyAddComponents
    ref="DynamicallyAddComponents"
    :data="data"
    @items="$emit('items', $event)"
    @AnchorReached="$emit('AnchorReached', $event)"
    :RefIndex="RefIndex"
  />
</template>

<script>
import Background from "../Background";
import BackgroundVideo from "../BackgroundVideo";
import DynamicallyAddComponents from "./DynamicallyAddComponents";
export default {
  components: {
    Background,
    BackgroundVideo,
    DynamicallyAddComponents,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
    RefIndex: {
      type: Number,
      default: 1,
    },
  },
  methods: {
    handleScroll(e) {
      this.$refs.DynamicallyAddComponents.handleScroll(e);
    },
  },
};
</script>

<style>
</style>