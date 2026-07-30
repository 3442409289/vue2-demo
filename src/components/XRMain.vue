<template>
  <div class="xr-main">
    <div
      v-for="item in data"
      :key="item.id"
      class="main-item"
      :style="{
        width: `calc(${100 / column}% - ${padding_left_right * 2}px)`,
        paddingLeft: `${padding_left_right}px`,
        paddingRight: `${padding_left_right}px`,
        paddingTop: `${padding_top_bottom}px`,
        paddingBottom: `${padding_top_bottom}px`,
      }"
    >
      <XRList :column="item.column" :data="item" :title="item.name" />
    </div>
  </div>
</template>

<script>
import XRList from "./XRList";
import PopupMenu from "./PopupMenu";
export default {
  components: {
    XRList,
    PopupMenu,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
    column: {
      type: Number,
      default: 2,
    },
    padding_left_right: {
      type: Number,
      default: 10,
    },
    padding_top_bottom: {
      type: Number,
      default: 5,
    },
  },
};
</script>

<style scoped>
.xr-main {
  display: flex;
  flex-wrap: wrap;
}
.main-item {
  transition: 0.5s;
  float: left;
}
@media screen and (min-width: 769px) {
  .xr-main {
    padding: 1em var(--Layout-LeftOfRightPadding);
  }
}
@media screen and (max-width: 768px) {
  .xr-main {
    padding: 1em;
  }
}
</style>