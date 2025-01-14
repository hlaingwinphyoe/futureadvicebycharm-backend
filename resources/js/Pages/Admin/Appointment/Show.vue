<template>
    <Head :title="'Appointment' + appointment.appointment_no" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <div class="flex items-center mb-3 gap-2">
                <Link :href="route('admin.appointments.index')">
                    <el-button type="primary">
                        <el-icon><Back /></el-icon>
                    </el-button>
                </Link>
                <h4 class="text-lg font-bold ml-1">Appointment Information</h4>
            </div>
            <div class="p-4 rounded shadow-sm border index-content">
                <div class="grid lg:grid-cols-2 gap-4">
                    <div>
                        <h5 class="font-bold uppercase">User Info:</h5>
                        <table>
                            <tbody>
                                <tr>
                                    <td class="py-3 px-4">Appointment No.</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        {{ appointment.appointment_no }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">User</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        <b>{{
                                            appointment.user
                                                ? appointment.user.name
                                                : ""
                                        }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Phone</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        {{
                                            appointment.user
                                                ? appointment.user.phone
                                                : ""
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Email</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        {{
                                            appointment.user
                                                ? appointment.user.email
                                                : ""
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Gender</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        <b>{{
                                            appointment.user
                                                ? appointment.user.gender.name
                                                : ""
                                        }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Weekday</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        <b>{{
                                            appointment.user
                                                ? appointment.user.weekday.name
                                                : ""
                                        }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Date of Birth</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        <b>{{
                                            appointment.user
                                                ? appointment.user.dob
                                                : ""
                                        }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Social Media</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        {{
                                            appointment.user
                                                ? appointment.user.social_link
                                                : ""
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Transaction ID</td>
                                    <td>-</td>
                                    <td class="py-3 px-4">
                                        {{ appointment.transaction_no }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">
                                        Transaction Images
                                    </td>
                                </tr>
                                <tr>
                                    <img
                                        :src="
                                            '/storage/' +
                                            appointment.transaction_image
                                        "
                                        class="h-40 rounded"
                                        alt=""
                                    />
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h5 class="font-bold uppercase">Packages:</h5>
                        <table>
                            <tr
                                v-for="appointment_pack in appointment.appointment_packages"
                                :key="appointment_pack.id"
                            >
                                <td>
                                    <el-icon class="pt-4"><Check /></el-icon>
                                </td>
                                <td class="py-3 px-4">
                                    {{
                                        appointment_pack.package
                                            ? appointment_pack.package.name
                                            : ""
                                    }}
                                </td>
                            </tr>
                        </table>
                        <h5 class="font-bold uppercase my-2">
                            Asked Questions:
                        </h5>
                        <p class="indent-5">{{ appointment.desc }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-4">
                <el-button
                    v-if="appointment.is_paid"
                    type="primary"
                    @click="handleAction('finished')"
                    >Finished</el-button
                >
                <div v-else>
                    <el-button type="danger" @click="handleAction('incomplete')"
                        >Incomplete</el-button
                    >
                    <el-button type="success" @click="handleAction('approved')"
                        >Approved</el-button
                    >
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Check, Back } from "@element-plus/icons-vue";
import { router } from "@inertiajs/vue3";
export default {
    props: ["appointment"],
    components: {
        AuthenticatedLayout,
        Check,
        Back,
    },
    setup(props) {
        const handleAction = (type) => {
            router.patch(
                route("admin.appointments.update", {
                    id: props.appointment.id,
                    type: type,
                })
            );
        };

        return {
            handleAction,
        };
    },
};
</script>

<style></style>
