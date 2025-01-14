<template>
    <Head title="Zodiacs" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <h4 class="text-lg font-bold mb-3 ml-1">System Information</h4>
            <div class="grid lg:grid-cols-2 gap-4">
                <div class="p-4 rounded shadow-sm border index-content">
                    <el-form
                        label-width="120px"
                        ref="formRef"
                        :model="form"
                        label-position="top"
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
                            <el-input v-model="form.name" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
                        </el-form-item>
                        <el-form-item label="Email" prop="email">
                            <el-input v-model="form.email" type="email" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.email"
                            />
                        </el-form-item>
                        <el-form-item label="Address" prop="address">
                            <el-input v-model="form.address" type="textarea" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.address"
                            />
                        </el-form-item>
                        <el-button
                            type="primary"
                            @click="systemInfoSubmit"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            >Save</el-button
                        >
                    </el-form>
                </div>
                <!-- <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <h4 class="mb-4">Upload Logo</h4>
            <div class="">
              <div class="relative group">
                <img
                  v-show="imgSrc != ''"
                  id="preview_img"
                  class="upload-card object-contain mr-2.5"
                  :src="imgSrc"
                />
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
            </div>
            <el-button class="mt-4" type="primary" @click="logoSubmit(formRef)">
              Upload
            </el-button>
          </div> -->
                <div class="p-4 rounded shadow-sm border index-content">
                    <h4 class="mb-4">Phone</h4>
                    <div class="flex flex-wrap gap-2">
                        <el-tag
                            v-for="phone in system_info.phones"
                            :key="phone"
                            closable
                            :disable-transitions="false"
                            size="large"
                            @close="handlePhoneDelete(phone.id)"
                        >
                            {{ phone.phone }}
                        </el-tag>
                        <div v-if="inputVisible">
                            <el-input
                                ref="InputRef"
                                v-model="phoneForm.phone"
                                style="width: 10rem"
                                @keyup.enter="systemPhoneSubmit"
                                @blur="systemPhoneSubmit"
                            />
                            <InputError
                                class="mt-2"
                                :message="phoneForm.errors.phone"
                            />
                        </div>
                        <el-button
                            v-else
                            @click="showInput"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            + New Phone
                        </el-button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ElMessage, ElMessageBox } from "element-plus";
import { nextTick, reactive, ref, toRefs } from "vue";
import InputError from "@/Components/InputError.vue";
import { Plus } from "@element-plus/icons-vue";
export default {
    props: ["system_info"],
    components: {
        AuthenticatedLayout,
        InputError,
        Plus,
    },
    setup(props) {
        const state = reactive({
            inputVisible: false,
            imgSrc: "",
            virtualForm: new FormData(),
        });
        const InputRef = ref();

        const form = useForm({
            name: props.system_info.name,
            email: props.system_info.email,
            address: props.system_info.address,
        });

        const phoneForm = useForm({
            phone: "",
        });

        const systemInfoSubmit = () => {
            form.patch(
                route("admin.system-infos.update", {
                    onSuccess: (page) => {
                        form.clearErrors();
                        ElMessage.success(page.props.flash.success);
                    },
                })
            );
        };

        const systemPhoneSubmit = () => {
            phoneForm.patch(
                route("admin.system-infos.addPhone", props.system_info.id),
                {
                    onSuccess: (page) => {
                        reset();
                        ElMessage.success(page.props.flash.success);
                    },
                }
            );
        };

        const handlePhoneDelete = (id) => {
            ElMessageBox.confirm(
                "Are you sure want to delete?",
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
                    router.delete(
                        route("admin.system-infos.deletePhone", {
                            info: props.system_info.id,
                            phone: id,
                        }),
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

        const showInput = () => {
            state.inputVisible = true;
            nextTick(() => {
                InputRef.value.focus();
            });
        };

        const reset = () => {
            form.reset();
            form.clearErrors();
            phoneForm.reset();
            phoneForm.clearErrors();
            state.inputVisible = false;
        };

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

        return {
            ...toRefs(state),
            InputRef,
            showInput,
            form,
            phoneForm,
            systemInfoSubmit,
            systemPhoneSubmit,
            handlePhoneDelete,
            selectImage,
            loadFile,
        };
    },
};
</script>

<style></style>
