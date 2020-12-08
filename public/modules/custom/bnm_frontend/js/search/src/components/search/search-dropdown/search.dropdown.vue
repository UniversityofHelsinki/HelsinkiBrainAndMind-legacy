<template>
  <div>
    <div class="search-dropdown">
      <label class="search-dropdown__label"  for="dropdown">
        Affiliations
      </label>
      <select class="search-dropdown__select" id="dropdown">
        <option value="0">-- All --</option>
        <optgroup :label="filter.name" v-for="filter in filterItems" v-bind:key="filter.id">
          <option class="selectableoptiontitle" :value="filter.id">{{filter.name}}</option>
          <option :value="child.id" v-for="child in filter.children" v-bind:key="child.id">{{child.name}}</option>
        </optgroup>
      </select>
    </div>
  </div>
</template>

<script>
import './search-dropdown.scss';

export default {
  name: 'SearchDropdown'
}
</script>