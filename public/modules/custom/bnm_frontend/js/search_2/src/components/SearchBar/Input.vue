<template>
    <input v-model="keyword">
    <div>{{keyword}}</div>
</template>

<script>
  export default {
    name: 'Input',
    data() {
      return {
        keyword: ''
      }
    },
    methods: {
      addKeyword() {
        this.$emit("add", { product: this.product, quantity: 42 });
      }
    }
  }
</script>

<style lang="scss">
</style>
