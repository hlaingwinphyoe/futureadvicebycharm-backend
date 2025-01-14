<template>
    <Head :title="'Details - ' + customer ? customer.name : ''" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <div class="flex items-center">
                <Link :href="$page.props.previous">
                    <el-button type="info" plain>
                        <el-icon><Back /></el-icon>
                    </el-button>
                </Link>
                <h4 class="text-lg font-bold ml-2">
                    Details of {{ customer ? customer.name : "" }}
                </h4>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Back } from "@element-plus/icons-vue";
export default {
    props: ["customer"],
    components: {
        AuthenticatedLayout,
        Back,
    },
};
</script>

<style></style>
