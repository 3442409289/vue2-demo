<template>
  <div class="xr-box">
    <div class="title" :style="{ color: data.color }">{{ title }}</div>
    <div class="xr-list">
      <div
        v-for="item in data.object"
        :key="item.id"
        class="list-item"
        :style="{
          width: `calc(${100 / column}% - ${padding_left_right * 2}px)`,
          paddingLeft: `${padding_left_right}px`,
          paddingRight: `${padding_left_right}px`,
          paddingTop: `${padding_top_bottom}px`,
          paddingBottom: `${padding_top_bottom}px`,
        }"
      >
        <XRItem
          :url="item.url"
          :name="item.name"
          :blank="item.target"
          :isRouter="item.router !== undefined && item.router !== false"
          :params="item.params"
        />
      </div>
    </div>
  </div>
</template>

<script>
import XRItem from "./XRItem";
import XRButtonA from "./XRButtonA";
export default {
  components: {
    XRItem,
    XRButtonA,
  },
  props: {
    data: {
      type: Object,
      required: true,
    },
    column: {
      type: Number,
      default: 2,
    },
    padding_left_right: {
      type: Number,
      default: 5,
    },
    padding_top_bottom: {
      type: Number,
      default: 5,
    },
    title: {
      type: String,
      default: "标题",
    },
  },
};
</script>

<style scoped>
.xr-box {
  position: relative;
  background: var(--bg-color4A);
  transform: rotate(0deg) skewX(0deg);
  box-shadow: -20px 20px 10px rgba(0, 0, 0, 0.5);
  transition: 0.5s;
  border-radius: 13px;
  overflow: hidden;
}
.xr-box:hover {
  background-color: var(--bg-color7A);
  transform: rotate(0deg) skewX(0deg) translate(10px, -15px);
  box-shadow: -50px 50px 50px rgba(0, 0, 0, 0.5);
}
.xr-box .title {
  text-align: center;
}
.xr-box .xr-list {
  width: 100%;
  border-top: var(--border_width1A) solid var(--bg-color6B);
  border-bottom: var(--border_width1A) solid var(--bg-color6B);
  box-sizing: border-box;
  display: flex;
  flex-wrap: wrap;
  margin: 1em 0;
}
.xr-box .xr-list .list-item {
  transition: 0.5s;
  float: left;
}
</style>