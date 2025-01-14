<template>
    <Head title="Edit Posts" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <div class="flex items-center">
                <Link :href="backurl">
                    <el-button type="info" plain>
                        <el-icon><Back /></el-icon>
                    </el-button>
                </Link>
                <h4 class="text-lg font-bold ml-2">Edit Posts</h4>
            </div>
            <div class="p-4 rounded shadow-sm">
                <el-form
                    label-width="120px"
                    ref="formRef"
                    :model="form"
                    label-position="top"
                >
                    <el-form-item label="Poster Photo" size="large">
                        <div
                            class="upload-card h-60 w-full object-cover"
                            @click="selectImage"
                        >
                            <img
                                v-show="imgSrc"
                                id="preview_img"
                                class="w-full h-auto object-cover"
                                :src="imgSrc"
                            />
                            <h4 v-show="!imgSrc">
                                Upload Poster (1200 x 630px)
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
                        <InputError
                            class="mt-2"
                            :message="$page.props.errors.image"
                        />
                    </el-form-item>
                    <div class="grid grid-cols-2 gap-4">
                        <el-form-item
                            label="Title"
                            prop="title"
                            :rules="[
                                {
                                    required: true,
                                    message: 'Title is required',
                                    trigger: 'blur',
                                },
                            ]"
                        >
                            <el-input v-model="form.title" />
                            <InputError
                                class="mt-2"
                                :message="$page.props.errors.name"
                            />
                        </el-form-item>
                        <el-form-item
                            label="Category"
                            :rules="[
                                {
                                    required: true,
                                    message: 'Category is required',
                                    trigger: 'blur',
                                },
                            ]"
                        >
                            <el-select
                                v-model="form.category"
                                placeholder="Select"
                                filterable
                            >
                                <el-option
                                    v-for="item in categories"
                                    :key="item.slug"
                                    :label="item.name"
                                    :value="item.id"
                                />
                            </el-select>
                            <InputError
                                class="mt-2"
                                :message="$page.props.errors.category"
                            />
                        </el-form-item>
                    </div>
                    <div class="mb-5">
                        <p class="mb-1.5">Description</p>
                        <el-input
                            type="textarea"
                            :rows="5"
                            v-model="form.desc"
                        />
                        <!-- <MyEditor v-model="form.desc" /> -->
                        <InputError
                            class="mt-2"
                            :message="$page.props.errors.desc"
                        />
                    </div>

                    <el-button
                        type="primary"
                        @click="submitDialog(formRef)"
                        :class="{ 'opacity-25': isLoading }"
                        :disabled="isLoading"
                    >
                        Save
                    </el-button>
                </el-form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { reactive, ref, toRefs } from "vue";
import InputError from "@/Components/InputError.vue";
import { Back } from "@element-plus/icons-vue";
import { router, usePage } from "@inertiajs/vue3";
import { ElMessage } from "element-plus";
export default {
    props: ["categories", "post"],
    components: {
        AuthenticatedLayout,
        InputError,
        Back,
    },
    setup(props) {
        const formRef = ref();
        const state = reactive({
            isLoading: false,
            imgSrc: props.post ? props.post.poster : "",
            virtualForm: new FormData(),
            form: {
                title: props.post.title ?? "",
                category: props.post.category_id ?? "",
                desc: props.post.desc ?? "",
            },
            backurl: usePage().props.previous,
        });

        // upload Image
        const selectImage = () => {
            var upload = document.getElementById("upload");
            upload.click();
        };

        const loadFile = (event) => {
            var input = event.target;
            var file = input.files[0];

            state.imgSrc = URL.createObjectURL(file);

            state.virtualForm.append("poster", file);

            var output = document.getElementById("preview_img");
            output.src = URL.createObjectURL(file);
            output.onload = function () {
                URL.revokeObjectURL(output.src); // free memory
            };
        };

        const submitDialog = (formRef) => {
            formRef.validate((valid) => {
                if (valid) {
                    state.isLoading = true;
                    state.virtualForm.append("title", state.form.title);
                    state.virtualForm.append("category", state.form.category);
                    state.virtualForm.append("desc", state.form.desc);
                    state.virtualForm.append("_method", "patch");
                    router.post(
                        route("admin.posts.update", props.post.id),
                        state.virtualForm,
                        {
                            onSuccess: (page) => {
                                state.isLoading = false;
                                ElMessage.success(page.props.flash.success);
                                formRef.resetFields();
                            },
                            onError: (page) => {
                                state.isLoading = false;
                                formRef.resetFields();
                                ElMessage.error(page.error);
                            },
                        }
                    );
                }
            });
        };

        return {
            ...toRefs(state),
            formRef,
            selectImage,
            loadFile,
            submitDialog,
        };
    },
};
</script>

<style></style>
