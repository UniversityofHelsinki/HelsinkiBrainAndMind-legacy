<template>
  <div>
    <div class="search-dropdown">
      <div class="search-dropdown__header">
        <label class="search-dropdown__label"  for="dropdown">
          Affiliations
        </label>
        <InfoIcon icon="Question" text="textOrganizationsHelp" iconName="Organizations"></InfoIcon>
      </div>
      <select class="search-dropdown__select" id="dropdown" @change="handleDropdownChange" :value="this.selectedOption">
        <SearchDropdownOptions :options="options"></SearchDropdownOptions>
      </select>
    </div>
  </div>
</template>

<script>
import InfoIcon from '../../info-icon/info-icon';
import SearchDropdownOptions from './search.dropdown.options';
import { watch } from '@vue/runtime-core';
import useInitialData from "../../../stores/data";
import './search-dropdown.scss';

export default {
  name: 'SearchDropdown',
  components: {SearchDropdownOptions, InfoIcon},
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
    const { results } = useInitialData();

    watch(() => {
      const affiliates = results._object.affiliates;
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
    })
  }
}
</script>
