<template>
  <div>
    <div class="xr-main" v-if="LoadMain">
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
    <div class="xr-start-main" v-else>
      <ItemEx @click="action" :fontSize="24">进入主页菜单</ItemEx>
    </div>
  </div>
</template>

<script>
import ItemEx from "./ItemEx";
import XRList from "./XRList";
import PopupMenu from "./PopupMenu";
export default {
  components: {
    ItemEx,
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
  data() {
    return {
      LoadMain: false,
    };
  },
  methods: {
    action() {
      this.LoadMain = !this.LoadMain;
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
.xr-start-main {
  position: fixed;
  bottom: 5%;
  left: 50%;
  transform: translate(-50%, -50%);
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