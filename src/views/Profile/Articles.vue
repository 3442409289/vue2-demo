<template>
  <div>
    <div v-if="tableData !== undefined">
      <el-table
        :data="
          tableData.filter(
            (data) =>
              !search || data.title.toLowerCase().includes(search.toLowerCase())
          )
        "
        style="width: 100%"
      >
        <el-table-column label="id" prop="id">
          <template slot-scope="id">
            <div>
              {{ id.row[id.column.property] }}
            </div>
          </template>
        </el-table-column>
        <el-table-column label="title" prop="title">
          <template slot-scope="title">
            <el-input
              v-if="title.row.isEdit"
              v-model="title.row[title.column.property]"
              placeholder="title"
            ></el-input>
            <div v-else>
              {{ title.row[title.column.property] }}
            </div>
          </template>
        </el-table-column>
        <el-table-column label="articles" prop="articles">
          <template slot-scope="articles">
            <input
              v-if="articles.row.isEdit"
              type="file"
              @change="readfile(articles.row, $event)"
            />
            <el-link
              v-else
              :href="articles.row[articles.column.property].url"
              >文章链接</el-link
            >
          </template>
        </el-table-column>
        <el-table-column label="author_id" prop="author_id">
          <template slot-scope="author_id">
            <div>
              {{ author_id.row[author_id.column.property] }}
            </div>
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
              v-if="!scope.row.isEdit"
              size="mini"
              @click="handleEdit(scope.$index, scope.row)"
              >Edit</el-button
            >
            <el-button
              v-if="scope.row.isEdit"
              size="mini"
              @click="handleSave(scope.$index, scope.row)"
              >Save</el-button
            >
            <el-button
              size="mini"
              type="danger"
              @click="handleDelete(scope.$index, scope.row)"
              >Delete</el-button
            >
          </template>
        </el-table-column>
      </el-table>
      <el-button
        @click.native.prevent="addNewRow(tableData)"
        type="text"
        size="small"
      >
        添加
      </el-button>
    </div>
    <Loading v-else />
  </div>
</template>

<script>
import Loading from "@/components/Loading";
import api from "@/utils/api";
import Store from "@/store"; // 确保路径正确
export default {
  components: {
    Loading,
  },
  data() {
    return {
      UserInfo: undefined,
      tableData: undefined,
      search: "",
    };
  },
  async created() {
    this.Init();
    const response = await api.post("/mysql/index.php", {
      mode: "Articles",
      type: 0,
      author_id: this.UserInfo.id,
    });
    const transformedData = response.data.map((item) => {
      return {
        id: item.id,
        title: item.title,
        articles: {
          file: undefined,
          url: "/#/AppLayout/PaperViewId/" + item.id,
        },
        author_id: item.author_id,
        isEdit: false,
      };
    });
    this.tableData = transformedData;
  },
  methods: {
    Init() {
      const isAuthenticated = Store.getters["auth/isAuthenticated"];
      if (isAuthenticated) {
        this.UserInfo = Store.getters["auth/currentUser"];
      } else {
        this.UserInfo = undefined;
      }
    },
    handleEdit(index, row) {
      console.log(index, row);
      row.isEdit = true;
    },
    async handleSave(index, row) {
      console.log(index, row);
      var data = {
        id: row.id,
        title: row.title,
        content: "",
        author_id: row.author_id,
      };
      const file = row.articles.file;
      const This = this;
      if (file) {
        const reader = new FileReader();
        reader.onload = async function (e) {
          const fileContent = e.target.result;
          data.content = fileContent;
          This.active(data);
        };
        reader.readAsText(file);
      }
    },
    handleDelete(index, row) {
      console.log(index, row);
    },
    addNewRow(rows) {
      // 新增一list
      const newRow = {
        id: 0,
        title: "",
        articles: {
          file: undefined,
          url: "",
        },
        author_id: this.UserInfo.id,
        isEdit: false,
      };
      rows.push(newRow);
    },
    readfile(row, e) {
      if (e.target.files.length) {
        row.articles.file = e.target.files[0];
      }
    },
    async active(data) {
      const response = await api.post("/mysql/index.php", {
        mode: "Articles",
        type: 1,
        id: this.UserInfo.id,
        data: data,
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