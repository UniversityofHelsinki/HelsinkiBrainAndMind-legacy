<template>
    <div class="hello">
        <label for="filter">Filter
            <select @change="filterChanged">
                <FilterOptions :filter-items="filterItems"></FilterOptions>
            </select>
        </label>
    </div>
</template>

<script>
  import FilterOptions from './FilterOptions.vue';
  export default {

    name: 'SearchFilter',
    components: {
      FilterOptions
    },
    emits: ['filterChanged'],
    data() {
      return {
        // filterDefault: [{id: 0, name: 'Select a filter'}],
        filterItems: []
      }
    },

    computed: {
      searchFilterList() {
        if(this.filterItems){
          return this.filterDefault.concat(this.filterItems);
        }
        return this.filterDefault;
      }
    },

    methods: {
      filterChanged(event){
        this.$emit('filterChanged', event.target.value);
      }
    },

    mounted() {
      // Hardcoded, move to config
      //'https://Brain:bnm_2020@dev.bnm.druidfi.wod.by/filters'

      let filters = [];
      this.axios.get('https://bnm.docker.sh/filters', {}, {
        headers: {
          'Content-type': 'application/json',
        },
      })
      .then((result) => {
        filters = result.data;
        const parents = filters.filter( filter => filter.children);
        const mappedFilters = parents.map((filter) => {
          const filterChildren = [];

          filter.children.forEach(childId => {
            const child = filters.find( f => f.id === childId);
            delete child.children;
            filterChildren.push({
              id: child.id,
              name: child.name });
          });
          return {id: filter.id, name: filter.name, children: filterChildren};
        });
        this.filterItems = mappedFilters
      }).catch((e) => {
        console.log('catch me if you can', e);
      });

    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
</style>
