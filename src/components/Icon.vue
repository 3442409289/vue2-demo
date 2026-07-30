<template>
  <i class="iconfont" :class="[iconClass, extraClass]"></i>
</template>

<script>
var map = {
  arrowUp: "icon-shangjiantou1",
  arrowDown: "icon-xiajiantou",
  arrowUp2: "icon-shangjiantou",
  arrowDown2: "icon-xiajiantou1",
  search: "icon-sousuo",
  TurnOnTheLight: "icon-kaideng",
  TurnOffTheLights: "icon-guandeng",
  TurnOnTheLight2: "icon-dengpaotishi",
  TurnOffTheLights2: "icon-dengpaotishi123-copy",
  share: "icon-fenxiang",
  AnchorPoint: "icon-maodian1-copy",
  AnchorPointHover: "icon-maodian1",
  AnchorPoint2: "icon-maodian",
  AnchorPointHover2: "icon-maodian-copy",
  Like: "icon-icon-like",
  LikeNone: "icon-icon-like-none",
};
export default {
  props: {
    type: {
      type: String,
      required: true,
    },
    extraClass: {
      type: String,
      default: "",
    },
  },
  computed: {
    iconClass() {
      return map[this.type];
    },
  },
};
</script>

<style scoped>
@import "//at.alicdn.com/t/c/font_4992661_0zb31hv2p01d.css";
</style>