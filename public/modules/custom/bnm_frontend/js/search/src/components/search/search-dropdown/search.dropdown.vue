<template>
  <div>
    <div class="search-dropdown">
      <label class="search-dropdown__label"  for="dropdown">
        Organization
      </label>
      <select class="search-dropdown__select" id="dropdown" @change="handleDropdownChange" :value="this.selectedOption">
        <SearchDropdownOptions :options="options"></SearchDropdownOptions>
      </select>
    </div>
  </div>
</template>

<script>
import SearchDropdownOptions from './search.dropdown.options';
import getAffiliates from '../../../services/affiliates.service.js';
import './search-dropdown.scss';

export default {
  name: 'SearchDropdown',
  components: {SearchDropdownOptions},
  props: {
    selectedOption: Number,
  },
  data() {
    return {
      options: []
    }
  },

  methods: {
    handleDropdownChange(event){
      this.$emit('handleChange', event.target.value);
    }
  },

  async mounted() {
    const affiliates = await getAffiliates();
    const parentOptions = affiliates.filter(option => option.children);

    let stack = [];

    if (parentOptions.length === 0) {
      this.options = affiliates;
    } else {
      stack = parentOptions.map(({id, name, children: childrenIds}) => {
        const children = childrenIds.map((childrenId) => {
          const children = affiliates.find(({id}) => id === childrenId)
          delete children.children;
          return children;
        })

        return [
          {id: 0, name: '--------------------------------', isDisabled: true},
          {id, name: `${name} (all)`},
          ...children
        ];
      })

      this.options = stack.flat();
    }
  }
}
</script>
