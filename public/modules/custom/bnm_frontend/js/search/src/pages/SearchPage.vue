<template>
    <Search @searchCompleted="getSearchResults" @searchReseted="resetSearch" @isLoading="setLoadingStatus"></Search>
    <Container v-if="results.length === 0">
      <h2>No results.</h2>
    </Container>
    <Loader v-if="this.isLoading"></Loader>
    <SearchResults :results="results" v-if="!this.isLoading"></SearchResults>
</template>

<script>
import Search from '../components/search/search.vue'
import SearchResults from '../components/search-results/search-results.vue';
import Loader from '../components/loader/loader.vue';
import Container from '../components/container/container.vue';

export default {
  name: 'SearchPage',
  components: {
    Search,
    SearchResults,
    Loader,
    Container
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
