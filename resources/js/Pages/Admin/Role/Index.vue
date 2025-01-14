<template>
    <Head title="Roles" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">Roles</h4>
            <div class="p-4 rounded shadow-sm border index-content">
                <div
                    class="flex items-center justify-between gap-4 xl:gap-0 mt-2 mb-5"
                >
                    <div class="flex items-center gap-3">
                        <el-button type="primary" @click="addNew">
                            <el-icon><Plus /></el-icon>
                            <span>New</span>
                        </el-button>
                    </div>

                    <div>
                        <el-input
                            v-model="param.search"
                            style="width: 200px"
                            placeholder="Search Roles"
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
                        :data="roles.data"
                        table-layout="auto"
                        :default-sort="{ prop: 'id', order: 'descending' }"
                    >
                        <el-table-column prop="id" label="ID" sortable />
                        <el-table-column label="Name" sortable>
                            <template #default="scope">
                                <h5 class="font-semibold">
                                    {{ scope.row.name }}
                                </h5>
                            </template>
                        </el-table-column>
                        <el-table-column label="Permissions">
                            <template #default="scope">
                                <el-tag size="small">{{
                                    scope.row.permissions
                                }}</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Actions">
                            <template #default="scope">
                                <el-tooltip
                                    class="box-item"
                                    content="Edit"
                                    placement="top"
                                >
                                    <el-button
                                        type="warning"
                                        circle
                                        style="margin-bottom: 5px"
                                        @click="handleEdit(scope.row)"
                                    >
                                        <el-icon><Edit /></el-icon>
                                    </el-button>
                                </el-tooltip>
                                <!-- <el-tooltip
                                    class="box-item"
                                    content="Delete"
                                    placement="top"
                                >
                                    <el-button
                                        v-if="!scope.row.users.length"
                                        type="danger"
                                        circle
                                        style="margin-bottom: 5px"
                                        @click="handleDelete(scope.row.id)"
                                    >
                                        <el-icon><Delete /></el-icon>
                                    </el-button>
                                </el-tooltip> -->
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
                            :total="roles.total"
                        />
                    </div>
                </div>
            </div>
        </div>
        <Dialog
            :show="showDialog"
            @closed="closeDialog"
            :title="dialog.dialogTitle"
            :data="dialog.dialogData"
            :permissions="permissions"
        />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash.debounce";
import { reactive, toRefs, watch } from "vue";
import Dialog from "./Dialog.vue";
import {
    Plus,
    Edit,
    Refresh,
    Filter,
    Checked,
    Delete,
} from "@element-plus/icons-vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["roles", "permissions"],
    components: {
        AuthenticatedLayout,
        Dialog,
        Plus,
        Edit,
        Refresh,
        Filter,
        Checked,
        Delete,
    },
    setup() {
        const state = reactive({
            showDialog: false,
            isLoading: false,
            dialog: {
                dialogTitle: "",
                dialogData: {},
            },
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

        const addNew = () => {
            state.dialog.dialogTitle = "Create";
            state.dialog.dialogData = {};
            state.showDialog = true;
        };

        const handleEdit = (row) => {
            state.dialog.dialogTitle = "Edit";
            state.dialog.dialogData = JSON.parse(JSON.stringify(row));
            state.showDialog = true;
        };

        const handleDelete = (id) => {
            ElMessageBox.confirm(
                "Are you sure you want to delete?",
                "Warning",
                {
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    type: "warning",
                    draggable: true,
                    closeOnClickModal: false,
                }
            )
                .then(() => {
                    router.delete(route("admin.roles.destroy", id), {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: (page) => {
                            ElMessage.success(page.props.flash.success);
                        },
                        onError: (page) => {
                            ElMessage.error(page.error);
                        },
                    });
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
            router.get("/admin/roles", state.param, {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onFinish: () => {
                    state.isLoading = false;
                },
            });
        };

        const closeDialog = () => {
            state.showDialog = false;
        };

        const reset = () => {
            router.get(route("admin.roles.index"));
        };

        return {
            ...toRefs(state),
            addNew,
            handleEdit,
            handleDelete,
            onSizeChange,
            onCurrentChange,
            closeDialog,
            reset,
        };
    },
};
</script>

<style></style>
