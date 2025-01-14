<template>
    <el-dialog
        :modelValue="show"
        @update:modelValue="show = $event"
        @close="closeDialog(formRef)"
        @open="openDialog"
        draggable
        :title="dialogTitle"
        :close-on-click-modal="false"
        width="550"
    >
        <el-form label-width="120px" ref="formRef" :model="form">
            <el-form-item
                label="Acc Name"
                prop="acc_name"
                :rules="[
                    {
                        required: true,
                        message: 'Acc Name is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-input v-model="form.acc_name" />
                <InputError
                    class="mt-2"
                    :message="$page.props.errors.acc_name"
                />
            </el-form-item>
            <el-form-item
                label="Acc No."
                prop="acc_no"
                :rules="[
                    {
                        required: true,
                        message: 'Acc Number is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-input v-model="form.acc_no" />
                <InputError class="mt-2" :message="$page.props.errors.acc_no" />
            </el-form-item>
            <el-form-item
                label="Type"
                prop="type"
                :rules="[
                    {
                        required: true,
                        message: 'Type is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-input v-model="form.type" />
                <InputError class="mt-2" :message="$page.props.errors.type" />
            </el-form-item>
            <el-form-item label="Image">
                <div class="relative group">
                    <img
                        v-show="imgSrc != ''"
                        id="preview_img"
                        class="upload-card object-contain mr-2.5"
                        :src="imgSrc"
                    />
                    <span
                        class="absolute-center hidden group-hover:flex group-hover:bg-black group-hover:bg-opacity-50 w-[148px] h-[148px] text-white items-center justify-center rounded-md"
                        @click="handleRemove(data)"
                    >
                        <el-icon size="large" class="cursor-pointer"
                            ><Delete
                        /></el-icon>
                    </span>
                </div>
                <div class="upload-card" @click="selectImage">
                    <h4>
                        <el-icon size="large"><Plus /></el-icon>
                    </h4>

                    <input
                        type="file"
                        class="hidden"
                        name="image"
                        id="upload"
                        @change="loadFile"
                        accept="image/*"
                    />
                </div>
                <InputError class="mt-2" :message="$page.props.errors.image" />
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeDialog(formRef)">Cancel</el-button>
                <el-button
                    type="primary"
                    @click="submitDialog(formRef)"
                    :class="{ 'opacity-25': isLoading }"
                    :disabled="isLoading"
                >
                    Save
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import { reactive, ref, toRefs } from "vue";
import { ElMessage } from "element-plus";
import InputError from "@/Components/InputError.vue";
import { Plus, Delete } from "@element-plus/icons-vue";
import { router } from "@inertiajs/vue3";
export default {
    props: ["show", "title", "data"],
    components: {
        InputError,
        Plus,
        Delete,
    },
    emits: ["closed"],
    setup(props, context) {
        const state = reactive({
            dialogTitle: "",
            isLoading: false,
            virtualForm: new FormData(),
            imgSrc: "",
            form: {
                acc_name: "",
                acc_no: "",
                type: "",
            },
        });

        const formRef = ref();

        const submitDialog = (formRef) => {
            state.isLoading = true;
            formRef.validate((valid) => {
                if (valid) {
                    state.virtualForm.append("acc_name", state.form.acc_name);
                    state.virtualForm.append("acc_no", state.form.acc_no);
                    state.virtualForm.append("type", state.form.type);
                    if (state.dialogTitle === "Create") {
                        router.post(
                            route("admin.banks.store"),
                            state.virtualForm,
                            {
                                onSuccess: (page) => {
                                    state.isLoading = false;
                                    ElMessage.success(page.props.flash.success);
                                    formRef.resetFields();
                                    closeDialog(formRef);
                                },
                                onError: () => {
                                    state.isLoading = false;
                                    formRef.resetFields();
                                    ElMessage.error(page.error);
                                },
                            }
                        );
                    } else {
                        state.virtualForm.append("_method", "patch");
                        router.post(
                            route("admin.banks.update", props.data.id),
                            state.virtualForm,
                            {
                                onSuccess: (page) => {
                                    state.isLoading = false;
                                    ElMessage.success(page.props.flash.success);
                                    formRef.resetFields();
                                    closeDialog(formRef);
                                },
                                onError: (page) => {
                                    state.isLoading = false;
                                    formRef.resetFields();
                                    ElMessage.error(page.error);
                                },
                            }
                        );
                    }
                }
            });
        };

        // upload Image
        const selectImage = () => {
            var upload = document.getElementById("upload");
            upload.click();
        };

        const loadFile = (event) => {
            var input = event.target;
            var file = input.files[0];

            state.imgSrc = URL.createObjectURL(file);

            state.virtualForm.append("image", file);

            var output = document.getElementById("preview_img");
            output.src = URL.createObjectURL(file);
            output.onload = function () {
                URL.revokeObjectURL(output.src); // free memory
            };
        };

        const closeDialog = (formRef) => {
            formRef.resetFields();
            context.emit("closed");
        };

        const handleRemove = (uploadFile) => {
            router.delete(route("admin.banks.destroy-media", uploadFile.id), {
                onSuccess: () => {
                    state.imgSrc = "";
                },
            });
        };

        const openDialog = () => {
            state.dialogTitle = props.title;
            state.form.acc_name = props.data.acc_name ?? "";
            state.form.acc_no = props.data.acc_no ?? "";
            state.form.type = props.data.type ?? "";
            state.imgSrc = props.data.image ?? "";
        };

        return {
            ...toRefs(state),
            openDialog,
            formRef,
            closeDialog,
            submitDialog,
            selectImage,
            loadFile,
            handleRemove,
        };
    },
};
</script>

<style></style>
