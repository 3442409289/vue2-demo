<template>
  <Main
    :animation="animation"
    @hover_start="$emit('hover_start')"
    @hover_end="$emit('hover_end')"
    @active="handleActive"
    @items1="$emit('items1', $event)"
    @scrollToTop="$emit('scrollToTop')"
  />
</template>

<script>
import Main from "./Main";
import Background from "@/components/Background";
import BackgroundVideo from "@/components/BackgroundVideo";
export default {
  components: {
    Main,
    Background,
    BackgroundVideo,
  },
  props: {
    animation: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    handleActive() {},
  },
  beforeDestroy() {
    // Vue 2
    this.$emit("items1", undefined);
    this.$emit("items2", undefined);
    this.$emit("top", "auto");
    this.$emit("update_My_black_and_white");
  },
};
</script>

<style>
</style>