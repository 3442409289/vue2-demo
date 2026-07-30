<template>
  <div :class="['music']">
    <button @click="music_up"  circle-btn>上一首</button>
    <audio
      v-if="data.length"
      ref="audioPlayer"
      preload="none"
      controls="true"
      loop="true"
      @play="
        $emit('play');
        isPlaying = true;
      "
      @pause="isPlaying = false"
    >
      <source :src="data[index].url" />
    </audio>
    <button @click="music_down" circle-btn>下一首</button>
  </div>
</template>

<script>
export default {
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
    };
  },
  mounted() {
    window.addEventListener("beforeunload", this.handleBeforeUnload);
    this.InitMusic();
  },
  methods: {
    music_up() {
      if (this.data.length > 1) {
        if (!this.isIndexValid(this.data, this.index - 1)) {
          this.index = this.data.length - 1;
        } else {
          this.index--;
        }
      }
      this.changeAudio();
    },
    music_down() {
      if (this.data.length > 1) {
        if (!this.isIndexValid(this.data, this.index + 1)) {
          this.index = 0;
        } else {
          this.index++;
        }
      }
      this.changeAudio();
    },
    changeAudio() {
      return new Promise((resolve, reject) => {
        const audio = this.$refs.audioPlayer;
        audio.load(); // 触发重新加载

        // 成功加载时 resolve
        const successHandler = () => {
          audio.removeEventListener("canplaythrough", successHandler);
          audio.removeEventListener("error", errorHandler);
          resolve();
        };

        // 加载失败时 reject
        const errorHandler = (err) => {
          audio.removeEventListener("canplaythrough", successHandler);
          audio.removeEventListener("error", errorHandler);
          reject(new Error("音频加载失败"));
        };

        audio.addEventListener("canplaythrough", successHandler, {
          once: true,
        });
        audio.addEventListener("error", errorHandler, { once: true });
      });
    },
    isIndexValid(arr, index) {
      return index >= 0 && index < arr.length;
    },
    InitMusic() {
      const savedState = sessionStorage.getItem("musicState");
      if (savedState) {
        const { index, isPlaying, currentTime } = JSON.parse(savedState);
        this.index = index;
        this.isPlaying = isPlaying;
        this.currentTime = currentTime;
        if (isPlaying) {
          this.changeAudio()
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
                });
            })
            .catch((err) => console.error(err));
        } else {
          this.changeAudio()
            .then(() => {
              this.$refs.audioPlayer.currentTime = currentTime;
            })
            .catch((err) => console.error(err));
        }
      } else {
        this.changeAudio();
      }
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
  },
  beforeDestroy() {
    // 组件销毁前保存状态
    this.saveProgress();
    window.removeEventListener("beforeunload", this.handleBeforeUnload);
  },
};
</script>

<style scoped>
.music {
  display: flex;
  justify-content: center;
  align-content: flex-end;
  flex-wrap: wrap;
  width: 100%;
  height: 100%;
}

.music audio {
  transition: var(--time1A);
  height: 54px;
  flex: 1;
}

.music audio::-webkit-media-controls-panel {
  background: var(--bg-color2);
}

@media screen and (min-width: 769px) {
  .music audio {
    flex: 0.5;
  }
}

@media screen and (max-width: 768px) {
  .music audio {
    flex: 1;
  }
}

</style>