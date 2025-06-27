<template>
    <el-dialog
        :modelValue="show"
        @update:modelValue="show = $event"
        @close="closeDialog(formRef)"
        @open="openDialog"
        draggable
        :title="dialogTitle"
        :close-on-click-modal="false"
        width="600"
    >
        <ul
            :class="remarks.length > 0 ? 'mb-7' : 'mb-2'"
            class="ml-5 list-disc space-y-2"
        >
            <li
                v-for="remark in remarks"
                :key="remark.id"
                class="flex items-center gap-2"
            >
                {{ remark.name }}
                <el-button
                    type="danger"
                    size="small"
                    @click="confirmDeleteRemark(remark)"
                >
                    Delete
                </el-button>
            </li>
        </ul>
        <el-form
            ref="formRef"
            label-width="100px"
            :model="form"
            label-position="top"
            class="flex items-center gap-5"
        >
            <el-form-item
                label="Name"
                prop="name"
                :rules="[
                    {
                        required: true,
                        message: 'Name is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-input
                    v-model="form.name"
                    style="width: 350px"
                    placeholder="Enter Remark"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </el-form-item>
            <el-form-item label="Image Input" prop="is_image">
                <el-switch
                    v-model="form.is_image"
                    :active-value="1"
                    :inactive-value="0"
                />
                <InputError class="mt-2" :message="form.errors.is_image" />
            </el-form-item>
            <el-button
                type="primary"
                @click="submitDialog(formRef)"
                class="mt-2.5"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </el-button>
        </el-form>
    </el-dialog>
</template>

<script>
import { useForm, router } from "@inertiajs/vue3";
import { reactive, ref, toRefs } from "vue";
import InputError from "@/Components/InputError.vue";
import { ElMessage, ElMessageBox } from "element-plus";

export default {
    props: ["show", "title", "data"],
    components: {
        InputError,
    },
    emits: ["closed"],
    setup(props, context) {
        const state = reactive({
            dialogTitle: "",
            remarks: [],
        });
        const formRef = ref();
        const form = useForm({
            name: "",
            is_image: 0,
        });

        const submitDialog = (formRef) => {
            formRef.validate((valid) => {
                if (valid) {
                    form.post(
                        route("admin.packages.add-remarks", props.data.id),
                        {
                            onSuccess: (page) => {
                                form.reset();
                                form.clearErrors();
                                formRef.resetFields();
                                getRemarks();
                                ElMessage.success(page.props.flash.success);
                            },
                            onError: (page) => {
                                ElMessage.success(page.props.flash.error);
                            },
                        }
                    );
                }
            });
        };

        const closeDialog = (formRef) => {
            form.reset();
            form.clearErrors();
            formRef.resetFields();
            context.emit("closed");
        };

        const openDialog = () => {
            state.dialogTitle = props.title;
            getRemarks();
        };

        const getRemarks = () => {
            axios
                .get(route("admin.packages.get-remarks", props.data.id))
                .then((res) => {
                    state.remarks = res.data.remarks;
                })
                .catch((err) => {
                    console.log(err);
                });
        };

        const deleteRemark = (remark) => {
            router.delete(
                route("admin.packages.delete-remark", {
                    package: props.data.id,
                    remark: remark.id,
                }),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        ElMessage.success("Remark deleted");
                        getRemarks();
                    },
                    onError: () => {
                        ElMessage.error("Failed to delete remark");
                    },
                }
            );
        };

        const confirmDeleteRemark = (remark) => {
            ElMessageBox.confirm(
                "Are you sure you want to delete this remark?",
                "Warning",
                {
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    type: "warning",
                }
            )
                .then(() => {
                    deleteRemark(remark);
                })
                .catch(() => {});
        };

        return {
            ...toRefs(state),
            formRef,
            form,
            closeDialog,
            openDialog,
            submitDialog,
            confirmDeleteRemark,
        };
    },
};
</script>

<style></style>
