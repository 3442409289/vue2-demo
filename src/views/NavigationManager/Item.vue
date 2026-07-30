<template>
  <div>
    <el-table :data="tableData" style="width: 100%">
      <el-table-column prop="name" label="name">
        <template slot-scope="name">
          <el-input
            v-model="name.row[name.column.property]"
            placeholder="name"
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
      <el-table-column prop="color" label="color">
        <template slot-scope="color">
          <el-color-picker
            v-model="color.row[color.column.property]"
          ></el-color-picker>
        </template>
      </el-table-column>
      <el-table-column prop="background_color" label="background_color">
        <template slot-scope="background_color">
          <el-color-picker
            v-model="background_color.row[background_color.column.property]"
          ></el-color-picker>
        </template>
      </el-table-column>
      <el-table-column width="50" label="操作">
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
      <el-table-column fixed="right" label="object" width="60" type="expand">
        <template slot-scope="object">
          <el-table :data="object.row.object" style="width: 100%">
            <el-table-column fixed prop="id" label="Id">
              <template slot-scope="id">
                <el-input
                  v-model="id.row[id.column.property]"
                  placeholder="id"
                ></el-input>
              </template>
            </el-table-column>
            <el-table-column prop="name" label="name">
              <template slot-scope="name">
                <el-input
                  v-model="name.row[name.column.property]"
                  placeholder="name"
                ></el-input>
              </template>
            </el-table-column>
            <el-table-column prop="url" label="url">
              <template slot-scope="url">
                <el-input
                  v-model="url.row[url.column.property]"
                  placeholder="url"
                ></el-input>
              </template>
            </el-table-column>
            <el-table-column prop="target" label="target">
              <template slot-scope="scope">
                <el-switch
                  v-model="scope.row[scope.column.property]"
                  active-color="#13ce66"
                  inactive-color="#ff4949"
                >
                </el-switch
              ></template>
            </el-table-column>
            <el-table-column prop="router" label="router">
              <template slot-scope="scope">
                <el-switch
                  v-model="scope.row[scope.column.property]"
                  active-color="#13ce66"
                  inactive-color="#ff4949"
                >
                </el-switch
              ></template>
            </el-table-column>
            <el-table-column prop="params" label="params">
              <template slot-scope="params">
                <el-input
                  v-if="params.row[params.column.property] !== undefined"
                  v-model="params.row[params.column.property].url"
                  placeholder="params"
                ></el-input>
              </template>
            </el-table-column>
            <el-table-column fixed="right" width="100" label="操作">
              <template slot-scope="scope">
                <el-button
                  @click.native.prevent="
                    deleteRow(scope.$index, object.row.object)
                  "
                  type="text"
                  size="small"
                >
                  移除
                </el-button>
              </template>
            </el-table-column>
          </el-table>
          <el-button
            @click.native.prevent="addNewRow(object.row.object)"
            type="text"
            size="small"
          >
            添加
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
    addNewRow(rows) {
      // 新增一行
      const newRow = {
        id: 0,
        name: "",
        url: "",
        target: false,
        router: false,
        params: { url: "" },
      };
      rows.push(newRow);
    },
    addNewList(rows) {
      // 新增一list
      const newRow = {
        name: "",
        column: 2,
        color: "",
        background_color: "",
        object: [],
      };
      rows.push(newRow);
    },
  },
};
</script>

<style scoped>
.item {
}
</style>