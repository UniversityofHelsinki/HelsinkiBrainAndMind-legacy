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

  mounted() {
    this.axios.get('http://dev.bnm.druidfi.wod.by/filters', {}, {
      headers: {
        'Content-type': 'application/json',
      },
    })
    .then(({data: result}) => {
      const parentOptions = result.filter(option => option.children);

      this.options = parentOptions.map(({id, name, children: childrenIds}) => {
        const children = childrenIds.map((childrenId) => {
          const children = result.find(({id}) => id === childrenId)
          delete children.children;

          return children;
        })

        return {id, name, children};
      })

    }).catch((error) => {
      console.log(error);
    });
  }
}
</script>