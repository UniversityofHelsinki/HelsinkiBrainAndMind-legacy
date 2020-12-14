<template>
  <div>
    <div class="search-dropdown">
      <label class="search-dropdown__label"  for="dropdown">
        Affiliations
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

// const BACKEND_URL = process.env.VUE_APP_BACKEND_URL;

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

    if (parentOptions.length === 0) {
      this.options = affiliates;
    } else {
      this.options = parentOptions.map(({id, name, children: childrenIds}) => {
        const children = childrenIds.map((childrenId) => {
          const children = affiliates.find(({id}) => id === childrenId)
          delete children.children;
          return children;
        })
        return {id, name, children};
      })
    }
  }
}
</script>
