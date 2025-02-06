<template>
    <el-dialog
        :modelValue="show"
        @update:modelValue="show = $event"
        @close="closeDialog(formRef)"
        @open="openDialog"
        draggable
        :title="dialogTitle"
        :close-on-click-modal="false"
        width="500"
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
                label="Price (Ks)"
                prop="price"
                :rules="[
                    {
                        required: true,
                        message: 'Price is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-input v-model="form.price" />
                <InputError class="mt-2" :message="$page.props.errors.price" />
            </el-form-item>
            <el-form-item
                label="Price (฿)"
                prop="th_price"
                :rules="[
                    {
                        required: true,
                        message: 'Thai Price is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-input v-model="form.th_price" />
                <InputError
                    class="mt-2"
                    :message="$page.props.errors.th_price"
                />
            </el-form-item>
            <!-- <el-form-item
          label="Price"
          prop="price"
          :rules="[
            {
              required: true,
              message: 'Price is required',
              trigger: 'blur',
            },
          ]"
        >
          <el-input
            v-model="form.price"
            type="number"
            :min="0"
            style="max-width: 600px"
            placeholder="Please input"
            class="input-with-select"
          >
            <template #append>
              <el-select
                v-model="form.currency"
                placeholder="Select"
                style="width: 115px"
              >
                <el-option
                  v-for="item in currencies"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </template>
          </el-input>
          <InputError class="mt-2" :message="form.errors.price" />
          <InputError class="mt-2" :message="form.errors.currency" />
        </el-form-item> -->
            <el-form-item
                label="Astrologer"
                prop="astrologer"
                :rules="[
                    {
                        required: true,
                        message: 'Astrologer is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-select
                    v-model="form.astrologer"
                    placeholder="Select"
                    clearable
                    @clear="form.astrologer = ''"
                >
                    <el-option
                        v-for="item in astrologers"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    />
                </el-select>
                <InputError
                    class="mt-2"
                    :message="$page.props.errors.astrologer"
                />
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
    props: ["show", "title", "data", "astrologers", "currencies"],
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
                name: "",
                price: "",
                astrologer: "",
                th_price: "",
            },
        });

        const formRef = ref();

        // upload Image
        const selectImage = () => {
            var upload = document.getElementById("upload");
            upload.click();
        };

        const loadFile = (event) => {
            var input = event.target;
            console.log(input)
            var file = input.files[0];

            state.imgSrc = URL.createObjectURL(file);

            state.virtualForm.append("image", file);

            var output = document.getElementById("preview_img");
            output.src = URL.createObjectURL(file);
            output.onload = function () {
                URL.revokeObjectURL(output.src); // free memory
            };
        };

        const submitDialog = (formRef) => {
            state.isLoading = true;
            formRef.validate((valid) => {
                if (valid) {
                    state.virtualForm.append("name", state.form.name);
                    state.virtualForm.append("price", state.form.price);
                    state.virtualForm.append("th_price", state.form.th_price);
                    state.virtualForm.append(
                        "astrologer",
                        state.form.astrologer
                    );
                    if (state.dialogTitle === "Create") {
                        router.post(
                            route("admin.packages.store"),
                            state.virtualForm,
                            {
                                onSuccess: (page) => {
                                    state.isLoading = false;
                                    closeDialog(formRef);
                                    ElMessage.success(page.props.flash.success);
                                },
                            }
                        );
                    } else {
                        state.virtualForm.append("_method", "patch");
                        router.post(
                            route("admin.packages.update", props.data.id),
                            state.virtualForm,
                            {
                                onSuccess: (page) => {
                                    state.isLoading = false;
                                    closeDialog(formRef);
                                    ElMessage.success(page.props.flash.success);
                                },
                            }
                        );
                    }
                }
            });
        };

        const closeDialog = (formRef) => {
            formRef.resetFields();
            context.emit("closed");
            state.imgSrc = "";
        };

        const openDialog = () => {
            state.dialogTitle = props.title;
            state.form.name = props.data.name ?? "";
            state.form.price = props.data.price ?? "";
            state.form.th_price = props.data.th_price ?? "";
            state.form.astrologer = props.data.astrologer_id
                ? props.data.astrologer_id
                : props.astrologers[0].id;
            state.imgSrc = props.data.image ?? "";
            // form.currency = props.data.currency_id ?? props.currencies[0].id;
        };

        const handleRemove = (uploadFile) => {
            state.imgSrc = "";
            if (uploadFile) {
                router.delete(
                    route("admin.packages.destroy-media", uploadFile.id),
                    {
                        onSuccess: () => {
                            state.imgSrc = "";
                        },
                    }
                );
            }
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
