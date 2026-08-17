import { computed, onBeforeUnmount, onMounted, ref } from "vue";

export const useAppLauncher = (applications = []) => {
    const appLauncherRef = ref(null);
    const isAppLauncherOpen = ref(false);

    const getOrigin = (value) => {
        try {
            const parsedUrl = new URL(value, window.location.origin);
            return parsedUrl.origin;
        } catch (error) {
            return value;
        }
    };

    const currentAppOrigin = computed(() => window.location.origin);

    const availableApplications = computed(() =>
        applications.filter(
            (application) => getOrigin(application.url) !== currentAppOrigin.value
        )
    );

    const toggleAppLauncher = () => {
        isAppLauncherOpen.value = !isAppLauncherOpen.value;
    };

    const openApplication = (applicationUrl) => {
        window.location.href = applicationUrl;
    };

    const closeAppLauncher = () => {
        isAppLauncherOpen.value = false;
    };

    const handleClickOutside = (event) => {
        if (!appLauncherRef.value?.contains(event.target)) {
            closeAppLauncher();
        }
    };

    const handleEscape = (event) => {
        if (event.key === "Escape") {
            closeAppLauncher();
        }
    };

    onMounted(() => {
        document.addEventListener("click", handleClickOutside);
        document.addEventListener("keydown", handleEscape);
    });

    onBeforeUnmount(() => {
        document.removeEventListener("click", handleClickOutside);
        document.removeEventListener("keydown", handleEscape);
    });

    return {
        appLauncherRef,
        isAppLauncherOpen,
        availableApplications,
        toggleAppLauncher,
        openApplication,
    };
};
