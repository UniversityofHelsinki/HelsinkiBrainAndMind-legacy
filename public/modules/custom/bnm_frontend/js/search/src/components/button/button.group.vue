<template>
  <div class="button-group" v-bind:class="{ 'button-group--reversed': isReversed }">
    <slot/>
  </div>
</template>

<script>

export default {
  name: 'ButtonGroup',
  props: {
    isReversed: Boolean,
  }
}
</script>