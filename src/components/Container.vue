<template>
  <div>
    <el-popover
      :placement="placement"
      :trigger="trigger"
      :close-delay="800"
      :visible-arrow="false"
      popper-class="popover-content"
      transition="el-zoom-in-top"
      :title="title"
    >
      <!-- 动态内容区域 -->
      <slot name="main"></slot>
      <slot name="button" slot="reference"></slot>
    </el-popover>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      default: "",
    },
    trigger: {
      type: String,
      default: "click",
    },
    placement: {
      type: String,
      default: "top",
    },
  },
};
</script>

<style scope>
.el-popover {
  min-width: auto;
}
</style>
