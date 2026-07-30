<template>
  <transition name="fade">
    <div v-if="visible" class="toast">
      <TextBox :text="message" />
    </div>
  </transition>
</template>

<script>
import TextBox from "./TextBox";
export default {
  components: {
    TextBox,
  },
  data() {
    return {
      visible: false,
      message: "",
      duration: 5000,
    };
  },
  methods: {
    show(options) {
      this.message = options.message || "";
      this.duration = options.duration || this.duration;
      this.visible = true;

      setTimeout(() => {
        this.hide();
      }, this.duration);
    },
    hide() {
      this.visible = false;
      setTimeout(() => {
        this.$destroy();
        this.$el.parentNode?.removeChild(this.$el);
      }, 300); // 等待动画结束
    },
  },
};
</script>

<style scoped>
.toast {
  position: fixed;
  top: 50%;
  left: 50%;
  user-select: none;
  transform: translate(-50%, -50%);
  padding: 22px 24px;
  color: var(--text-color);
  border-radius: 4px;
  z-index: 1000000;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>