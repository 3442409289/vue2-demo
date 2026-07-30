<template>
  <div class="click-effect-wrapper" @click="handleClick">
    <!-- 插槽内容 -->
    <div class="slot-content">
      <slot></slot>
    </div>

    <!-- 特效容器 -->
    <div v-for="effect in effects" :key="effect.id" class="effect-container">
      <!-- 涟漪外层 -->
      <div
        class="ripple"
        :style="{
          left: `${effect.x}px`,
          top: `${effect.y}px`,
        }"
      ></div>

      <!-- 涟漪内层 -->
      <div
        class="ripple-inner"
        :style="{
          left: `${effect.x}px`,
          top: `${effect.y}px`,
        }"
      ></div>

      <!-- 粒子 -->
      <div
        v-for="(particle, index) in effect.particles"
        :key="index"
        class="click-particle"
        :style="{
          left: `${effect.x}px`,
          top: `${effect.y}px`,
          '--tx': `${particle.tx}px`,
          '--ty': `${particle.ty}px`,
          '--duration': `${particle.duration}s`,
        }"
      ></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ClickEffect",

  data() {
    return {
      effects: [],
      effectId: 0,
    };
  },

  methods: {
    // 处理点击事件
    handleClick(event) {
      const rect = event.currentTarget.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;

      this.createEffect(x, y);
    },

    // 创建特效
    createEffect(x, y) {
      const id = this.effectId++;
      const particles = this.generateParticles();

      this.effects.push({
        id,
        x,
        y,
        particles,
      });

      // 2秒后自动清理
      setTimeout(() => {
        this.removeEffect(id);
      }, 1000);
    },

    // 移除特效
    removeEffect(id) {
      const index = this.effects.findIndex((effect) => effect.id === id);
      if (index !== -1) {
        this.effects.splice(index, 1);
      }
    },

    // 生成粒子数据
    generateParticles() {
      const particles = [];
      const count = 12; // 固定12个粒子

      for (let i = 0; i < count; i++) {
        const angle = Math.random() * Math.PI * 2;
        const distance = 30 + Math.random() * 100; // 30-130px

        particles.push({
          tx: Math.cos(angle) * distance,
          ty: Math.sin(angle) * distance,
          duration: 0.5 + Math.random() * 0.5, // 0.5-1秒
        });
      }

      return particles;
    },
  },
};
</script>

<style scoped>
.click-effect-wrapper {
  position: relative;
  overflow: hidden;
  width: 100%;
  height: 100%;
}

.slot-content {
  position: relative;
  z-index: 1;
  width: 100%;
  height: 100%;
}

/* 特效元素样式 */
.effect-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 2;
}

.ripple {
  position: absolute;
  pointer-events: none;
  z-index: 4;
  border: 2px solid var(--Effect-Color);
  border-radius: 50%;
  animation: 1s ease-out forwards rippleExpand;
  transform: translate(-50%, -50%);
}

.ripple-inner {
  position: absolute;
  pointer-events: none;
  z-index: 4;
  border: 1px solid var(--Effect-Color);
  border-radius: 50%;
  animation: 0.8s ease-out forwards rippleExpand2;
  transform: translate(-50%, -50%);
}

.click-particle {
  position: absolute;
  pointer-events: none;
  z-index: 4;
  width: 4px;
  height: 4px;
  animation: particleFly var(--duration) ease-out forwards;
  background: var(--Effect-Color);
  border-radius: 50%;
  transform: translate(-50%, -50%);
}

/* 动画关键帧 */
@keyframes rippleExpand {
  0% {
    width: 0;
    height: 0;
    opacity: 1;
  }
  100% {
    width: 250px;
    height: 250px;
    opacity: 0;
  }
}

@keyframes rippleExpand2 {
  0% {
    width: 0;
    height: 0;
    opacity: 0.8;
  }
  100% {
    width: 160px;
    height: 160px;
    opacity: 0;
  }
}

@keyframes particleFly {
  0% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
  100% {
    opacity: 0;
    transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty)))
      scale(0.2);
  }
}
</style>