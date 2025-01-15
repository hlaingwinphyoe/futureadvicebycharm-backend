<template>
    <header class="container mx-auto flex justify-between items-center p-4">
        <div
            @click="showSidebar"
            class="px-3 py-1 border border-[#363638] text-primary-500 rounded cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="22"
                height="22"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-width="1.5"
                    d="M20 7H4m16 5H4m16 5H4"
                />
            </svg>
        </div>
        <div class="flex items-center">
            <el-button
                v-if="theme == 'light'"
                circle
                class="mr-4"
                @click="darkMode"
                color="#000"
            >
                <el-icon><Moon /></el-icon>
            </el-button>
            <el-button
                v-else
                type="warning"
                circle
                class="mr-4"
                @click="lightMode"
                plain
            >
                <el-icon><Sunny /></el-icon>
            </el-button>
            <el-dropdown class="mr-3">
                <el-button text bg>
                    <el-icon size="large"><UserFilled /></el-icon>
                    <span>{{ user.name }}</span>
                </el-button>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item>
                            <Link
                                class="flex items-center gap-2"
                                :href="route('profile.edit')"
                            >
                                <el-icon><Tools /></el-icon>
                                <span>Profile</span>
                            </Link>
                        </el-dropdown-item>
                        <el-dropdown-item divided>
                            <Link
                                class="flex items-center gap-2 text-red-500"
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                <el-icon><SwitchButton /></el-icon>
                                <span>Log Out</span>
                            </Link>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>
    </header>
</template>

<script>
import { useDarkMode } from "@/darkmode";
import {
    UserFilled,
    Tools,
    SwitchButton,
    Sunny,
    Moon,
} from "@element-plus/icons-vue";
import { usePage } from "@inertiajs/vue3";
import moment from "moment";
import { onMounted } from "vue";

export default {
    components: {
        UserFilled,
        Tools,
        SwitchButton,
        Sunny,
        Moon,
    },
    emits: ["show-event"],
    setup(props, { emit }) {
        const user = usePage().props.auth.user;

        const showSidebar = () => {
            emit("show-event");
        };

        const { theme, darkMode, lightMode } = useDarkMode();

        const createdDate = (date) => {
            return moment(date).format("DD-MMMM-YYYY");
        };

        onMounted(() => {
            let getTheme = JSON.parse(localStorage.getItem("theme")) ?? "dark";
            if (getTheme == "dark") {
                darkMode();
            } else {
                lightMode();
            }
        });

        return {
            theme,
            darkMode,
            lightMode,
            showSidebar,
            user,
            createdDate,
        };
    },
};
</script>

<style></style>
