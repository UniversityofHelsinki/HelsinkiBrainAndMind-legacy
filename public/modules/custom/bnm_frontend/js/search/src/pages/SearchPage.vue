<template>
  <div>
    <Search @searchCompleted="getSearchResults"></Search>
  </div>
</template>

<script>
import Search from '../components/search/search.vue'

export default {
  name: 'SearchPage',
  components: {
    Search
  },
  data() {
    return {
      results: []
    }
  },
  methods: {
    getSearchResults(results){
      this.results = Object.values(results);
    }
  }
}
</script>