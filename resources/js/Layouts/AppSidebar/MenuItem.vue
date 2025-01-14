<template>
    <template v-for="item in menuList">
        <el-sub-menu
            v-if="item.type == 'group' && item.permission"
            :key="item.url"
            :index="item.id"
        >
            <template v-slot:title>
                <span v-html="item.icon"></span>

                <span class="ml-2.5">{{ item.name }}</span>
            </template>

            <MenuItem :menuList="item.children"> </MenuItem>
        </el-sub-menu>

        <el-menu-item
            v-if="item.type == 'page' && item.permission"
            :key="item.id"
            :index="item.url"
            @click="onSelectItem(item)"
        >
            <template v-if="item.icon">
                <span v-html="item.icon" class="ml-px"></span>
            </template>
            <template v-else>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="8"
                    height="8"
                    viewBox="0 0 24 24"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="7"
                        fill="currentColor"
                        opacity=".5"
                    />
                    <path
                        fill="currentColor"
                        fill-rule="evenodd"
                        d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10m0-3a7 7 0 1 0 0-14a7 7 0 0 0 0 14"
                        clip-rule="evenodd"
                    />
                </svg>
            </template>
            <template #title>
                <span class="ml-2.5">{{ item.name }}</span>
                <!-- <div class="noti" v-if="item.name == 'Deposit'">
                      {{ depositNotiCount > 99 ? "99+" : depositNotiCount }}
                  </div>
  
                  <div class="noti" v-if="item.name == 'Withdraw'">
                      {{ withdrawNotiCount > 99 ? "99+" : withdrawNotiCount }}
                  </div> -->
            </template>
        </el-menu-item>
    </template>
</template>

<script>
import { Document } from "@element-plus/icons-vue";
import { router, usePage } from "@inertiajs/vue3";
export default {
    props: ["menuList"],
    components: {
        Document,
    },
    setup() {
        const page = usePage();
        const onSelectItem = (item) => {
            if (page.url === item.url) return;

            router.get(item.url);
        };

        return {
            onSelectItem,
        };
    },
};
</script>

<style></style>
