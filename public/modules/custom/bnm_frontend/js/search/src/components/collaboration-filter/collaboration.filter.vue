<template>
  <div class="industrial-collaboration-container">
    <input id="only-industrial-collaboration" class="industrial-collaboration-checkbox" type="checkbox" @change="handleCollaborationCheckbox" />
    <label for="only-industrial-collaboration" class="search-field__label"> Only Industrial Collaboration </label>
  </div>
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
      this.$emit('handleChange', event.target.checked)
    },
  }
}
</script>
