<template>
  <div>
    <el-table :data="tableData" style="width: 100%">
      <el-table-column prop="log" label="log">
        <template slot-scope="log">
          <el-input
            v-model="log.row[log.column.property]"
            placeholder="log"
          ></el-input>
        </template>
      </el-table-column>
      <el-table-column fixed="right" width="100" label="操作">
        <template slot-scope="scope">
          <el-button
            @click.native.prevent="deleteRow(scope.$index, tableData)"
            type="text"
            size="small"
          >
            移除
          </el-button>
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
      tableData: this.data,
    };
  },
  methods: {
    deleteRow(index, rows) {
      rows.splice(index, 1);
    },
    addNewList(rows) {
      // 新增一list
      const newRow = {
        log: "",
      };
      rows.push(newRow);
    },
  },
};
</script>

<style>
</style>