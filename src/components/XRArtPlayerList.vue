<template>
  <div class="xr-artplayer">
    <div
      v-if="is_InitPlayer"
      ref="playerContainer"
      class="artplayer-container"
    ></div>
    <div v-else class="button">
      <Item @active="handleClick"><span>初始化</span></Item>
    </div>
  </div>
</template>

<script>
import Item from "./Item";
import Artplayer from "artplayer";
import Hls from "hls.js";
import flvjs from "flv.js";
import { nextTick } from "vue";
import channelServ from "@/services/channel";

export default {
  components: {
    Item,
  },
  props: {
    url: { type: String, required: true },
    isLive: { type: Boolean, default: false },
    autoplay: { type: Boolean, default: false },
  },
  data() {
    return {
      is_InitPlayer: false,
      artPlayer: null,
      hls: null,
      flvPlayer: null,
      menuPanel: null,
      videoList: [],
      videoIndex: 0,
    };
  },
  async created() {
    document.title = "媒体浏览";
    this.videoList = await channelServ.getChannels(this.url);
  },
  mounted() {},
  beforeUnmount() {
    this.destroyPlayer();
  },
  methods: {
    async handleClick() {
      if (this.videoList[0] !== undefined && this.videoList[0].url !== "") {
        this.is_InitPlayer = true;
        await nextTick();
        this.initPlayer();
      }
    },
    initPlayer() {
      const container = this.$refs.playerContainer;
      if (!container) return;

      const getPlayType = (url) => {
        if (url.includes(".m3u8")) return "m3u8";
        if (url.includes(".mp4")) return "mp4";
        if (url.includes(".flv")) return "flv";
        return "auto"; // 默认自动检测
      };

      const type = getPlayType(this.videoList[0].url);

      const customType = {};
      if (type === "m3u8") {
        customType.m3u8 = (video, url) => {
          if (Hls.isSupported()) {
            this.hls = new Hls();
            this.hls.loadSource(url);
            this.hls.attachMedia(video);
            this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
              video.loadSource();
            });
          } else if (video.canPlayType("application/vnd.apple.mpegurl")) {
            video.src = url; // Safari 原生支持
          }
        };
      } else if (type === "flv") {
        customType.flv = (video, url) => {
          if (flvjs.isSupported()) {
            this.flvPlayer = flvjs.createPlayer({
              type: "flv",
              url,
              isLive: this.isLive, // 点播设为 false，直播设为 true
              enableStashBuffer: this.isLive, // 优化直播缓冲
              stashInitialSize: 512, // 缓冲区大小（KB）
              lazyLoad: !this.isLive, // 启用智能加载（可选）
              autoCleanupSourceBuffer: this.isLive, // 自动清理旧数据（防内存泄漏）[7](@ref)
            });
            this.flvPlayer.attachMediaElement(video);
            this.flvPlayer.load();
          } else {
            console.error("当前浏览器不支持 flv.js");
          }
        };
      }

      this.artPlayer = new Artplayer({
        container,
        url: this.videoList[0].url,
        autoplay: this.autoplay,
        type, // 明确指定HLS类型
        theme: "#23ade5", // 播放器主题颜色，目前用于进度条和高亮元素
        muted: this.autoplay, // 静音
        volume: 0.5, // 播放器的默认音量
        mutex: true,
        autoMini: false, // 当播放器滚动到浏览器视口以外时，自动进入迷你播放模式
        fullscreen: true, // 设置和获取播放器窗口全屏
        fullscreenWeb: true, // 设置和获取播放器网页全屏
        setting: true, // 开启设置面板
        pip: true, // 开启画中画
        playbackRate: true, // 是否显示视频播放速度功能，会出现在设置面板和右键菜单里
        flip: true, // 是否显示视频翻转功能，目前只出现在设置面板和右键菜单
        aspectRatio: true, // 比例
        miniProgressBar: true, // 迷你进度条，只在播放器失去焦点后且正在播放时出现
        crossOrigin: "anonymous", // 关键！允许跨域资源[3,7](@ref)
        screenshot: true, // 截图
        autoPlayback: true, // 自动回放
        icons: {
          state: '<img width="48" heigth="48" src="./img/play.svg">',
        }, // 自定义图标
        settings: [
          {
            html: "Switcher",
            icon: '<img width="22" heigth="22" src="./img/state.svg">',
            tooltip: "OFF",
            switch: false,
            onSwitch: function (item) {
              item.tooltip = item.switch ? "OFF" : "ON";
              console.info("You clicked on the custom switch", item.switch);
              return !item.switch;
            },
          },
          {
            html: "Slider",
            icon: '<img width="22" heigth="22" src="./img/state.svg">',
            tooltip: "5x",
            range: [5, 1, 10, 0.1],
            onRange: function (item) {
              return item.range[0] + "x";
            },
          },
          {
            html: "分享链接",
            icon: '<img width="22" heigth="22" src="./img/state.svg">',
            tooltip: "",
            onClick: async () => {
              this.$copyText(
                `<iframe src="${this.$el.baseURI}" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe>`
              ).then(
                () => this.$toast({ message: "iframe嵌入代码复制成功" }),
                () => this.$toast({ message: "iframe嵌入代码复制失败" })
              );
              return "";
            },
          },
        ],
        controls: [
          {
            name: "selections",
            position: "right",
            html: '<img width="22" heigth="22" src="./img/state.svg">',
            tooltip: "选集",
            style: {
              color: "#fff",
              display: "flex",
            },
            click: () => {
              const isMenuVisible = this.menuPanel.style.display === "flex";
              this.menuPanel.style.display = isMenuVisible ? "none" : "flex";
            },
          },
        ],
        customType,
      });

      // 播放器就绪后，添加自定义选集菜单
      this.artPlayer.on("ready", () => {
        this.addCustomMenu();
      });
    },
    addCustomMenu() {
      // 2. 创建选集菜单面板
      this.menuPanel = document.createElement("div");
      this.menuPanel.id = "artplayer-custom-menu";
      this.menuPanel.style.cssText = `
        position: absolute;
        bottom: 70px;
        right: 10px;
        background: rgba(28, 28, 28, 0.9);
        border-radius: 4px;
        padding: 10px;
        display: none; /* 默认隐藏 */
        flex-direction: column;
        gap: 5px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 200;
      `;

      // 3. 为每个视频源创建菜单项
      this.videoList.forEach((video) => {
        const menuItem = document.createElement("div");
        menuItem.innerHTML = video.title;
        menuItem.style.cssText = `
          padding: 8px 12px;
          cursor: pointer;
          border-radius: 2px;
          white-space: nowrap;
        `;
        menuItem.addEventListener("mouseenter", () => {
          menuItem.style.backgroundColor = "rgba(255, 255, 255, 0.2)";
        });
        menuItem.addEventListener("mouseleave", () => {
          menuItem.style.backgroundColor = "transparent";
        });
        // 点击菜单项切换视频
        menuItem.addEventListener("click", () => {
          this.switchVideo(video);
          this.menuPanel.style.display = "none"; // 切换后隐藏菜单
        });
        this.menuPanel.appendChild(menuItem);
      });

      // 4. 将菜单面板添加到播放器容器
      this.artPlayer.template.$player.appendChild(this.menuPanel);
    },
    switchVideo(video) {
      if (this.artPlayer && video.url !== this.artPlayer.url) {
        // 使用 ArtPlayer 的 switchUrl 方法切换视频源
        this.artPlayer.switchUrl(video.url);
        // 如果需要，也可以更新封面
        // this.artPlayer.poster = video.pic;
      }
    },
    destroyPlayer() {
      if (this.hls) {
        this.hls.destroy();
      }
      if (this.artPlayer) {
        this.artPlayer.destroy();
      }
      if (this.flvPlayer) {
        this.flvPlayer.destroy(); // 销毁 flv.js 实例（如有）
      }
    },
  },
};
</script>

<style scoped>
.xr-artplayer {
  width: 100%;
  height: 100%;
}
.artplayer-container {
  width: 100%;
  height: 100%;
}
.button {
  width: 100%;
  height: 100%;
}
</style>