<!-- components/Login.vue -->
<template>
  <div class="login-container">
    <div class="login-form">
      <h2 style="color: var(--text-color)">用户登录</h2>
      <form @submit.prevent="handleLogin">
        <div class="form-group" :class="{ error: errors.username }">
          <label for="username" style="color: var(--text-color)">用户名:</label>
          <input
            type="text"
            id="username"
            v-model="form.username"
            placeholder="请输入用户名"
            :class="{ error: errors.username }"
          />
          <span class="error-message" v-if="errors.username">{{
            errors.username
          }}</span>
        </div>

        <div class="form-group" :class="{ error: errors.password }">
          <label for="password" style="color: var(--text-color)">密码:</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            placeholder="请输入密码"
            :class="{ error: errors.password }"
          />
          <span class="error-message" v-if="errors.password">{{
            errors.password
          }}</span>
        </div>

        <div class="form-options">
          <label class="remember-me" style="color: var(--text-color)">
            <input type="checkbox" v-model="form.rememberMe" />
            记住我
          </label>
        </div>

        <button type="submit" style="display: none"></button>
        <ItemEx type="submit" :disabled="loading" @click="handleLogin"
          ><span v-if="!loading">登录</span>
          <span v-else>登录中...</span></ItemEx
        >
        <div class="error-message server-error" v-if="serverError">
          {{ serverError }}
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import ItemEx from "@/components/ItemEx";
export default {
  name: "Login",
  components: {
    ItemEx,
  },
  data() {
    return {
      form: {
        username: "",
        password: "",
        rememberMe: false,
      },
      errors: {
        username: "",
        password: "",
      },
      loading: false,
      serverError: "",
    };
  },

  methods: {
    ...mapActions("auth", ["login"]),

    validateForm() {
      let isValid = true;
      this.errors = { username: "", password: "" };
      this.serverError = "";

      if (!this.form.username.trim()) {
        this.errors.username = "用户名不能为空";
        isValid = false;
      }

      if (!this.form.password) {
        this.errors.password = "密码不能为空";
        isValid = false;
      } else if (this.form.password.length < 6) {
        this.errors.password = "密码长度不能少于6位";
        isValid = false;
      }

      return isValid;
    },

    async handleLogin() {
      if (!this.validateForm()) return;

      this.loading = true;
      this.serverError = "";

      try {
        await this.login({
          username: this.form.username,
          password: this.form.password,
          rememberMe: this.form.rememberMe,
        });
        const redirect = this.$route.query.redirect || "/";
        this.$router.push(redirect);
      } catch (error) {
        this.handleLoginError(error);
      } finally {
        this.loading = false;
      }
    },

    handleLoginError(error) {
      if (error.response) {
        switch (error.response.status) {
          case 401:
            this.serverError = error.response.data.error;
            break;
          case 500:
            this.serverError = "服务器错误，请稍后再试";
            break;
          default:
            this.serverError = "登录失败，请重试";
        }
      } else {
        this.serverError = "网络错误，请检查网络连接";
      }
    },
  },

  mounted() {
    // 如果已登录，跳转到首页
    if (this.$store.getters["auth/isAuthenticated"]) {
      //this.$router.push("/");
    }
  },
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.login-form {
  background: var(--bg-color4A);
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

h2 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #333;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #555;
}

input[type="text"],
input[type="password"] {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--color12C);
  border-radius: 4px;
  font-size: 1rem;
  box-sizing: border-box;
  background: var(--bg-color4A);
  color: var(--text-color);
}

input:focus {
  outline: none;
  border-color: var(--color12D);
}

input.error {
  border-color: #e74c3c;
}

.error-message {
  color: #e74c3c;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}

.server-error {
  text-align: center;
  margin-top: 1rem;
  font-weight: 500;
}

.form-options {
  margin: 1rem 0;
}

.remember-me {
  display: flex;
  align-items: center;
  font-weight: normal;
}

.remember-me input {
  width: auto;
  margin-right: 0.5rem;
}

.login-btn {
  width: 100%;
  padding: 0.75rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
  font-family: var(--Current-Font);
}

.login-btn:hover:not(:disabled) {
  background-color: #2980b9;
}

.login-btn:disabled {
  background-color: var(--bg-color4A);
  cursor: not-allowed;
}
</style>