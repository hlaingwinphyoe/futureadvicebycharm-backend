<template>
    <Head title="Posts" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">Posts</h4>
            <div class="p-4 rounded shadow-sm border index-content">
                <div
                    class="flex items-center justify-between gap-4 xl:gap-0 mt-2 mb-5"
                >
                    <div class="flex items-center gap-3">
                        <Link :href="route('admin.posts.create')">
                            <el-button type="primary">
                                <el-icon><Plus /></el-icon>
                                <span>New</span>
                            </el-button>
                        </Link>
                    </div>

                    <div>
                        <el-input
                            v-model="param.search"
                            style="width: 200px"
                            placeholder="Search Posts"
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
                        :data="posts.data"
                        table-layout="auto"
                        :default-sort="{ prop: 'id', order: 'descending' }"
                    >
                        <el-table-column prop="id" label="ID" sortable />
                        <el-table-column label="Poster">
                            <template #default="scope">
                                <img
                                    v-if="scope.row.poster"
                                    :src="scope.row.poster"
                                    class="h-8 w-10 object-cover rounded"
                                    :alt="scope.row.title"
                                />
                            </template>
                        </el-table-column>
                        <el-table-column label="Title" sortable>
                            <template #default="scope">
                                <h5 class="font-semibold">
                                    {{ scope.row.title }}
                                </h5>
                            </template>
                        </el-table-column>
                        <el-table-column label="Category">
                            <template #default="scope">
                                <el-tag type="primary">{{
                                    scope.row.cate_name
                                }}</el-tag>
                            </template>
                        </el-table-column>
                        <!-- <el-table-column label="Description">
                            <template #default="scope">
                                <h5
                                    class="font-semibold"
                                    v-html="scope.row.excerpt"
                                ></h5>
                            </template>
                        </el-table-column> -->
                        <el-table-column prop="owner" label="Author" />
                        <el-table-column
                            prop="created_at"
                            label="Created At"
                            sortable
                            align="center"
                        />
                        <el-table-column label="Available" align="center">
                            <template #default="scope">
                                <el-switch
                                    v-model="scope.row.is_published"
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
                                    <Link
                                        :href="
                                            route(
                                                'admin.posts.edit',
                                                scope.row.id
                                            )
                                        "
                                        class="mr-3"
                                    >
                                        <el-button
                                            type="warning"
                                            circle
                                            style="margin-bottom: 5px"
                                        >
                                            <el-icon><Edit /></el-icon>
                                        </el-button>
                                    </Link>
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
                            :total="posts.total"
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
import { Plus, Edit, Refresh, Filter, Delete } from "@element-plus/icons-vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["posts", "page", "pageSize"],
    components: {
        AuthenticatedLayout,
        Plus,
        Edit,
        Refresh,
        Filter,
        Delete,
    },
    setup(props) {
        const state = reactive({
            isLoading: false,
            pageList: [10, 20, 60, 80, 100],
            param: {
                page: props.page,
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
                        route("admin.posts.change-status", row.id),
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
                    router.delete(route("admin.posts.destroy", id), {
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
            router.get("/admin/posts", state.param, {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onFinish: () => {
                    state.isLoading = false;
                },
            });
        };

        const reset = () => {
            router.get(route("admin.posts.index"));
        };

        return {
            ...toRefs(state),
            handleDelete,
            onSizeChange,
            onCurrentChange,
            reset,
            changeStatus,
        };
    },
};
</script>

<style></style>
