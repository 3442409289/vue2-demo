<template>
  <div>
    <div v-if="tableData !== undefined">
      <el-table
        :data="
          tableData.filter(
            (data) =>
              !search ||
              data.version.toLowerCase().includes(search.toLowerCase())
          )
        "
        style="width: 100%"
      >
        <el-table-column prop="date" label="Date">
          <template slot-scope="date">
            <el-input
              v-model="date.row[date.column.property]"
              placeholder="date"
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
        <el-table-column prop="color" label="color">
          <template slot-scope="color">
            <el-color-picker
              v-model="color.row[color.column.property]"
            ></el-color-picker>
          </template>
        </el-table-column>
        <el-table-column align="right">
          <template slot="header" slot-scope="scope">
            <el-input
              v-model="search"
              size="mini"
              placeholder="输入关键字搜索"
            />
          </template>
          <template slot-scope="scope">
            <el-button
              size="mini"
              type="danger"
              @click="deleteRow(scope.$index, tableData)"
              >Delete</el-button
            >
          </template>
        </el-table-column>
        <el-table-column align="right" type="expand">
          <template slot-scope="object">
            <Table :data="object.row.object" />
          </template>
        </el-table-column>
      </el-table>
      <el-button
        @click.native.prevent="addNewList(tableData)"
        type="text"
        size="small"
      >
        添加
      </el-button>
      <el-button type="primary" plain @click="active">保存</el-button>
    </div>
    <Loading v-else />
  </div>
</template>

<script>
import Table from "./Log/Table";
import Loading from "@/components/Loading";
import api from "@/utils/api";
export default {
  components: {
    Table,
    Loading,
  },
  data() {
    return {
      tableData: undefined,
      search: "",
    };
  },
  async created() {
    const response = await api.post("../log.json");
    this.tableData = response.data;
  },
  methods: {
    async active() {
      const data = new Blob([JSON.stringify(this.tableData)], {
        type: "application/json",
      });
      const url = URL.createObjectURL(data);
      const a = document.createElement("a");
      a.href = url;
      a.download = "log.json";
      a.click();
      URL.revokeObjectURL(url);
    },
    deleteRow(index, rows) {
      console.log(index, rows);
      rows.splice(index, 1);
    },
    handleDelete(index, row) {
      console.log(index, row);
    },
    addNewList(rows) {
      // 新增一list
      const newRow = {
        date: new Date().toLocaleString(),
        version: "",
        object: [],
      };
      rows.push(newRow);
    },
  },
};
</script>

<style>
</style>