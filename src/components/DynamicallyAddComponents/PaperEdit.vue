<template>
  <div class="PaperEdit">
    <DynamicallyAddComponents
      :data="componentList"
      @active_delete="active_delete"
      @active_modify="active_modify"
      @active_add="active_add"
      :isEdit="true"
      :value="value"
    />
    <div class="edit">
      <textarea
        cols="30"
        rows="10"
        v-model="value"
        @keydown.tab.prevent="handleTab"
      ></textarea>
      <PopupMenu>
        <template v-slot:button> 添加组件 </template>
        <template v-slot:menu>
          <ItemList :data="item" @active="test" />
          <div class="backColor">
            <span>target</span>
            <el-switch
              v-model="target"
              active-color="#13ce66"
              inactive-color="#ff4949"
            >
            </el-switch>
          </div>
          <input
            class="backColor"
            type="color"
            v-model="color"
            :style="{ width: '100%' }"
          />
          <input
            class="backColor"
            type="file"
            @change="readfile"
            :style="{ width: '100%' }"
          />
        </template>
      </PopupMenu>
    </div>
  </div>
</template>

<script>
import Background from "../Background";
import PopupMenu from "../PopupMenu";
import DynamicallyAddComponents from "./DynamicallyAddComponents";
import ItemList from "./ItemList";
export default {
  components: {
    Background,
    PopupMenu,
    DynamicallyAddComponents,
    ItemList,
  },
  data() {
    return {
      item: [
        { id: 0, name: "保存" },
        { id: 1, name: "读取" },
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
      componentList: [],
      value: "",
      color: "#000000",
      target: true,
    };
  },
  created() {
    document.title = "编辑文章";
  },
  mounted() {},
  methods: {
    readfile(e) {
      if (e.target.files.length) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = (tmp) => {
          this.componentList = JSON.parse(tmp.target.result);
        };
        reader.readAsText(file); // 或 readAsDataURL、readAsArrayBuffer
      }
    },
    InitArrayId(array) {
      for (let index = 0; index < array.length; index++) {
        array[index].id = index;
      }
    },
    test(e) {
      if (e === 0) {
        //保存文章相关代码
        const userInput = prompt();
        if (!userInput || userInput === "") {
          return;
        }

        this.InitArrayId(this.componentList);

        localStorage.setItem(userInput, JSON.stringify(this.componentList));

        const data = new Blob([JSON.stringify(this.componentList)], {
          type: "application/json",
        });
        const url = URL.createObjectURL(data);
        const a = document.createElement("a");
        a.href = url;
        a.download = userInput + ".json";
        a.click();
        URL.revokeObjectURL(url);
        a.remove();
        return;
      }

      if (e === 1) {
        const userInput = prompt();
        if (!userInput || userInput === "") {
          return;
        }
        const savedItems = JSON.parse(localStorage.getItem(userInput)) || [];
        this.componentList = savedItems; // Vue 3的ref赋值[5](@ref)
        return;
      }

      const userInput = this.value;
      if (!userInput || userInput === "") {
        return;
      }

      switch (this.item[e].name) {
        case "XR_H1":
        case "XR_H2":
        case "XR_H3":
        case "XR_H4":
        case "XR_H5":
        case "XR_H6":
        case "XR_Blockquote":
        case "XR_PreCode":
        case "XR_Temp":
        case "XR_Image":
        case "XR_Iframe":
        case "XR_Video":
        case "XR_Video_Ex":
          this.componentList.push({
            name: this.item[e].name,
            data: userInput,
            color: this.color === "#000000" ? undefined : this.color,
          });
          break;
        case "XR_A":
          const title = prompt();
          this.componentList.push({
            name: this.item[e].name,
            data: { src: userInput, title, title, blank: this.target },
          });
          break;
      }
    },
    active_delete(e) {
      this.componentList.splice(e, 1);
    },
    active_modify(e) {
      const userInput = this.value;
      if (!userInput || userInput === "") {
        return;
      }

      switch (this.componentList[e].name) {
        case "XR_H1":
        case "XR_H2":
        case "XR_H3":
        case "XR_H4":
        case "XR_H5":
        case "XR_H6":
        case "XR_Blockquote":
        case "XR_PreCode":
        case "XR_Temp":
        case "XR_Image":
        case "XR_Iframe":
        case "XR_Video":
        case "XR_Video_Ex":
          this.$set(this.componentList, e, {
            name: this.componentList[e].name,
            data: userInput,
            color: this.color === "#000000" ? undefined : this.color,
          });
          break;
        case "XR_A":
          const title = prompt();
          this.$set(this.componentList, e, {
            name: this.componentList[e].name,
            data: { src: userInput, title, title, blank: this.target },
          });
          break;
      }
    },
    active_add(e) {
      const userInput = this.value;
      if (!userInput || userInput === "") {
        return;
      }

      switch (this.item[e.id].name) {
        case "XR_H1":
        case "XR_H2":
        case "XR_H3":
        case "XR_H4":
        case "XR_H5":
        case "XR_H6":
        case "XR_Blockquote":
        case "XR_PreCode":
        case "XR_Temp":
        case "XR_Image":
        case "XR_Iframe":
        case "XR_Video":
        case "XR_Video_Ex":
          this.componentList.splice(e.index, 0, {
            name: this.item[e.id].name,
            data: userInput,
            color: this.color === "#000000" ? undefined : this.color,
          });
          break;
        case "XR_A":
          const title = prompt();
          this.componentList.splice(e.index, 0, {
            name: this.item[e.id].name,
            data: { src: userInput, title, title, blank: this.target },
          });
          break;
      }
    },
    handleTab(e) {
      const textarea = e.target;
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const value = textarea.value;

      if (start === end && !e.shiftKey) {
        // 单行缩进（同基础实现）
        textarea.value =
          value.substring(0, start) + "\t" + value.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + 1;
      } else {
        // 多行缩进或取消缩进
        const lines = value.substring(0, end).split("\n");
        const startLine = value.substring(0, start).split("\n").length - 1;
        const endLine = lines.length - 1;

        lines.slice(startLine, endLine + 1).forEach((line, i) => {
          const lineIndex = startLine + i;
          if (e.shiftKey) {
            // Shift+Tab：取消缩进（移除开头\t或空格）
            lines[lineIndex] = line.replace(/^\t|^ {1,4}/, "");
          } else {
            // Tab：增加缩进
            lines[lineIndex] = "\t" + line;
          }
        });

        textarea.value = lines.join("\n");
        // 调整光标位置（需计算缩进后的新位置）
        textarea.selectionStart = start + (e.shiftKey ? -1 : 1);
        textarea.selectionEnd =
          end + (e.shiftKey ? -1 : 1) * (endLine - startLine + 1);
      }
    },
  },
};
</script>

<style scoped>
.PaperEdit {
  width: 100%;
  height: 100%;
  overflow: auto;
}

.edit {
  position: fixed;
  top: 120px;
  left: 20px;
}

.edit textarea {
  background: 0;
  border: 1px solid var(--color3B);
}

.backColor {
  background: var(--bg-color3);
  border: none;
}
</style>