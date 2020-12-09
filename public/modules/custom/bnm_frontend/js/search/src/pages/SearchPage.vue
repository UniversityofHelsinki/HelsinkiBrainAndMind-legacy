<template>
    <Search @searchCompleted="getSearchResults" @searchReseted="resetSearch" @isLoading="setLoadingStatus"></Search>
    <Loader v-if="this.isLoading"></Loader>
    <SearchResults :results="results" v-if="!this.isLoading"></SearchResults>
</template>

<script>
import Search from '../components/search/search.vue'
import SearchResults from '../components/search-results/search-results.vue';
import Loader from '../components/loader/loader.vue';

export default {
  name: 'SearchPage',
  components: {
    Search,
    SearchResults,
    Loader
  },
  data() {
    return {
      results: [],
      isLoading: false,
    }
  },
  methods: {
    getSearchResults(results) {
      this.results = Object.values(results);
    },
    resetSearch() {
      this.results = [];
    },
    setLoadingStatus(status) {
      this.isLoading = status;
    }
  }
}
</script>
