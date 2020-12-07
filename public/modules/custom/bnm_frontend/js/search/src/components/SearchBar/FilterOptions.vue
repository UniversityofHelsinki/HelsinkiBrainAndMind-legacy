<template>
    <option value="0">Select filter</option>
    <optgroup :label="filter.name" v-for="filter in filterItems" v-bind:key="filter.id">
        <option class="selectableoptiontitle" :value="filter.id">{{filter.name}}</option>
        <option :value="child.id" v-for="child in filter.children" v-bind:key="child.id">{{child.name}}</option>
    </optgroup>
</template>

<script>
  export default {
    name: 'FilterOptions',
    props: {
      filterItems: Array
    },
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
</style>