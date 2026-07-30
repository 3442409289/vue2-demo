<template>
  <div class="aside">
    <ChannelSearch />
    <TitleMenu :isActive="activeId === 100" @active="activeId = 100">
      <template v-slot:title> 发现频道 </template>
      <template v-slot:icon> > </template>
    </TitleMenu>
    <ChannelList :activeId="activeId" @active="activeId = $event" />
  </div>
</template>

<script>
import TitleMenu from "./TitleMenu";
import ChannelList from "./ChannelList";
import ChannelSearch from "./ChannelSearch";
export default {
  components: {
    TitleMenu,
    ChannelList,
    ChannelSearch,
  },
  data() {
    return {
      activeId: 100,
    };
  },
};
</script>

<style scoped>
.aside {
  width: 100%;
  height: 100%;
  overflow: auto;
  border-right: 2px solid #e7e7e7;
  box-sizing: border-box;
}

.aside::-webkit-scrollbar {
  width: 5px;
  height: 5px;
  background: rgb(0, 140, 140);
}

.aside::-webkit-scrollbar-button {
  width: 0;
  height: 0;
}

.aside::-webkit-scrollbar-thumb {
  border-radius: 10px;
  background: rgb(243, 138, 184);
}

.aside::-webkit-scrollbar-track {
}

.aside::-webkit-scrollbar-corner {
  background: rgba(0, 140, 140, 0.5);
}
</style>