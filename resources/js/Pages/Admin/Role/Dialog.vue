<template>
    <el-dialog
      :modelValue="show"
      @update:modelValue="show = $event"
      @close="closeDialog(formRef)"
      @open="openDialog"
      draggable
      :title="dialogTitle"
      :close-on-click-modal="false"
      width="800"
    >
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
          <InputError class="mt-2" :message="form.errors.name" />
        </el-form-item>
        <el-form-item label="Permissions">
          <el-scrollbar height="400">
            <PermissionsList
              v-for="(permis, type) in permissions"
              :key="type"
              :type="type"
              :permis="permis"
              :selected="data.permissionIds"
              :role="data"
              @on-checked-all="checkedAll"
              @on-checked="checked"
            />
            <InputError class="mt-2" :message="form.errors.permis" />
          </el-scrollbar>
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="closeDialog(formRef)">Cancel</el-button>
          <el-button
            type="primary"
            @click="submitDialog(formRef)"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Save
          </el-button>
        </div>
      </template>
    </el-dialog>
  </template>
  
  <script>
  import { reactive, ref, toRefs } from "vue";
  import { useForm } from "@inertiajs/vue3";
  import { ElMessage } from "element-plus";
  import InputError from "@/Components/InputError.vue";
  import PermissionsList from "./PermissionsList.vue";
  export default {
    props: ["show", "title", "data", "permissions"],
    components: {
      InputError,
      PermissionsList,
    },
    emits: ["closed"],
    setup(props, context) {
      const state = reactive({
        dialogTitle: "",
        checkAll: false,
        isIndeterminate: true,
      });
  
      const formRef = ref();
  
      const form = useForm({
        name: "",
        permis: [],
      });
  
      const submitDialog = (formRef) => {
        formRef.validate((valid) => {
          if (valid) {
            if (state.dialogTitle === "Create") {
              form.post(route("admin.roles.store"), {
                onSuccess: (page) => {
                  closeDialog(formRef);
                  ElMessage.success(page.props.flash.success);
                },
              });
            } else {
              form.patch(route("admin.roles.update", props.data.id), {
                onSuccess: (page) => {
                  closeDialog(formRef);
                  ElMessage.success(page.props.flash.success);
                },
              });
            }
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
        form.name = props.data.name ?? "";
        form.permis = props.data.permissionIds ? props.data.permissionIds : [];
      };
  
      const checkedAll = ([data, bol]) => {
        if (bol == true) {
          form.permis.push(...data);
        } else {
          form.permis = form.permis.filter((old) => !data.includes(old));
        }
      };
  
      const checked = ([data, bol]) => {
        if (bol == true) {
          if (!form.permis.includes(data)) {
            form.permis.push(data);
          }
        } else {
          if (form.permis.includes(data)) {
            console.log(data);
            form.permis = form.permis.filter((old) => old != data);
          }
        }
      };
  
      return {
        ...toRefs(state),
        openDialog,
        formRef,
        closeDialog,
        submitDialog,
        form,
        checkedAll,
        checked,
      };
    },
  };
  </script>
  
  <style></style>
  