<script setup>
import { RouterLink, RouterView } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useLoaderStore } from "@/stores/loader";
import { useRouter } from "vue-router";
import { onMounted } from "vue";
import { backendUrl } from "@/config/env";
import { useAppLauncher } from "@/composables/useAppLauncher";
import { applications } from "@/config/applications";

const loaderStore = useLoaderStore();
const authStore = useAuthStore();
const router = useRouter();
const {
    appLauncherRef,
    isAppLauncherOpen,
    availableApplications,
    toggleAppLauncher,
    openApplication,
} = useAppLauncher(applications);

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
                    <RouterLink to="/todo">Todo list</RouterLink>
                </div>
                <div class="nav-right">
                    <div class="app-launcher" ref="appLauncherRef">
                        <button
                            class="app-launcher-btn"
                            type="button"
                            @click.stop="toggleAppLauncher"
                            aria-label="Ouvrir le lanceur d'applications"
                            :aria-expanded="isAppLauncherOpen"
                        >
                            <span v-for="index in 9" :key="index" class="dot"></span>
                        </button>

                        <div v-if="isAppLauncherOpen" class="app-launcher-menu">
                            <button
                                v-for="application in availableApplications"
                                :key="application.url"
                                type="button"
                                class="app-link"
                                @click="openApplication(application.url)"
                            >
                                <span class="app-logo">
                                    {{ application.name.slice(0, 1).toUpperCase() }}
                                </span>
                                <span>{{ application.name }}</span>
                            </button>

                            <p
                                v-if="availableApplications.length === 0"
                                class="empty-app-list"
                            >
                                Aucune autre application disponible.
                            </p>
                        </div>
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

.nav-right {
    display: flex;
    align-items: center;
    gap: 16px;
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

.app-launcher {
    position: relative;
}

.app-launcher-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid #4a4a4a;
    background: #2f2f2f;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    place-items: center;
    gap: 1px;
    padding: 7px;
    cursor: pointer;
    transition: background 0.2s ease;
}

.app-launcher-btn:hover {
    background: #404040;
}

.dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #fff;
}

.app-launcher-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 230px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.22);
    padding: 10px;
}

.app-link {
    width: 100%;
    border: none;
    background: transparent;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px;
    cursor: pointer;
    text-align: left;
}

.app-link:hover {
    background: #f3f6ff;
}

.app-logo {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, #1f7aea, #67a7ff);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.empty-app-list {
    margin: 8px;
    color: #666;
    font-size: 14px;
}
</style>
