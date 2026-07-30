<template>
  <div v-if="xr_data !== undefined" class="main">
    <Loading v-if="Loading" />
    <div v-else>
      <XRMain :data="xr_data.object" :column="xr_data.column" />
    </div>
  </div>
</template>

<script>
import XRMain from "@/components/XRMain";
import SideMenu from "../components/SideMenu";
import Loading from "../components/Loading";
import channelServ from "../services/channel";
import api from "@/utils/api";
export default {
  components: {
    XRMain,
    SideMenu,
    Loading,
  },
  data() {
    return {
      title: "",
      musics: undefined,
      xr_data: undefined,
      Loading: true,
      showSideMenu: false,
      baseUrl: "",
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
            this.$globalSwitch.DarkMode.toggle();
            this.$nextTick(() => {
              if (this.$globalSwitch.DarkMode.isDarkMode) {
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
  metaInfo() {
    return {
      meta: [
        {
          name: "description",
          content:
            "在静谧的插画与音乐背景下，探索一个充满可能性的知识库。从严谨的学科到实用的编程工具，再到启发思维的智力测试，这里是你高效学习与灵感漫步的理想之地。",
        }, // 普通 meta 标签
        {
          name: "keywords",
          content:
            "学习平台, 在线工具, 效率工具, 英语单词, C语言编程, IQ测试, 动漫风格界面, 个人知识库",
        },
        { property: "og:title", content: this.title }, // Open Graph 协议标签
        {
          property: "og:image",
          content: this.baseUrl + "/favicon.ico",
        },
      ],
      link: [
        { rel: "canonical", href: this.baseUrl + "/#/AppLayout" }, // 其他 head 标签
      ],
    };
  },
  async created() {
    if (this.$globalSwitch.isDarkMode) {
      this.items1[1].name = "开灯";
      this.items1[1].icon = "TurnOffTheLights2";
    } else {
      this.items1[1].name = "关灯";
      this.items1[1].icon = "TurnOnTheLight2";
    }
    await this.InitData();
    this.items();
    // this.my_sql = await channelServ.getChannels("/MySQL/index.php?mode=3");
    // console.log(this.my_sql);
  },
  methods: {
    async InitData() {
      this.Loading = true;

      const cacheKey = "music_data";
      const cachedData = await this.getLargeSessionStorage(cacheKey);

      if (cachedData) {
        const { musics, xr_data, baseUrl } = cachedData;
        this.musics = musics;
        this.xr_data = xr_data;
        this.baseUrl = baseUrl;
      } else {
        this.musics = await channelServ.getChannels("/music/index.php?mode=0");

        const response = await api.post("/mysql/index.php", {
          mode: "JsonArraysTable",
          type: 0,
          id: 1,
        });
        this.xr_data = JSON.parse(response.data.data.data)[0];

        sessionStorage.setItem(
          cacheKey,
          JSON.stringify({
            musics: this.musics,
            xr_data: this.xr_data,
          })
        );
        this.$XR_version(this.xr_data.vue_version);
      }

      document.title = this.xr_data.title;
      this.title = this.xr_data.title;
      this.Loading = false;
    },
    test() {
      console.log(123);
    },
    async getLargeSessionStorage(key) {
      return new Promise((resolve) => {
        setTimeout(() => {
          const data = sessionStorage.getItem(key);
          resolve(data ? JSON.parse(data) : null);
        }, 0);
      });
    },
    executeFunction({ id, array }) {
      let scrollTimer;
      const item = array.find((item) => item.id === id);
      if (item && item.action) {
        this.AnchorPoint = false;
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
          this.AnchorPoint = true;
        }, 1000);
        item.action(id);
      } // 执行对应 ID 的函数
    },
    items() {
      this.$emit("items1", this.items1);
    },
  },
};
</script>

<style scoped>
.main {
  padding: 100px 0;
}
</style>