<script setup>
import { RouterLink, RouterView } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useLoaderStore } from "@/stores/loader";
import { useRouter } from "vue-router";
import { onMounted } from "vue";
import { backendUrl } from "@/config/env";

const loaderStore = useLoaderStore();
const authStore = useAuthStore();
const router = useRouter();

const logout = async () => {
    const logoutError = await authStore.logout();
    if (logoutError) {
        console.error("Logout failed:", logoutError);
    } else {
        router.push({ name: "home" });
    }
};

onMounted(() => {
    authStore.attempt();
});


const handleLogin = () => {
    window.location.href = `${backendUrl}/login`;
};


const handleRegister = () => {
    window.location.href = `${backendUrl}/register`;
};
</script>

<template>
    <div class="layout">
        <header class="navbar">
            <nav class="nav">
                <div>
                    <RouterLink to="/">Home</RouterLink>
                    <RouterLink to="/about">About</RouterLink>
                    <RouterLink to="/contact">Contact</RouterLink>
                    <RouterLink to="/profile">Profile</RouterLink>
                    <RouterLink to="/posts">Posts</RouterLink>
                </div>
                <div v-if="authStore.isAuthResolved">
                    <div v-if="authStore.isAuthenticated">
                        <span style="color: white"
                            >Welcome, {{ authStore.user.name }}</span
                        >
                        <button @click="logout">Logout</button>
                    </div>
                    <div v-else>
                        <button class="btn-none" @click="handleLogin">Login</button>
                        <button class="btn-none" @click="handleRegister">Register</button>
                    </div>
                </div>
            </nav>
        </header>

        <main class="content">
            <RouterView />
        </main>

        <footer class="footer">
            <p>© 2026 My App</p>
        </footer>
    </div>
</template>

<style scoped>
.nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.layout {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.navbar {
    background: #222;
    padding: 15px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

.navbar a {
    color: white;
    margin-right: 15px;
    text-decoration: none;
}

.content {
    flex: 1;
    padding: 30px;
    margin-top: 56px;
}

.footer {
    background: #eee;
    padding: 10px;
    text-align: center;
}

.btn-none {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    /* font-size: 16px; */
}
</style>
