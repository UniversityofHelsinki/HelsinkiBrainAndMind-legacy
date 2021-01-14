<template>
  <option value="0">All</option>
  <option :value="option.id" v-for="option in options" v-bind:key="option.id" :disabled="option.isDisabled">{{option.name}}</option>
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
