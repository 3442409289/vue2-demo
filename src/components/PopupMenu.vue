<template>
  <div class="popover-wrap">
    <div
      :style="{ offsetWidth: popover_button_width }"
      ref="popover_button"
      class="popover-button"
      @mouseenter="hover_start"
      @mouseleave="hover_end"
      @click="$emit('active')"
    >
      <slot name="button"></slot>
    </div>
    <div
      ref="popover"
      class="popover"
      :class="[map[position], isMenuVisible ? 'hover-start' : 'hover-end']"
      @mouseenter="hover_start"
      @mouseleave="hover_end"
    >
      <slot name="menu"></slot>
    </div>
  </div>
</template>
 
<script>
export default {
  props: {
    position: {
      type: String,
      default: "bottom", // 默认位置为顶部
    },
  },
  data() {
    return {
      isMenuVisible: false,
      map: {
        top: "is-top-start",
        right: "is-right-start",
        bottom: "is-bottom-start",
        left: "is-left-start",
      },
      isMode: false,
      popover_button_width: 0, // 示例值
      popover_width: 0, // 示例值
    };
  },
  mounted() {
    if (this.position === "bottom") {
      this.popover_button_width = this.$refs.popover_button.offsetWidth;
      this.popover_width = this.getHiddenElementWidth(this.$refs.popover);
      this.$refs.popover.style.left = `calc(${
        this.popover_button_width / 2
      }px + -${this.popover_width / 2}px)`;
    }
  },
  methods: {
    hover_start() {
      this.isMenuVisible = true;
    },
    hover_end() {
      this.isMenuVisible = false;
    },
    getHiddenElementWidth(element) {
      // 保存原始的 display 属性
      const originalDisplay = element.style.display;

      // 临时改变 display 属性为 block，以便可以获取宽度
      element.style.display = "block";

      // 获取宽度
      const rect = element.getBoundingClientRect();
      const width = rect.width || rect.right - rect.left; // 可能需要两者之一，取决于浏览器实现

      // 恢复原始的 display 属性
      element.style.display = originalDisplay;

      return width;
    },
  },
};
</script>
 
<style scoped>
@keyframes jump {
  0% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-3px);
  }

  to {
    transform: translateY(0);
  }
}

.popover-wrap {
  position: relative;
  display: inline-block;
  height: 100%;
  text-align: center;
}

.popover-button {
  cursor: pointer;
  display: inline-block;
  height: 100%;
  text-align: center;
}

.popover-button:hover {
  animation: jump 0.3s;
}

.hover-start {
  opacity: 1;
  visibility: visible;
}

.hover-end {
  opacity: 0;
  visibility: hidden;
}

.popover {
  user-select: text;
  position: absolute;
  transition: 0.3s;
  z-index: 1;
}

.popover.is-top-start {
  bottom: 100%;
  left: 0;
}

.popover.is-left-start {
  top: 0;
  right: 100%;
}

.popover.is-right-start {
  top: 0;
  left: 100%;
}

.popover.is-bottom-start {
  top: 100%;
  left: 0;
}
</style>