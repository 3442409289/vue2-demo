<template>
  <div class="music">
    <div class="song-name-wrapper" ref="wrapper">
      <div
        class="song-name"
        ref="songName"
        :style="{ transform: `translateX(${offset}px)` }"
      >
        <span class="scroll-text">{{ displayText }}</span>
      </div>
    </div>
    <div class="actions">
      <div class="action-item">
        <Container trigger="hover">
          <template v-slot:main>
            <div style="width: 150px">
              <el-slider
                v-model="sliderValue"
                @input="handleSliderChange"
              ></el-slider>
            </div>
          </template>
          <template v-slot:button>
            <ItemEx class="text">音量</ItemEx>
          </template>
        </Container>
      </div>
      <div class="action-item">
        <Loading v-if="isLoading" :size="25" style="position: relative" />
        <ItemEx v-else @click="Player" class="text">{{
          isPlaying ? "暂停" : "播放"
        }}</ItemEx>
      </div>
      <div class="action-item">
        <Container trigger="hover">
          <template v-slot:main>
            <div class="list">
              <ItemEx v-for="(item, i) in data" :key="i" @click="action(i)">
                {{ item.url.split("/").pop() }}
              </ItemEx>
            </div>
          </template>
          <template v-slot:button>
            <ItemEx class="text">列表</ItemEx>
          </template>
        </Container>
      </div>
    </div>
    <audio
      ref="audioPlayer"
      :src="data[index].url"
      class="hidden-audio"
      preload="none"
      loop="true"
      @play="handlePlay"
      @pause="handlePause"
      @ended="updateStatus"
    ></audio>
  </div>
</template>

