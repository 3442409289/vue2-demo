<template>
  <div class="marquee">
    <div class="text" :style="textStyle" ref="text">{{ text }}</div>
  </div>
</template>
 
<script>
export default {
  props: {
    text: {
      type: String,
      required: true,
    },
    speed: {
      // 滚动速度，单位为px/s
      type: Number,
      default: -1, // 默认速度为1px/s,负数表示从右往左，正数表示从左往右
    },
  },
  data() {
    return {
      position: 0, // 当前位置，初始化为0（从左向右滚动）
    };
  },
  computed: {
    textStyle() {
      // 计算样式以实现滚动效果
      return { transform: `translateX(${this.position}px)` };
    },
  },
  mounted() {
    this.position = this.$refs.text.clientWidth;
    setInterval(() => {
      this.marquee = this.$refs.text;
      this.position += this.speed;
      if (this.marquee) {
        if (Math.abs(this.position) >= this.marquee.scrollWidth) {
          this.position = this.marquee.clientWidth;
        }
      }
    }, 20);
  },
};
</script>
<style scoped>
.marquee {
  overflow: hidden;
  white-space: nowrap;
}
.marquee .text {
  color: red;
}
</style>