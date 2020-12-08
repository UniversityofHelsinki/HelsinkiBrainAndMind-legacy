<template>
    <Search @searchCompleted="getSearchResults"></Search>
    <SearchResults :results="results"></SearchResults>
</template>

<script>
import Search from '../components/search/search.vue'
import SearchResults from '../components/search-result/search-results.vue';

export default {
  name: 'SearchPage',
  components: {
    Search,
    SearchResults,
  },
  data() {
    return {
      results: [],
    }
  },
  methods: {
    getSearchResults(results){
      this.results = Object.values(results);
    }
  }
}
</script>