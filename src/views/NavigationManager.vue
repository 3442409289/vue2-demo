<template>
  <div>
    <div v-if="config !== undefined">
      <el-table :data="config" style="width: 100%">
        <el-table-column prop="title" label="title">
          <template slot-scope="title">
            <el-input
              v-model="title.row[title.column.property]"
              placeholder="title"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="column" label="column" width="200">
          <template slot-scope="column">
            <el-input-number
              v-model="column.row[column.column.property]"
              :min="1"
              :max="100"
              label="column"
            ></el-input-number>
          </template>
        </el-table-column>
        <el-table-column prop="is_show" label="is_show">
          <template slot-scope="is_show">
            <el-switch
              v-model="is_show.row[is_show.column.property]"
              active-color="#13ce66"
              inactive-color="#ff4949"
            >
            </el-switch
          ></template>
        </el-table-column>
        <el-table-column prop="text" label="text">
          <template slot-scope="text">
            <el-input
              v-model="text.row[text.column.property]"
              placeholder="text"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="vue_text" label="vue_text">
          <template slot-scope="vue_text">
            <el-input
              v-model="vue_text.row[vue_text.column.property]"
              placeholder="vue_text"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="vue_version" label="vue_version">
          <template slot-scope="vue_version">
            <el-input
              v-model="vue_version.row[vue_version.column.property]"
              placeholder="vue_version"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="version" label="version">
          <template slot-scope="version">
            <el-input
              v-model="version.row[version.column.property]"
              placeholder="version"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="BackgroundVideo" label="BackgroundVideo">
          <template slot-scope="BackgroundVideo">
            <el-input
              v-model="BackgroundVideo.row[BackgroundVideo.column.property]"
              placeholder="BackgroundVideo"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column prop="marqueeText" label="marqueeText">
          <template slot-scope="marqueeText">
            <el-input
              v-model="marqueeText.row[marqueeText.column.property]"
              placeholder="marqueeText"
            ></el-input>
          </template>
        </el-table-column>
        <el-table-column label="object" width="60" type="expand">
          <template slot-scope="object">
            <List :data="object.row.object" />
          </template>
        </el-table-column>
      </el-table>
      <el-button type="primary" plain @click="active">保存</el-button>
    </div>
    <Loading v-else />
  </div>
</template>

<script>
import List from "./NavigationManager/List";
import Loading from "@/components/Loading";
import api from "@/utils/api";
export default {
  components: {
    List,
    Loading,
  },
  data() {
    return {
      config: undefined,
    };
  },
  async created() {
    const response = await api.post("/mysql/index.php", {
      mode: "JsonArraysTable",
      type: 0,
      id: 1,
    });
    this.config = response.data;
  },
  methods: {
    async active() {
      const response = await api.post("/mysql/index.php", {
        mode: "JsonArraysTable",
        type: 1,
        id: 1,
        data: this.config,
      });
      if (!response.data.code) {
        this.$toast({ message: "保存数据成功" });
      }
    },
  },
};
</script>

<style>
</style>