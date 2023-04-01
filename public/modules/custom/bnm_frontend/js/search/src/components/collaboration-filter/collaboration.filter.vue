<template>
  <label for="industrial collaboration checkbox">
    Industrial Collaboration Only
    <input type="checkbox" @change="handleCollaborationCheckbox" />
<!--           @input="(event) => $emit('update:checked', event.target.checked)"/>-->
<!--           @change="$emit('update:checked', $event.target.checked)" label="Only Industrial Collaboration"/>-->
  </label>
</template>

<script>

import './collaboration.filter.scss';
export default {
  name: "CollaborationFilter",
  props: {
    type: String,
  },
  checked: {
    type:Boolean,
  },
  methods: {
    handleCollaborationCheckbox(event){
      this.$emit('handleChange', event.target.value)
    },
  }
}
</script>