<script>
import ItemEx from "@/components/ItemEx";
import Loading from "@/components/Loading";
import Container from "@/components/Container";
export default {
  components: {
    ItemEx,
    Loading,
    Container,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      index: 0,
      isPlaying: false,
      currentTime: 0,
      audioVolume: 0.5,
      isLoading: true,
      displayText: "",
      offset: 0,
      direction: -1, // -1 向左，1 向右
      animFrameId: null,
      startTime: null,
      pauseStart: null,
      isPaused: false,
      state: "idle", // idle, moving-left, pause-left, moving-right, pause-right
    };
  },
  async mounted() {
    window.addEventListener("beforeunload", this.handleBeforeUnload);
    await this.InitMusic();
    this.setVolume(this.audioVolume);
    this.updateText();
    window.addEventListener("resize", this.handleResize);
  },
  computed: {
    sliderValue: {
      get() {
        return Math.round(this.audioVolume * 100);
      },
      set(val) {
        this.audioVolume = val / 100;
      },
    },
    audioPaused() {
      return this.$refs.audioPlayer?.paused ?? true;
    },
  },
  watch: {
    // 监听音量变化，同步到音频元素
    audioVolume(newVal) {
      this.setVolume(newVal);
    },
    index: {
      handler() {
        this.updateText();
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    Player() {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        if (this.isPlaying) {
          audio.pause();
        } else {
          audio.play();
        }
      }
    },
    async action(i) {
      this.isPlaying = false;
      this.index = i;
      await this.changeAudio();
      const audio = this.$refs.audioPlayer;
      if (audio) {
        if (this.isPlaying) {
          audio.pause();
        } else {
          audio.play();
        }
      }
    },
    handleSliderChange(val) {
      this.audioVolume = val / 100;
    },
    setVolume(value) {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        audio.volume = Math.max(0, Math.min(1, value));
      }
    },
    updateStatus() {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        this.isPlaying = !audio.paused;
      }
    },
    handlePlay() {
      this.$emit("play");
      // 每次播放时确保音量设置正确
      this.setVolume(this.audioVolume);
      this.updateStatus();
    },
    handlePause() {
      this.updateStatus();
    },
    async InitMusic() {
      const savedState = sessionStorage.getItem("musicState");
      if (savedState) {
        const { index, isPlaying, currentTime } = JSON.parse(savedState);
        this.index = index;
        this.isPlaying = isPlaying;
        this.currentTime = currentTime;
        if (isPlaying) {
          await this.changeAudio()
            .then(() => {
              this.$refs.audioPlayer.currentTime = currentTime;
              this.$confirm(
                "检测到上次未播放完的音乐，是否继续播放？",
                "提示",
                {
                  confirmButtonText: "继续",
                  cancelButtonText: "取消",
                  type: "info",
                }
              )
                .then(() => {
                  // 用户点击继续
                  this.$refs.audioPlayer.play().catch((e) => console.error(e));
                })
                .catch(() => {
                  // 用户点击取消
                  this.updateStatus();
                });
            })
            .catch((err) => console.error(err));
        } else {
          await this.changeAudio()
            .then(() => {
              this.$refs.audioPlayer.currentTime = currentTime;
            })
            .catch((err) => console.error(err));
        }
      } else {
        await this.changeAudio();
      }
    },
    async changeAudio() {
      this.isLoading = true;

      return await new Promise((resolve, reject) => {
        const audio = this.$refs.audioPlayer;

        // 成功加载时 resolve
        const successHandler = () => {
          audio.removeEventListener("canplaythrough", successHandler);
          audio.removeEventListener("error", errorHandler);
          this.isLoading = false;
          resolve();
        };

        // 加载失败时 reject
        const errorHandler = (err) => {
          audio.removeEventListener("canplaythrough", successHandler);
          audio.removeEventListener("error", errorHandler);
          this.isLoading = false;
          reject(new Error("音频加载失败"));
        };

        audio.addEventListener("canplaythrough", successHandler, {
          once: true,
        });
        audio.addEventListener("error", errorHandler, { once: true });

        requestAnimationFrame(() => {
          audio.load(); //触发加载
        });
      });
    },
    isIndexValid(arr, index) {
      return index >= 0 && index < arr.length;
    },
    saveProgress() {
      // 实时保存播放进度和状态
      sessionStorage.setItem(
        "musicState",
        JSON.stringify({
          index: this.index,
          isPlaying: this.isPlaying,
          currentTime: this.$refs.audioPlayer.currentTime,
        })
      );
    },
    handleBeforeUnload() {
      // 组件销毁前保存状态
      this.saveProgress();
      window.removeEventListener("beforeunload", this.handleBeforeUnload);
    },
    updateText() {
      this.displayText = this.data[this.index]?.url?.split("/").pop() || "";
      this.$nextTick(() => {
        this.startAnimation();
      });
    },
    handleResize() {
      this.restartAnimation();
    },
    startAnimation() {
      this.stopAnimation();

      const wrapper = this.$refs.wrapper;
      const songName = this.$refs.songName;

      if (!wrapper || !songName) return;

      const wrapperWidth = wrapper.clientWidth;
      const textWidth = songName.scrollWidth;

      if (textWidth <= wrapperWidth) {
        this.offset = 0;
        return;
      }

      const maxOffset = wrapperWidth - textWidth;
      this.offset = 0;
      this.state = "moving-left";
      this.startTime = performance.now();
      this.lastTimestamp = performance.now();

      const animate = (timestamp) => {
        if (this.state === "idle") return;

        const deltaTime = timestamp - this.lastTimestamp;
        this.lastTimestamp = timestamp;

        // 每秒移动 50px
        const speed = 50; // px/s
        const moveAmount = (speed * deltaTime) / 1000;

        switch (this.state) {
          case "moving-left":
            this.offset -= moveAmount;
            if (this.offset <= maxOffset) {
              this.offset = maxOffset;
              this.state = "pause-left";
              this.pauseStart = timestamp;
            }
            break;

          case "pause-left":
            if (timestamp - this.pauseStart >= 2000) {
              // 暂停 2 秒
              this.state = "moving-right";
            }
            break;

          case "moving-right":
            this.offset += moveAmount;
            if (this.offset >= 0) {
              this.offset = 0;
              this.state = "pause-right";
              this.pauseStart = timestamp;
            }
            break;

          case "pause-right":
            if (timestamp - this.pauseStart >= 2000) {
              // 暂停 2 秒
              this.state = "moving-left";
            }
            break;
        }

        this.animFrameId = requestAnimationFrame(animate);
      };

      this.animFrameId = requestAnimationFrame(animate);
    },
    stopAnimation() {
      if (this.animFrameId) {
        cancelAnimationFrame(this.animFrameId);
        this.animFrameId = null;
      }
      this.state = "idle";
    },
    restartAnimation() {
      this.stopAnimation();
      this.startAnimation();
    },
  },
  beforeDestroy() {
    // 组件销毁前保存状态
    this.saveProgress();
    window.removeEventListener("beforeunload", this.handleBeforeUnload);
    this.stopAnimation();
    window.removeEventListener("resize", this.handleResize);
  },
};
</script>

<style scoped>
.music {
  width: 100%;
  height: 100%;
  background: var(--bg-color4A);
  border-radius: 10px;
  padding: 0 20px;
  display: flex;
  align-items: center; /* 垂直居中 */
  justify-content: space-between; /* 两端对齐 */
  gap: 10px;
}

.song-name-wrapper {
  width: 100px; /* 固定宽度，根据需要调整 */
  overflow: hidden;
  white-space: nowrap;
}

.song-name {
  display: inline-block;
  white-space: nowrap;
  will-change: transform;
}

.scroll-text {
  display: inline-block;
  color: var(--text-color);
}

.actions {
  display: flex;
  gap: 10px;
}

.action-item {
  /* 保持原有样式 */
}

.list {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.list div {
  width: auto;
  margin: 10px 0;
}

.hidden-audio {
  display: none;
}

.text {
  white-space: nowrap;
}

/* 滚动动画：从左到右再到左 */
@keyframes scrollLeftRight {
  0% {
    transform: translateX(0);
  }
  45% {
    transform: translateX(calc(200px - 100%)); /* 滚动到最右侧 */
  }
  55% {
    transform: translateX(calc(200px - 100%)); /* 停留片刻 */
  }
  100% {
    transform: translateX(0); /* 回到起点 */
  }
}
</style>