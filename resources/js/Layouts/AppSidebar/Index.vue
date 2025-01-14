<template>
    <el-scrollbar
        :class="show ? 'opacity-100' : 'opacity-0'"
        class="transition-all duration-300 h-screen"
    >
        <div
            class="sidebar-header p-2 mb-2 border-b sticky top-0 z-50 w-full"
        >
            <Link
                :href="route('admin.dashboard')"
                class="flex items-center justify-center gap-2 p-2 text-primary-500"
            >
                <img src="/logo.png" class="h-8" alt="logo" />
                <img src="/typo.png" class="h-7" alt="typo" />
            </Link>
            <div class="block xl:hidden absolute right-2 top-4">
                <el-button
                    type="danger"
                    plain
                    size="small"
                    @click="closeSidebar"
                >
                    <el-icon><Close /></el-icon>
                </el-button>
            </div>
        </div>

        <div class="w-full space-y-1 z-40">
            <el-menu
                unique-opened
                :default-active="$page.url"
                class="el-menu-vertical-demo"
                @select="onMenuSelect"
                mode="vertical"
            >
                <MenuItem :menuList="menu" />
            </el-menu>
        </div>
    </el-scrollbar>
</template>

<script>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";
import { computed } from "vue";
import { sidebarItem } from "@/Hooks/menu";
import MenuItem from "./MenuItem.vue";
import { Close } from "@element-plus/icons-vue";
export default {
    props: ["show"],
    components: {
        ApplicationLogo,
        NavLink,
        Close,
        MenuItem,
    },
    emits: ["closed", "menuSelect"],
    setup(props, { emit }) {
        const onMenuSelect = (index) => {
            emit("menuSelect", index);
        };

        const closeSidebar = () => {
            emit("closed");
        };

        return {
            closeSidebar,
            menu: computed(() => [...sidebarItem]),
            onMenuSelect,
        };
    },
};
</script>
