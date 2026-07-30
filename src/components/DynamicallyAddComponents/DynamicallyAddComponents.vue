<template>
  <div class="markdown-preview">
    <div v-for="(comp, index) in data" :key="index">
      <div v-if="isEdit" class="isEdit">
        <button @click="$emit('active_delete', index)">删除</button>
        <button @click="$emit('active_modify', index)">修改</button>
        <PopupMenu position="right">
          <template v-slot:button> 添加组件 </template>
          <template v-slot:menu>
            <ItemList
              :data="item"
              @active="$emit('active_add', { id: $event, index: index })"
            />
          </template>
        </PopupMenu>
      </div>
      <component
        :is="comp.name"
        :data="comp.data"
        :color="comp.color"
        :ref="
          comp.name === item[RefIndex].name ? `${comp.name}_${comp.id}` : null
        "
        class="comp"
      />
    </div>
  </div>
</template>

<script>
import PopupMenu from "../PopupMenu";
import ItemList from "./ItemList";
import XR_H1 from "./XR_H1";
import XR_H2 from "./XR_H2";
import XR_H3 from "./XR_H3";
import XR_H4 from "./XR_H4";
import XR_H5 from "./XR_H5";
import XR_H6 from "./XR_H6";
import XR_Blockquote from "./XR_Blockquote";
import XR_PreCode from "./XR_PreCode";
import XR_Temp from "./XR_Temp";
import XR_Image from "./XR_Image";
import XR_A from "./XR_A";
import XR_Iframe from "./XR_Iframe";
import XR_Video from "./XR_Video";
import XR_Video_Ex from "./XR_Video_Ex";
export default {
  components: {
    PopupMenu,
    ItemList,
    XR_H1,
    XR_H2,
    XR_H3,
    XR_H4,
    XR_H5,
    XR_H6,
    XR_Blockquote,
    XR_PreCode,
    XR_Temp,
    XR_Image,
    XR_A,
    XR_Iframe,
    XR_Video,
    XR_Video_Ex,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
    RefIndex: {
      type: Number,
      default: 1,
    },
  },
  created() {
    this.InitData();
  },
  data() {
    return {
      item: [
        { id: 2, name: "XR_H1" },
        { id: 3, name: "XR_H2" },
        { id: 4, name: "XR_H3" },
        { id: 5, name: "XR_H4" },
        { id: 6, name: "XR_H5" },
        { id: 7, name: "XR_H6" },
        { id: 8, name: "XR_Blockquote" },
        { id: 9, name: "XR_PreCode" },
        { id: 10, name: "XR_Temp" },
        { id: 11, name: "XR_Image" },
        { id: 12, name: "XR_A" },
        { id: 13, name: "XR_Iframe" },
        { id: 14, name: "XR_Video" },
        { id: 15, name: "XR_Video_Ex" },
      ],
      items: [],
    };
  },
  methods: {
    InitData() {
      this.items = [];
      const matchedItems = this.data.filter(
        (item) => item.name === this.item[this.RefIndex].name
      );
      matchedItems.forEach((item, index) => {
        this.items.push({
          id: index,
          name: item.data,
          icon: "AnchorPoint",
          ref: `${item.name}_${item.id}`,
          refs: this.$refs,
          is_show: true,
          action: (e) => {
            if (
              this.$refs[this.items[e].ref] &&
              this.$refs[this.items[e].ref].length !== 0
            ) {
              this.$refs[this.items[e].ref][0].$el.scrollIntoView({
                behavior: "smooth",
                block: "start",
              });
            }
          },
        });
      });

      this.$emit("items", this.items);
    },
    handleScroll(e) {
      this.items.forEach((item) => {
        if (this.$refs[item.ref] && this.$refs[item.ref].length !== 0) {
          const element = this.$refs[item.ref][0].$el;
          if (element) {
            const elementTop = this.getElementScrollPosition(element);
            // 判断当前视口是否覆盖锚点区域
            if (
              elementTop.topPosition <= 100 &&
              elementTop.topPosition >= -100
            ) {
              this.$emit("AnchorReached", item.id); // 触发自定义事件
            }
          }
        }
      });
    },
    getElementScrollPosition(element) {
      // 1. 获取页面总滚动高度和当前滚动位置
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const scrollHeight = document.documentElement.scrollHeight;

      // 2. 计算元素相对于文档顶部的绝对位置（像素）
      const elementRect = element.getBoundingClientRect();
      const elementTop = elementRect.top + scrollTop; // 元素顶部到文档顶部的距离（像素）
      const elementBottom = elementTop + elementRect.height; // 元素底部到文档顶部的距离（像素）

      // 3. 返回元素在滚动条中的实际位置（像素值）
      return {
        topPosition: elementTop, // 元素顶部在文档中的绝对位置（像素）
        bottomPosition: elementBottom, // 元素底部在文档中的绝对位置（像素）
        scrollHeight: scrollHeight, // 文档总高度（像素）
        currentScroll: scrollTop, // 当前滚动位置（像素）
      };
    },
  },
};
</script>

<style scoped>
.markdown-preview {
  position: relative;
  left: 50%;
  transform: translateX(-50%);
}

@media screen and (min-width: 769px) {
  .markdown-preview {
    padding: 4em var(--Layout-LeftOfRightPadding);
  }
}

@media screen and (max-width: 768px) {
  .markdown-preview {
    padding: 4em 1em;
  }
}

.isEdit {
  position: fixed;
  left: calc(100% - 300px);
  z-index: 11;
}

.isEdit:hover + .comp {
  background-color: #d97373;
}

.comp {
}
</style>