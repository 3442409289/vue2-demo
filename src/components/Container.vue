<template>
  <div>
    <el-popover
      placement="top-start"
      trigger="hover"
      popper-class="my-custom-popover"
      transition="el-zoom-in-top"
      :visible-arrow="false"
    >
      <div class="container">
        <SideMenuButton
          class="item"
          v-for="item in data"
          :key="item.id"
          :data="item.name"
          :icon="item.icon"
          @active="$emit('active', { id: item.id, array: data })"
        />
      </div>
      <SideMenuButton slot="reference" data="更多" />
    </el-popover>
  </div>
</template>

<script>
import SideMenuButton from "@/components/SideMenuButton.vue";
export default {
  components: {
    SideMenuButton,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style scope>
.el-popover.my-custom-popover {
  min-width: 0px;
  padding: 0;
  background: none;
  border: none;
  box-shadow: none;
}

@media screen and (min-width: 769px) {
  .container {
    opacity: 1;
  }
}

@media screen and (max-width: 768px) {
  .container {
    opacity: 0.5;
  }
}
</style>
