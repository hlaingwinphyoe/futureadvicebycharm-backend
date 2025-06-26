<template>
    <Head :title="'Login -' + $page.props.app_name" />
    <div
        class="container mx-auto flex items-center h-screen shadow-sm overflow-y-hidden relative"
    >
        <div class="absolute right-8 top-24">
            <el-button
                v-if="theme == 'light'"
                circle
                @click="darkMode"
                size="large"
                color="#000"
            >
                <el-icon size="large"><Moon /></el-icon>
            </el-button>
            <el-button
                v-else
                type="warning"
                circle
                @click="lightMode"
                size="large"
            >
                <el-icon size="large"><Sunny /></el-icon>
            </el-button>
        </div>
        <div
            class="bg-[#7b61ba] rounded-3xl border border-gray-200 overflow-hidden"
            style="
                background-image: url(/imgs/bg.png);
                background-repeat: no-repeat;
                background-size: contain;
                background-position: center;
            "
        >
            <div class="grid md:grid-cols-2 items-center gap-8 h-full">
                <div class="max-md:order-1 p-4">
                    <img
                        src="/imgs/1.svg"
                        class="lg:max-w-[80%] w-full h-full object-contain block mx-auto"
                        alt="login-image"
                    />
                </div>

                <div
                    class="flex items-center md:px-8 p-6 md:py-16 2xl:py-24 bg-[#FAF4FE] md:rounded-tl-[55px] md:rounded-bl-[55px] h-full md:rounded-br-3xl"
                    style="
                        background-image: url(./imgs/bg.png);
                        background-repeat: no-repeat;
                        background-size: cover;
                        background-position: center;
                    "
                >
                    <el-form
                        label-position="top"
                        ref="formRef"
                        :model="form"
                        class="max-w-lg w-full mx-auto"
                    >
                        <div class="mb-5">
                            <div class="flex items-center gap-2">
                                <img src="/logo.png" class="h-12" alt="logo" />
                                <p class="text-black text-lg">
                                    {{ $page.props.app_name }}
                                </p>
                            </div>
                            <h3 class="text-black text-4xl mt-4 font-bold">
                                Welcome Back!
                            </h3>
                            <p class="text-black font-medium mt-4">Sign in</p>
                        </div>

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
                            class="!mb-6 black-text font-medium"
                        >
                            <el-input
                                v-model="form.name"
                                class="input-round"
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="form.name" />
                        </el-form-item>
                        <el-form-item
                            label="Password"
                            prop="password"
                            :rules="[
                                {
                                    required: true,
                                    message: 'Password is required',
                                    trigger: 'blur',
                                },
                            ]"
                            class="black-text font-medium"
                        >
                            <el-input
                                type="password"
                                show-password
                                v-model="form.password"
                                class="input-round"
                            />
                            <InputError class="mt-2" :message="form.password" />
                        </el-form-item>

                        <div
                            class="flex flex-wrap items-center justify-between gap-4"
                        >
                            <el-checkbox
                                v-model="form.remember"
                                label="Remember me"
                                size="large"
                                class="!text-black"
                            />
                            <!-- <div>
                            <a
                                href="jajvascript:void(0);"
                                class="text-primary-600 font-semibold text-sm hover:underline"
                            >
                                Forgot Password?
                            </a>
                        </div> -->
                        </div>

                        <div class="mt-12" @click="submit">
                            <el-button class="w-full" type="primary" round>
                                Sign in
                            </el-button>
                        </div>

                        <div class="my-4 flex items-center gap-4">
                            <hr class="w-full border-gray-300" />
                            <!-- <p class="text-sm text-black text-center">or</p>
                            <hr class="w-full border-gray-300" /> -->
                        </div>

                        <!-- <el-button round plain class="w-full btn-large group">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20px"
                                class="inline mr-2"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    fill="#fbbd00"
                                    d="M120 256c0-25.367 6.989-49.13 19.131-69.477v-86.308H52.823C18.568 144.703 0 198.922 0 256s18.568 111.297 52.823 155.785h86.308v-86.308C126.989 305.13 120 281.367 120 256z"
                                    data-original="#fbbd00"
                                />
                                <path
                                    fill="#0f9d58"
                                    d="m256 392-60 60 60 60c57.079 0 111.297-18.568 155.785-52.823v-86.216h-86.216C305.044 385.147 281.181 392 256 392z"
                                    data-original="#0f9d58"
                                />
                                <path
                                    fill="#31aa52"
                                    d="m139.131 325.477-86.308 86.308a260.085 260.085 0 0 0 22.158 25.235C123.333 485.371 187.62 512 256 512V392c-49.624 0-93.117-26.72-116.869-66.523z"
                                    data-original="#31aa52"
                                />
                                <path
                                    fill="#3c79e6"
                                    d="M512 256a258.24 258.24 0 0 0-4.192-46.377l-2.251-12.299H256v120h121.452a135.385 135.385 0 0 1-51.884 55.638l86.216 86.216a260.085 260.085 0 0 0 25.235-22.158C485.371 388.667 512 324.38 512 256z"
                                    data-original="#3c79e6"
                                />
                                <path
                                    fill="#cf2d48"
                                    d="m352.167 159.833 10.606 10.606 84.853-84.852-10.606-10.606C388.668 26.629 324.381 0 256 0l-60 60 60 60c36.326 0 70.479 14.146 96.167 39.833z"
                                    data-original="#cf2d48"
                                />
                                <path
                                    fill="#eb4132"
                                    d="M256 120V0C187.62 0 123.333 26.629 74.98 74.98a259.849 259.849 0 0 0-22.158 25.235l86.308 86.308C162.883 146.72 206.376 120 256 120z"
                                    data-original="#eb4132"
                                />
                            </svg>
                            <span
                                class="text-black group-hover:text-primary-500"
                                >Continue with google</span
                            >
                        </el-button> -->
                    </el-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { Sunny, Moon } from "@element-plus/icons-vue";
import { useDarkMode } from "@/darkmode";
import InputError from "@/Components/InputError.vue";

export default {
    components: {
        Sunny,
        Moon,
        InputError,
    },
    setup() {
        const formRef = ref();
        const form = useForm({
            name: "",
            password: "",
        });

        const submit = () => {
            form.post(route("login"), {
                onFinish: () => form.reset("password"),
            });
        };

        const { theme, darkMode, lightMode } = useDarkMode();

        onMounted(() => {
            let getTheme = JSON.parse(localStorage.getItem("theme")) ?? "dark";
            if (getTheme == "dark") {
                darkMode();
            } else {
                lightMode();
            }
        });

        return {
            theme,
            darkMode,
            lightMode,
            form,
            formRef,
            submit,
        };
    },
};
</script>

<style>
.black-text .el-form-item__label {
    color: #000 !important;
}

.btn-large .el-button,
.el-button.is-round {
    padding: 20px 19px !important;
}
</style>
