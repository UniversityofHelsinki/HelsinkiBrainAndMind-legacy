<template>
  <button @click="handleClick" class="button" :class="{ 'button--primary': isPrimary, 'button--secondary': isSecondary }" type="button">
    <slot/>
  </button>
</template>

<script>
import './button.scss';

export default {
  name: 'Button',
  props: {
    isPrimary: Boolean,
    isSecondary: Boolean
  },
  methods: {
    handleClick(event){
      this.$emit('handleClick', event);
    }
  },
}
</script>