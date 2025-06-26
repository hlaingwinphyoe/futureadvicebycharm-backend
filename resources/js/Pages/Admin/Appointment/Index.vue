<template>
    <Head title="Today Appointments" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">Today Appointments</h4>
            <div class="p-4 rounded shadow-sm border index-content">
                <div
                    class="flex items-center justify-end gap-4 xl:gap-0 mt-2 mb-5"
                >
                    <div>
                        <el-input
                            v-model="param.search"
                            style="width: 200px"
                            placeholder="Search Appointments"
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
                        :data="appointments.data"
                        table-layout="fixed"
                        :default-sort="{
                            prop: 'appointment_no',
                            order: 'descending',
                        }"
                    >
                        <el-table-column
                            prop="appointment_no"
                            label="Book No."
                            sortable
                        />
                        <el-table-column
                            prop="customer_name"
                            label="Cus: Name"
                            sortable
                        />
                        <el-table-column prop="desc" label="Desc" />
                        <el-table-column label="Total Price">
                            <template #default="scope">
                                {{ scope.row.total_price }} Ks
                            </template>
                        </el-table-column>
                        <el-table-column label="Status">
                            <template #default="scope">
                                <el-tag
                                    :type="
                                        scope.row.status == 'Incomplete'
                                            ? 'danger'
                                            : scope.row.status == 'Approved'
                                            ? 'success'
                                            : 'warning'
                                    "
                                    >{{ scope.row.status }}</el-tag
                                >
                            </template>
                        </el-table-column>
                        <el-table-column
                            prop="created_at"
                            label="Booked Date"
                            sortable
                        />
                        <el-table-column label="Actions">
                            <template #default="scope">
                                <el-tooltip
                                    class="box-item"
                                    content="Detail"
                                    placement="top"
                                >
                                    <el-button
                                        circle
                                        style="margin-bottom: 5px"
                                        @click="handleView(scope.row.id)"
                                    >
                                        <el-icon><View /></el-icon>
                                    </el-button>
                                </el-tooltip>
                                <!-- <el-tooltip
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
                                </el-tooltip> -->
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
                            :total="appointments.total"
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
import {
    Plus,
    Edit,
    Refresh,
    Filter,
    Delete,
    View,
} from "@element-plus/icons-vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["appointments"],
    components: {
        AuthenticatedLayout,
        Plus,
        Edit,
        Refresh,
        Filter,
        Delete,
        View,
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

        const handleView = (id) => {
            router.visit(route("admin.appointments.show", id));
        };

        const addNew = () => {
            state.dialog.dialogTitle = "Create";
            state.dialog.dialogData = {};
            state.showDialog = true;
        };

        watch(
            () => state.param.search,
            debounce(() => {
                getData();
            }, 500)
        );

        const getData = () => {
            state.isLoading = true;
            router.get("/admin/appointments", state.param, {
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
            router.get(route("admin.appointments.index"));
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
                    router.delete(route("admin.appointments.destroy", id), {
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

        return {
            ...toRefs(state),
            addNew,
            handleView,
            onSizeChange,
            onCurrentChange,
            closeDialog,
            reset,
            handleDelete,
        };
    },
};
</script>

<style></style>
