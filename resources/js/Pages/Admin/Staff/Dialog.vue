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
                <el-input v-model="form.name" />
                <InputError class="mt-2" :message="$page.props.errors.name" />
            </el-form-item>
            <el-form-item
                label="Role"
                :rules="[
                    {
                        required: true,
                        message: 'Role is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-select
                    v-model="form.role"
                    placeholder="Select"
                    clearable
                    @clear="form.role = ''"
                >
                    <el-option
                        v-for="item in roles"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    />
                </el-select>
                <InputError class="mt-2" :message="$page.props.errors.role" />
            </el-form-item>
            <el-form-item label="Email" prop="email">
                <el-input type="email" v-model="form.email" />
                <InputError class="mt-2" :message="$page.props.errors.email" />
            </el-form-item>
            <el-form-item label="Phone" prop="phone">
                <el-input v-model="form.phone" />
                <InputError class="mt-2" :message="$page.props.errors.phone" />
            </el-form-item>
            <el-form-item label="Social Link" prop="social">
                <el-input v-model="form.social" />
                <InputError class="mt-2" :message="$page.props.errors.social" />
            </el-form-item>
            <el-form-item label="Profile">
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
    props: ["show", "title", "data", "roles"],
    components: {
        InputError,
        Plus,
        Delete,
    },
    emits: ["closed"],
    setup(props, context) {
        const state = reactive({
            dialogTitle: "",
            imgSrc: "",
            virtualForm: new FormData(),
            form: {
                name: "",
                role: "",
                phone: "",
                email: "",
                social_link: "",
            },
        });

        const formRef = ref();

        const submitDialog = (formRef) => {
            state.isLoading = true;
            formRef.validate((valid) => {
                if (valid) {
                    state.virtualForm.append("name", state.form.name);
                    state.virtualForm.append("role", state.form.role);
                    state.virtualForm.append("phone", state.form.phone);
                    state.virtualForm.append("email", state.form.email);
                    state.virtualForm.append(
                        "social_link",
                        state.form.social_link
                    );
                    if (state.dialogTitle === "Create") {
                        router.post(
                            route("admin.staffs.store"),
                            state.virtualForm,
                            {
                                onSuccess: (page) => {
                                    ElMessage.success(page.props.flash.success);
                                    closeDialog(formRef);
                                },
                                onError: () => {
                                    state.isLoading = false;
                                    ElMessage.error(page.error);
                                },
                            }
                        );
                    } else {
                        state.virtualForm.append("_method", "patch");
                        router.post(
                            route("admin.staffs.update", props.data.id),
                            state.virtualForm,
                            {
                                onSuccess: (page) => {
                                    closeDialog(formRef);
                                    ElMessage.success(page.props.flash.success);
                                },
                                onError: () => {
                                    state.isLoading = false;
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
            state.isLoading = false;
            formRef.resetFields();
            context.emit("closed");
        };

        const openDialog = () => {
            state.dialogTitle = props.title;
            state.form.name = props.data.name ?? "";
            state.form.role = props.data.role_id ?? "";
            state.form.phone = props.data.phone ?? "";
            state.form.email = props.data.email ?? "";
            state.imgSrc = props.data.profile ?? "";
        };

        const handleRemove = (uploadFile) => {
            router.delete(route("admin.staffs.destroy-media", uploadFile.id), {
                onSuccess: () => {
                    state.imgSrc = "";
                },
            });
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
