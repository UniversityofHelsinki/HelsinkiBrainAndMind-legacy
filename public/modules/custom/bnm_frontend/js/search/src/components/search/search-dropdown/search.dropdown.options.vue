<template>
  <option value="0">All</option>
  <optgroup :label="option.name" v-for="option in options" v-bind:key="option.id">
    <option :value="option.id">{{option.name}}</option>
    <option :value="child.id" v-for="child in option.children" v-bind:key="child.id">{{child.name}}</option>
  </optgroup>
</template>

<script>
import './search-dropdown.scss';

export default {
  name: 'SearchDropdownOptions',
  props: {
    options: Array
  }
}
</script>