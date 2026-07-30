<template>
  <div
    :class="[
      'app',
      { 'dark-mode': isDarkMode },
      { 'bg-opacity': isBgOpacity },
      { 'text-luminescence': isDarkMode },
    ]"
  >
    <ClickEffect> <router-view /> </ClickEffect>
  </div>
</template>

<script>
import ClickEffect from "@/components/ClickEffect";

export default {
  components: {
    ClickEffect,
  },
  computed: {
    isDarkMode() {
      const savedMode = JSON.parse(sessionStorage.getItem("darkMode"));
      if (savedMode !== null) {
        this.$globalSwitch.DarkMode.setMode(savedMode);
      }
      document.body.classList = savedMode ? "dark-mode" : "";
      return this.$globalSwitch.DarkMode.isDarkMode; // 确保 this 指向正确
    },
    isBgOpacity() {
      const savedMode = JSON.parse(sessionStorage.getItem("bgOpacity"));
      if (savedMode !== null) {
        this.$globalSwitch.BgOpacity.setMode(savedMode);
      }
      return this.$globalSwitch.BgOpacity.isBgOpacity; // 确保 this 指向正确
    },
  },
};
</script>

<style scoped>
.app {
  width: 100%;
  height: 100%;
}
</style>