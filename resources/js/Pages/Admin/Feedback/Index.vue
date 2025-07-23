<template>
    <Head title="Feedback" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">Feedback</h4>
            <div class="p-4 rounded shadow-sm border index-content">
                <div
                    class="flex items-center justify-end gap-4 xl:gap-0 mt-2 mb-5"
                >
                    <div>
                        <el-select
                            v-model="param.type"
                            placeholder="Select"
                            filterable
                            style="width: 240px"
                        >
                            <el-option
                                v-for="item in types"
                                :key="item.slug"
                                :label="item.name"
                                :value="item.slug"
                            />
                        </el-select>
                        <el-input
                            v-model="param.search"
                            style="width: 200px"
                            placeholder="Search Feedback"
                            class="ml-3"
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
                        :data="feedback.data"
                        table-layout="fixed"
                        :default-sort="{ prop: 'id', order: 'descending' }"
                    >
                        <el-table-column prop="id" label="ID" sortable />
                        <el-table-column label="Name" sortable>
                            <template #default="scope">
                                <h5 class="font-semibold">
                                    {{ scope.row.name }}
                                </h5>
                                <small>{{ scope.row.email }}</small>
                            </template>
                        </el-table-column>
                        <el-table-column label="Rating">
                            <template #default="scope">
                                <el-rate
                                    v-model="scope.row.rating"
                                    disabled
                                    show-text
                                    show-score
                                    :max="5"
                                />
                            </template>
                        </el-table-column>
                        <el-table-column label="Type">
                            <template #default="scope">
                                <el-tag effect="plain" type="info">
                                    {{ scope.row.type }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="message" label="Message" />
                        <el-table-column
                            prop="created_at"
                            label="Created At"
                            sortable
                            align="center"
                        />
                        <!-- <el-table-column label="Actions">
                            <template #default="scope">
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
                        </el-table-column> -->
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
                            :total="feedback.total"
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
import { Refresh, Filter, Delete } from "@element-plus/icons-vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["feedback"],
    components: {
        AuthenticatedLayout,
        Refresh,
        Filter,
        Delete,
    },
    setup() {
        const state = reactive({
            isLoading: false,
            pageList: [10, 20, 60, 80, 100],
            param: {
                page: 1,
                page_size: 10,
                search: "",
                type: "",
            },
            types: [
                { id: 1, name: "All", slug: "all" },
                { id: 2, name: "General Feedback", slug: "feedback" },
                { id: 3, name: "Suggestion", slug: "suggestion" },
                { id: 4, name: "Service Quality", slug: "service" },
                { id: 5, name: "Reading Accuracy", slug: "reading" },
                { id: 6, name: "Website Experience", slug: "website" },
            ],
        });

        const onSizeChange = (val) => {
            state.param.page_size = val;
            getData();
        };

        const onCurrentChange = (val) => {
            state.param.page = val;
            getData();
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
                    router.delete(route("admin.feedback.destroy", id), {
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

        watch(
            () => state.param.type,
            debounce(() => {
                getData();
            }, 500)
        );

        const getData = () => {
            state.isLoading = true;
            router.get("/admin/feedback", state.param, {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onFinish: () => {
                    state.isLoading = false;
                },
            });
        };

        const reset = () => {
            router.get(route("admin.feedback"));
        };

        return {
            ...toRefs(state),
            handleDelete,
            onSizeChange,
            onCurrentChange,
            reset,
        };
    },
};
</script>

<style></style>
