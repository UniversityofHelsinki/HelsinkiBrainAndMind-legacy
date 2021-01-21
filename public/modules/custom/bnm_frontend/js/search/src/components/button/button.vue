<template>
  <button @mouseup="handleMouseClick" @click="handleClick" class="button" :class="{ 'button--primary': isPrimary, 'button--secondary': isSecondary }" :type="isSubmit ? 'submit' : 'button'">
    <slot/>
  </button>
</template>

<script>
import './button.scss';

export default {
  name: 'Button',
  props: {
    isPrimary: Boolean,
    isSecondary: Boolean,
    isSubmit: Boolean
  },
  methods: {
    handleClick(event){
      this.$emit('handleClick', event);
    },
    handleMouseClick(event){
      this.$emit('handleMouseClick', event);
    }
  },
}
</script>