<template>
  <div class="LogComponent layout">
    <div v-for="item in Json" :key="item.id" class="item">
      <h1 :style="{ color: item.color }">{{ item.version }}</h1>
      <div v-for="(object, i) in item.object" :key="i">
        <XR_Blockquote :data="object.log" />
      </div>
    </div>
  </div>
</template>

<script>
import XR_Blockquote from "@/components/DynamicallyAddComponents/XR_Blockquote";
import channelServ from "@/services/channel";
export default {
  components: {
    XR_Blockquote,
  },
  props: {
    data: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      Json: [],
      items1: [
        {
          id: 0,
          name: "顶部",
          icon: "arrowUp2",
          is_show: false,
          action: (e) => {
            this.$emit("scrollToTop");
          },
        },
        {
          id: 1,
          name: "关灯",
          icon: "TurnOnTheLight2",
          is_show: true,
          action: (e) => {
            this.$globalSwitch.toggle();
            this.$nextTick(() => {
              if (this.$globalSwitch.isDarkMode) {
                this.items1[e].name = "开灯";
                this.items1[e].icon = "TurnOffTheLights2";
              } else {
                this.items1[e].name = "关灯";
                this.items1[e].icon = "TurnOnTheLight2";
              }
            });
          },
        },
      ],
    };
  },
  async created() {
    document.title = "更新日志";
    await this.InitData();
  },
  methods: {
    async InitData() {
      this.Loading = true;

      this.$emit("items1", this.items1);

      this.Json = await channelServ.getChannels(this.data);

      this.Loading = false;
    },
  },
};
</script>

<style scoped>
.LogComponent {
}
.LogComponent .item {
}
</style>