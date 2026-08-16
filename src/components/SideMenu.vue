<template>
  <div
    :class="['side-menu']"
    :style="[
      position ? { left: `${range}px` } : { right: `${range}px` },
      order ? { flexDirection: 'column' } : { flexDirection: 'column-reverse' },
      { top: top },
      { bottom: bottom },
    ]"
  >
    <div
      class="side-menu-item"
      v-for="(item, i) in data"
      :key="i"
      :style="{
        opacity: `${item.is_show | is_show ? '' : '0'}`,
        visibility: `${item.is_show | is_show ? 'visible' : 'hidden'}`,
      }"
    >
      <SideMenuButton
        v-if="item.childmenu === undefined"
        :data="item.name"
        :icon="item.icon"
        @active="$emit('active', { id: item.id, array: data })"
      />
      <Container trigger="hover" v-else-if="item.childmenu !== undefined">
        <template v-slot:main>
          <div class="list">
            <SideMenuButton
              v-for="(menu, i) in item.childmenu"
              :key="i"
              :data="menu.name"
              :icon="menu.icon"
              @active="menu.action(i)"
            />
          </div>
        </template>
        <template v-slot:button>
          <SideMenuButton data="更多" />
        </template>
      </Container>
    </div>
  </div>
</template>

<script>
import SideMenuButton from "./SideMenuButton";
import Container from "./Container";
import ItemEx from "./ItemEx";
export default {
  components: {
    SideMenuButton,
    Container,
    ItemEx,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
    is_show: {
      type: Boolean,
      default: true,
    },
    position: {
      type: Boolean,
      default: false,
    },
    range: {
      type: Number,
      default: 20,
    },
    order: {
      type: Boolean,
      default: false,
    },
    top: {
      type: String,
      default: "100px",
    },
    bottom: {
      type: String,
      default: "100px",
    },
  },
};
</script>

<style scoped>
.side-menu {
  -webkit-user-select: none;
  -moz-user-select: none;
  user-select: none;
  position: fixed;
  overflow: auto;
  display: flex;
  transition: 1s;
  opacity: var(--bg-opacity);
}

.side-menu .side-menu-item {
  transition: opacity 0.3s ease;
}

.list {
  display: flex;
  flex-wrap: wrap;
}

.list div {
  flex: 0 0 calc(100% / 3); /* 每行3个 */
}

@media screen and (min-width: 769px) {
  .side-menu .side-menu-item {
    opacity: 1;
  }
}

@media screen and (max-width: 768px) {
  .side-menu .side-menu-item {
    opacity: 0.5;
  }
}
</style>