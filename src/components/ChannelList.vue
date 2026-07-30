<template>
  <div>
    <div class="channel-list" :style="{ height: `${height}px` }">
      <div
        v-for="item in channels"
        :key="item.id"
        class="item"
        :style="{ width: `${100 / columns}%` }"
      >
        <Channel
          @active="$emit('active', item.id)"
          :isActive="item.id === activeId"
          :data="item"
        />
      </div>
    </div>
    <div class="collapse" @click="isExpand = !isExpand">
      <span>{{ isExpand ? "收起" : "展开" }}</span>
      <Icon :type="isExpand ? 'arrowUp' : 'arrowDown'" extraClass="icon" />
    </div>
  </div>
</template>

<script>
import Channel from "./Channel";
import channelServ from "@/services/channel";
import Icon from "./Icon";
export default {
  components: { Channel, Icon },
  props: {
    activeId: {
      type: Number,
      required: false,
    },
    columns: {
      type: Number,
      default: 2,
    },
  },
  data() {
    return {
      channels: [
        { id: 0, name: "动漫1", channel_count: 205 },
        { id: 1, name: "动漫1", channel_count: 205 },
        { id: 2, name: "动漫2", channel_count: 205 },
        { id: 3, name: "动漫3", channel_count: 205 },
        { id: 4, name: "动漫4", channel_count: 205 },
        { id: 5, name: "动漫5", channel_count: 205 },
        { id: 6, name: "动漫6", channel_count: 205 },
        { id: 7, name: "动漫7", channel_count: 205 },
        { id: 8, name: "动漫8", channel_count: 205 },
        { id: 9, name: "动漫9", channel_count: 205 },
        { id: 10, name: "动漫10", channel_count: 205 },
        { id: 11, name: "动漫11", channel_count: 205 },
        { id: 12, name: "动漫12", channel_count: 205 },
        { id: 13, name: "动漫13", channel_count: 205 },
      ],
      isExpand: true, //是否是展开状态
    };
  },
  computed: {
    height() {
      var rows = 3;
      if (this.isExpand) {
        rows = Math.ceil(this.channels.length / this.columns);
      }
      return rows * 40;
    },
  },
  async created() {
    console.log("组件被创建了");
    // this.channels = await channelServ.getChannels();
  },
};
</script>

<style scoped>
@import "//at.alicdn.com/t/font_1564527_7ksvh9f13lg.css";
.channel-list {
  overflow: hidden;
  transition: 0.3s;
}
.item {
  float: left;
}
.collapse {
  clear: both;
  height: 40px;
  line-height: 40px;
  text-align: center;
  color: #999;
  font-size: 14px;
  cursor: pointer;
  border-bottom: 1px solid #e7e7e7;
}
.icon {
  font-size: 12px;
  margin-left: 5px;
}
</style>