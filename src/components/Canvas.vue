<template>
  <canvas class="xr_canvas" ref="canvas"> </canvas>
</template>

<script>
var canvas = undefined;
var ctx = undefined;
var num = 2;
var color = "#ff8c8c";
function drawStar(ctx, cx, cy, spikes, outerRadius, color, life, innerRadius) {
  if (innerRadius === undefined) {
    innerRadius = outerRadius * 0.382; // 默认内径比例
  }

  const rot = Math.PI / 180; // 起始旋转角度
  const step = (Math.PI * 2) / spikes; // 每个尖角的角度

  ctx.beginPath();
  for (let i = 0; i < spikes; i++) {
    // 计算外点坐标
    const x = cx + Math.cos(rot + i * step) * outerRadius;
    const y = cy + Math.sin(rot + i * step) * outerRadius;
    ctx.lineTo(x, y);

    // 计算内点坐标
    const xInner = cx + Math.cos(rot + (i + 0.5) * step) * innerRadius;
    const yInner = cy + Math.sin(rot + (i + 0.5) * step) * innerRadius;
    ctx.lineTo(xInner, yInner);
  }
  ctx.closePath();

  ctx.fillStyle = color;
  ctx.globalAlpha = life;
  ctx.fill();
}
function Particle(x, y, speed, radius, spikes, lifespeed) {
  this.x = x;
  this.y = y;
  this.vx = (Math.random() - 0.5) * speed;
  this.vy = (Math.random() - 0.5) * speed;
  this.radius = Math.random() * radius;
  if (spikes) {
    this.spikes = spikes;
  } else {
    this.spikes = num;
  }
  this.life = 1;
  if (lifespeed) {
    this.lifespeed = lifespeed;
  } else {
    this.lifespeed = 0.01;
  }
  this.color = color;
}

Particle.prototype.update = function () {
  this.x += this.vx;
  this.y += this.vy;
  if (this.life > this.lifespeed) {
    this.life -= this.lifespeed;
  } else {
    this.life = 0;
  }
  if (this.life < 0) {
    console.error(this.life);
  }
};

Particle.prototype.draw = function () {
  // ctx.beginPath();
  // ctx.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
  // ctx.fillStyle = color;
  // ctx.globalAlpha = this.life;
  // ctx.fill();
  drawStar(
    ctx,
    this.x,
    this.y,
    this.spikes,
    this.radius,
    this.color,
    this.life
  );
};

export default {
  props: {
    is_animation: {
      type: Boolean,
      default: false,
    },
    is_love: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      particles: [],
      data: undefined,
      json: undefined,
      intervalId: null, // 存储定时器ID
      resizeListener: null, // 存储resize事件回调（若需解绑）
      mouseMoveListener: null, // 存储mousemove事件回调
    };
  },
  watch: {
    is_animation(newVal) {
      newVal ? this.Initanimate() : () => {};
    },
    is_love(newVal) {
      newVal ? this.love() : () => {};
    },
  },
  mounted() {
    canvas = this.$refs.canvas;
    ctx = canvas.getContext("2d");
  },
  methods: {
    RandomColor() {
      const str = "0123456789abcdef";
      var randomcolor = "#";
      for (let index = 0; index < 6; index++) {
        const randomIndex = Math.floor(Math.random() * str.length);
        randomcolor += str[randomIndex];
      }
      return randomcolor;
    },
    getRandomInt(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    onMouseMove(e) {
      const x = e.clientX;
      const y = e.clientY;
      this.particles.push(new Particle(x, y, 5, 30));
    },
    animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      this.particles.forEach((particle) => {
        particle.update();
        particle.draw();
      });
      this.particles = this.particles.filter((particle) => particle.life > 0);
      requestAnimationFrame(this.animate);
    },
    Init_Width_Height() {
      if (
        canvas.width !== window.innerWidth ||
        canvas.height !== window.innerHeight
      ) {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        return {
          size: canvas.width < canvas.height ? canvas.width : canvas.height,
        };
      } else {
        return 0;
      }
    },
    generateHeartShape(numPoints, size) {
      const points = [];
      const xScale = size; // 控制爱心的大小
      const yScale = size;
      for (let t = 0; t < 2 * Math.PI; t += Math.PI / numPoints) {
        const x = xScale * (16 * Math.pow(Math.sin(t), 3));
        const y =
          yScale *
          (13 * Math.cos(t) -
            5 * Math.cos(2 * t) -
            2 * Math.cos(3 * t) -
            Math.cos(4 * t));
        points.push({ x, y });
      }
      return points;
    },
    Initanimate() {
      this.json = this.Init_Width_Height();
      this.data = this.generateHeartShape(
        this.json.size / 5,
        this.json.size / 35
      );

      this.intervalId = setInterval(() => {
        color = this.RandomColor();
        num = this.getRandomInt(2, 5);
      }, 2000);

      this.resizeListener = () => {
        this.json = this.Init_Width_Height();
        this.data = this.generateHeartShape(
          this.json.size / 5,
          this.json.size / 35
        );
      };

      window.addEventListener("resize", this.resizeListener);
      document.addEventListener("mousemove", this.onMouseMove);

      this.animate();
    },
    cleanup() {
      if (this.intervalId) clearInterval(this.intervalId);
      if (this.resizeListener) {
        window.removeEventListener("resize", this.resizeListener);
      }
      document.removeEventListener("mousemove", this.onMouseMove);
    },
    love() {
      if (this.is_animation) {
        if (this.data) {
          for (let i = 0; i < this.data.length; i++) {
            this.particles.push(
              new Particle(
                this.data[i].x + canvas.width / 2,
                this.data[i].y * -1 + canvas.height / 2,
                0.5,
                10,
                4,
                0.01
              )
            );
          }
        }
      }
    },
  },
  beforeDestroy() {
    // Vue 2
    this.cleanup();
  },
};
</script>

<style scoped>
.xr_canvas {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1;
  pointer-events: none;
}
</style>