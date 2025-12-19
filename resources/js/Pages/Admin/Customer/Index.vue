<template>
    <Head title="Customers" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">Customers</h4>
            <div class="p-4 rounded shadow-sm border index-content">
                <div
                    class="flex items-center justify-end gap-4 xl:gap-0 mt-2 mb-5"
                >
                    <div>
                        <el-input
                            v-model="param.search"
                            style="width: 200px"
                            placeholder="Search Customers"
                        />
                        <el-button type="danger" @click="reset" class="ml-3">
                            <el-icon>
                                <Refresh />
                            </el-icon>
                        </el-button>
                    </div>
                </div>

                <div v-loading="isLoading" element-loading-text="Loading...">
                    <el-table
                        :data="customers.data"
                        table-layout="fixed"
                        :default-sort="{ prop: 'id', order: 'descending' }"
                    >
                        <el-table-column prop="id" label="ID" sortable />
                        <el-table-column label="Profile">
                            <template #default="scope">
                                <img
                                    v-if="scope.row.profile"
                                    :src="scope.row.profile"
                                    class="h-10 object-cover rounded-md"
                                    :alt="scope.row.name"
                                />
                            </template>
                        </el-table-column>
                        <el-table-column label="Name" sortable>
                            <template #default="scope">
                                <h5 class="font-semibold">
                                    {{ scope.row.name }}
                                </h5>
                            </template>
                        </el-table-column>
                        <el-table-column prop="email" label="Email" />
                        <el-table-column prop="phone" label="Phone" />
                        <el-table-column label="Status">
                            <template #default="scope">
                                <el-tag
                                    plain
                                    :type="
                                        scope.row.disabled == false
                                            ? 'success'
                                            : 'danger'
                                    "
                                >
                                    {{
                                        scope.row.disabled ? "Banned" : "Active"
                                    }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="created_at" label="Joined At" />
                        <el-table-column label="Actions">
                            <template #default="scope">
                                <el-tooltip
                                    class="box-item"
                                    content="Detail"
                                    placement="top"
                                >
                                    <el-button
                                        type="warning"
                                        circle
                                        style="margin-bottom: 5px"
                                        @click="handleView(scope.row.id)"
                                    >
                                        <el-icon><View /></el-icon>
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip
                                    class="box-item"
                                    content="Ban this user"
                                    placement="top"
                                >
                                    <el-button
                                        :type="
                                            scope.row.disabled == true
                                                ? 'success'
                                                : 'danger'
                                        "
                                        circle
                                        style="margin-bottom: 5px"
                                        @click="handleBan(scope.row.id)"
                                    >
                                        <el-icon
                                            v-if="scope.row.disabled == true"
                                        >
                                            <RefreshRight />
                                        </el-icon>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M12 3a9 9 0 1 0 9 9a9 9 0 0 0-9-9m-7.5 9a7.44 7.44 0 0 1 1.7-4.74L16.74 17.8A7.49 7.49 0 0 1 4.5 12m13.3 4.74L7.26 6.2A7.49 7.49 0 0 1 17.8 16.74"
                                            />
                                        </svg>
                                    </el-button>
                                </el-tooltip>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="my-5 flex items-center justify-center">
                        <el-pagination
                            @size-change="onSizeChange"
                            @current-change="onCurrentChange"
                            :page-size="param.page_size"
                            :background="true"
                            :page-sizes="pageList"
                            :current-page="param.page"
                            :layout="`total,sizes,prev,pager,next,jumper`"
                            :total="customers.total"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash.debounce";
import { reactive, toRefs, watch } from "vue";
import { Refresh, Filter, RefreshRight, View } from "@element-plus/icons-vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["customers"],
    components: {
        AuthenticatedLayout,
        Refresh,
        Filter,
        RefreshRight,
        View,
    },
    setup() {
        const state = reactive({
            isLoading: false,
            pageList: [10, 20, 60, 80, 100],
            param: {
                page: 1,
                page_size: 10,
                search: "",
            },
        });

        const onSizeChange = (val) => {
            state.param.page_size = val;
            getData();
        };

        const onCurrentChange = (val) => {
            state.param.page = val;
            getData();
        };

        const handleView = (id) => {
            router.visit(route("admin.customers.show", id));
        };

        const handleBan = (id) => {
            ElMessageBox.confirm("Are you sure?", "Warning", {
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
                type: "warning",
                draggable: true,
                closeOnClickModal: false,
            })
                .then(() => {
                    router.patch(
                        route("admin.customers.banned", id),
                        {},
                        {
                            onSuccess: (page) => {
                                ElMessage.success(page.props.flash.success);
                            },
                            onError: (page) => {
                                ElMessage.error(page.props.flash.error);
                            },
                        }
                    );
                })
                .catch(() => {
                    ElMessage({
                        type: "info",
                        message: "Cancel",
                    });
                });
        };

        watch(
            () => state.param.search,
            debounce(() => {
                getData();
            }, 500)
        );

        const getData = () => {
            state.isLoading = true;
            router.get("/admin/customers", state.param, {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onFinish: () => {
                    state.isLoading = false;
                },
            });
        };

        const reset = () => {
            router.get(route("admin.customers.index"));
        };

        return {
            ...toRefs(state),
            handleView,
            handleBan,
            onSizeChange,
            onCurrentChange,
            reset,
        };
    },
};
</script>

<style></style>
