<script lang="ts" setup>
import { onMounted, reactive, ref } from "vue";

const props = defineProps({
  role: { type: Object, required: true },
  permis: { type: Object, required: true },
  type: { type: String, required: true },
  selected: { type: Array, required: true },
});

const emit = defineEmits(["on-checked", "on-checked-all"]);

const checkAll = ref(false);
const isIndeterminate = ref(false);
const checkedPermissions = ref(props.selected.length ? props.selected : []);

const initialValue = calculateInitialValue();
const checkedItems = ref(initialValue);

const roleId = ref(props.role.id ? props.role.id : "create");

function calculateInitialValue() {
  if (props.selected.length > 0) {
    // change checkedItems value to true or false
    return new Array(props.selected.length).fill(true);
  } else {
    return new Array(props.permis.length).fill(false);
  }
}

// console.log(checkedPermissions.value);
const handleCheckAllChange = (val: boolean) => {
  const permissionsGroup = props.permis.map((i) => i.id); // ids of children
  checkedPermissions.value = val ? permissionsGroup : []; // children ids added to checkedPermissions
  isIndeterminate.value = false;

  if (val == true) {
    //fill true to children
    checkedItems.value = checkedItems.value.map((value, index) => true);
  } else {
    //fill false to children
    checkedItems.value = checkedItems.value.map((value, index) => false);
  }

  emit("on-checked-all", [permissionsGroup, val]);
};

const handleCheckboxChange = (index) => {
  const ck = document.querySelector(".checked-value-" + roleId.value + index);
  if (ck.classList.contains("is-checked")) {
    checkedPermissions.value.filter((per) => per == index);
    checkedItems.value[index] = true;
  } else {
    checkedPermissions.value.filter((per) => per != index);
    checkedItems.value[index] = false;
  }

  emit("on-checked", [index, checkedItems.value[index]]);
};

const handleCheckedPermissionsChange = (value: string[]) => {
  const checkedCount = value.length;

  checkAll.value = checkedCount === props.permis.length;
  isIndeterminate.value =
    checkedCount > 0 && checkedCount < props.permis.length;
};
</script>

<template>
  <div class="mb-5">
    <el-checkbox
      v-model="checkAll"
      :indeterminate="isIndeterminate"
      @change="handleCheckAllChange"
      :label="props.type"
      class="capitalize"
    >
      {{ props.type }}
    </el-checkbox>
    <el-checkbox-group
      class="flex flex-wrap"
      v-model="checkedPermissions"
      @change="handleCheckedPermissionsChange"
    >
      <el-checkbox
        v-for="permission in props.permis"
        :key="permission.id"
        :label="permission.slug"
        :value="permission.id"
        @change="handleCheckboxChange(permission.id)"
        v-model="checkedItems[permission.id]"
        :class="'checked-value-' + roleId.value + permission.id"
      >
        {{ permission.name }}
      </el-checkbox>
    </el-checkbox-group>
  </div>
</template>
