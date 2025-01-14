<template>
    <Head title="Staffs" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">Staffs</h4>
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
                            placeholder="Search Staffs"
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
                        :data="staffs.data"
                        table-layout="fixed"
                        :default-sort="{ prop: 'id', order: 'descending' }"
                    >
                        <!-- <el-table-column prop="id" label="ID" sortable /> -->
                        <el-table-column label="Name" sortable>
                            <template #default="scope">
                                <h5
                                    class="font-semibold flex items-center gap-2"
                                >
                                    <el-avatar
                                        :src="scope.row.profile"
                                        :size="30"
                                    />
                                    {{ scope.row.name }}
                                </h5>
                            </template>
                        </el-table-column>
                        <el-table-column prop="email" label="Email" />
                        <el-table-column prop="role" label="Role" />
                        <el-table-column prop="phone" label="Phone" />
                        <el-table-column label="Actions">
                            <template #default="scope">
                                <el-tooltip
                                    class="box-item"
                                    content="Change Password"
                                    placement="top"
                                >
                                    <el-button
                                        circle
                                        style="margin-bottom: 5px"
                                        @click="handleChangePassword(scope.row)"
                                    >
                                        <el-icon><Lock /></el-icon>
                                    </el-button>
                                </el-tooltip>
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
                                <el-tooltip
                                    class="box-item"
                                    content="Delete"
                                    placement="top"
                                >
                                    <el-button
                                        type="danger"
                                        circle
                                        style="margin-bottom: 5px"
                                        @click="handleDelete(scope.row.id)"
                                    >
                                        <el-icon><Delete /></el-icon>
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
                            :total="staffs.total"
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
            :roles="roles"
        />

        <ChangePassword
            :show="showChangePassword"
            @closed="closeDialog"
            :title="dialog.dialogTitle"
            :data="dialog.dialogData"
        />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash.debounce";
import { reactive, toRefs, watch } from "vue";
import Dialog from "./Dialog.vue";
import ChangePassword from "./ChangePassword.vue";
import {
    Plus,
    Edit,
    Refresh,
    Filter,
    Checked,
    Delete,
    Lock,
} from "@element-plus/icons-vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["staffs", "roles"],
    components: {
        AuthenticatedLayout,
        Dialog,
        Plus,
        Edit,
        Refresh,
        Filter,
        Checked,
        Delete,
        Lock,
        ChangePassword,
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
            showChangePassword: false,
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

        const handleChangePassword = (row) => {
            state.showChangePassword = true;
            state.dialog.dialogTitle = "Change Password";
            state.dialog.dialogData = JSON.parse(JSON.stringify(row));
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
                    router.delete(route("admin.staffs.destroy", id), {
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
            router.get("/admin/staffs", state.param, {
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
            state.showChangePassword = false;
        };

        const reset = () => {
            router.get(route("admin.staffs.index"));
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
            handleChangePassword,
        };
    },
};
</script>

<style></style>
