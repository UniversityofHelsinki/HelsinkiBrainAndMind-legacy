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
      //'http://Brain:bnm_2020@dev.bnm.druidfi.wod.by/filters'
      //'bnm.docker.sh/filters'

      /*

      axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
       */


      this.axios.get('http://bnm.docker.sh/filters', {}, {
        headers: {
          'Accept': 'application/json',
          'Content-type': 'application/json',
          //'Access-Control-Allow-Origin': '*'
        },
        withCredentials: true,
        crossorigin: true
      })
      .then((result) => {
        console.log(result);
      }).catch((e) => {
        console.log('catch me if you can', e);
      });


      const filters = [{"id":"2","name":"Aalto University","children":["4"]},{"id":"4","name":"Unit 2","children":null},{"id":"1","name":"Helsinki University","children":["3","4"]},{"id":"3","name":"Helsinki Unit 1","children":null},{"id":"4","name":"Unit 2","children":null}];
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
      })

      this.filterItems = mappedFilters
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
</style>
