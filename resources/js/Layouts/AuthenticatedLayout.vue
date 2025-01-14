<template>
    <section>
        <div class="flex">
            <!-- sidebar -->
            <aside
                :class="show ? 'ml-0' : '-ml-80 xl:-ml-64 2xl:-ml-72 w-0'"
                class="sidebar fixed top-0 bottom-0 left-0 z-50 w-[70%] md:w-[30%] xl:w-[18%] 2xl:w-[15%] h-screen border-r shadow-sm transition-all duration-300"
            >
                <AppSidebar :show="show" @closed="show = false" />
            </aside>
            <main
                :class="
                    show
                        ? 'ml-0 xl:ml-64 2xl:ml-72 xl:w-[82%] 2xl:w-[85%]'
                        : 'ml-0'
                "
                class="transition-all duration-300 w-full"
            >
                <!-- Header -->
                <div
                    class="page-header shadow-sm sticky top-0 z-40 border-b"
                >
                    <AppHeader @show-event="showSidebar" />
                </div>

                <slot />
            </main>
        </div>
    </section>
</template>

<script>
import { onMounted, reactive, toRefs } from "vue";
import AppHeader from "./AppHeader.vue";
import AppSidebar from "./AppSidebar/Index.vue";
export default {
    components: {
        AppSidebar,
        AppHeader,
    },

    setup() {
        const state = reactive({
            show: true,
            width: document.documentElement.clientWidth,
        });

        const showSidebar = () => {
            state.show = !state.show;
        };

        onMounted(() => {
            if (state.width <= 1024) {
                state.show = false;
            }
        });

        return {
            ...toRefs(state),
            showSidebar,
        };
    },
};
</script>

<style></style>
