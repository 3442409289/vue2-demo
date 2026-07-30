<template>
  <div
    class="background"
    :style="{
      backgroundImage: `${url}`,
      backgroundSize: `${animation ? max_size : min_size}%`,
    }"
  >
    <div
      :class="['black_and_white']"
      :style="{
        backdropFilter: `grayscale(${animation ? 0 : My_black_and_white})`,
      }"
      ref="dom"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      default() {
        return { image1: "var(--image2A)", image2: "var(--image1A)" };
      },
    },
    animation: {
      type: Boolean,
      default: false,
    },
    max_size: {
      type: Number,
      default: 200,
    },
  },
  data() {
    return {
      url: "",
      min_size: 150,
      My_black_and_white: 1,
      windowWidth: window.innerWidth, // 初始宽度
    };
  },
  computed: {
    isMobile() {
      return this.windowWidth < 768; // 根据你的需求调整阈值
    },
  },
  watch: {
    windowWidth(newWidth) {
      // 侦听窗口宽度变化
      if (newWidth < 768) {
        // 执行手机端的代码
        this.url = this.data.image1;
      } else {
        // 执行PC端的代码
        this.url = this.data.image2;
      }
    },
  },
  mounted() {
    // 组件挂载后添加事件监听器以更新窗口宽度属性
    this.updateWindowWidth(); // 初始化时更新一次宽度
    window.addEventListener("resize", this.updateWindowWidth); // 添加事件监听器以更新宽度属性
    this.$refs.dom.addEventListener("scroll", this.update); // 添加事件监听器以更新宽度属性
  },
  beforeDestroy() {
    // 组件销毁前移除事件监听器以避免内存泄漏问题
    window.removeEventListener("resize", this.updateWindowWidth); // 移除事件监听器以更新宽度属性
    this.$refs.dom.removeEventListener("scroll", this.update); // 移除事件监听器以更新宽度属性
  },
  methods: {
    Set_My_black_and_white(e) {
      this.My_black_and_white = e;
    },
    update() {
      // 获取当前滚动位置
      const scrollTop =
        this.$refs.dom.scrollTop ||
        document.documentElement.scrollTop ||
        document.body.scrollTop;

      // 获取页面总高度和视口高度
      const scrollHeight =
        this.$refs.dom.scrollHeight ||
        document.documentElement.scrollHeight ||
        document.body.scrollHeight;
      const clientHeight =
        document.documentElement.clientHeight || document.body.clientHeight;

      // 计算百分比
      const scrollPercent = scrollTop / (scrollHeight - clientHeight);
      this.My_black_and_white = 1 - scrollPercent;
      this.$emit("XR_scroll", scrollTop);
    },
    updateWindowWidth() {
      this.windowWidth = window.innerWidth;
      if (this.isMobile) {
        this.url = this.data.image1;
      } else {
        this.url = this.data.image2;
      }
    },
    scrollToTop() {
      this.$refs.dom.scrollTo({ top: 0, behavior: "smooth" });
    },
  },
};
</script>

<style>
.background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-position: center;
  background-repeat: no-repeat;
  transition: 1s;
  background-color: var(--color1B);
}

.black_and_white {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: var(--bg-color1);
  color: var(--text-color);
  transition: all 1s, background-color 0s ease, color 0s;
}
</style>