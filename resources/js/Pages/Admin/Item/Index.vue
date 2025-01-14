<template>
    <Head title="Items" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">Items</h4>
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
                            placeholder="Search Items"
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
                        :data="items.data"
                        table-layout="fixed"
                        :default-sort="{ prop: 'id', order: 'descending' }"
                    >
                        <el-table-column prop="id" label="ID" sortable />
                        <el-table-column label="Image">
                            <template #default="scope">
                                <img
                                    v-if="scope.row.image != ''"
                                    :src="scope.row.image"
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
                        <el-table-column label="Price" sortable align="center">
                            <template #default="scope">
                                <el-tag type="primary" class="mr-1"
                                    >{{ scope.row.price }}
                                    {{ scope.row.currency }}</el-tag
                                >
                                <el-tag type="danger"
                                    >{{ scope.row.th_price }}
                                    {{ scope.row.th_currency }}</el-tag
                                >
                            </template>
                        </el-table-column>
                        <el-table-column
                            prop="created_at"
                            label="Created At"
                            sortable
                            align="center"
                        />
                        <el-table-column label="Available" align="center">
                            <template #default="scope">
                                <el-switch
                                    v-model="scope.row.disabled"
                                    :active-value="0"
                                    :inactive-value="1"
                                    @change="changeStatus(scope.row)"
                                />
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
                            :total="items.total"
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
            :currencies="currencies"
        />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash.debounce";
import { reactive, toRefs, watch } from "vue";
import Dialog from "./Dialog.vue";
import { Plus, Edit, Refresh, Filter, Delete } from "@element-plus/icons-vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["items", "currencies"],
    components: {
        AuthenticatedLayout,
        Dialog,
        Plus,
        Edit,
        Refresh,
        Filter,
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

        const changeStatus = (row) => {
            ElMessageBox.confirm(
                "Are you sure want to change status of this item?",
                "Confirmation",
                {
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    type: "warning",
                    draggable: true,
                    closeOnClickModal: false,
                }
            )
                .then(() => {
                    router.patch(
                        route("admin.items.change-status", row.id),
                        {},
                        {
                            preserveState: true,
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
                    router.reload();
                    ElMessage({
                        type: "info",
                        message: "Cancel",
                    });
                });
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
                    router.delete(route("admin.items.destroy", id), {
                        onSuccess: (page) => {
                            ElMessage.success(page.props.flash.success);
                        },
                        onError: (page) => {
                            ElMessage.error(page.props.flash.error);
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
            router.get("/admin/items", state.param, {
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
            router.get(route("admin.items.index"));
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
            changeStatus,
        };
    },
};
</script>

<style></style>
