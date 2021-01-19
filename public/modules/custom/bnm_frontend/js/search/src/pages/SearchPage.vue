<template>
    <Search @searchCompleted="getSearchResults"></Search>
    <Container v-if="results.length === 0 && !this.isLoading">
      <h2>No results.</h2>
    </Container>
    <Loader v-if="this.isLoading"></Loader>
    <SearchResults :results="results" v-if="!this.isLoading"></SearchResults>
    <Pagination v-if="isPaginationVisible && results.length !== 0"></Pagination>
    <Footer></Footer>
</template>

<script>
import Search from '../components/search/search.vue'
import SearchResults from '../components/search-results/search-results.vue';
import Loader from '../components/loader/loader.vue';
import Container from '../components/container/container.vue';
import Footer from '../components/footer/footer.vue';
import Pagination from '../components/pagination/pagination.vue'
import useInitialData from '../stores/data';
import { watch } from '@vue/runtime-core';

export default {
  name: 'SearchPage',
  components: {
    Search,
    SearchResults,
    Loader,
    Container,
    Footer,
    Pagination
  },
  data() {
    return {
      results: [],
      isLoading: false,
      isPaginationVisible: false,
    }
  },
  methods: {
    getSearchResults(results) {
      this.results = Object.values(results);
    },
    setLoadingStatus(status) {
      this.isLoading = status;
    }
  },
  async mounted() {
    const { fetchInitialData, setResultsLoadingStatus, results } = useInitialData();

    watch(() => {
      const hasSubArray = results._object.results.some(item => Array.isArray(item))

      this.isPaginationVisible = false;

      if (hasSubArray) {
        this.getSearchResults(results._object.results[results._object.currentPage])
        this.isPaginationVisible = true;
      } else {
        this.getSearchResults(results._object.results);
      }

    if (results._object.pageCount === 0) {
      this.isPaginationVisible = false;
    }

      this.setLoadingStatus(results._object.resultsLoadingStatus);
    });

    if (results._object.pageCount === 0) {
      this.isPaginationVisible = false;
    }

    setResultsLoadingStatus();

    await fetchInitialData(this.$environment)
    .then(() => {
        this.getSearchResults(results._object.results);
    })
    .catch((error) => {
      // eslint-disable-next-line
      console.log('error', error);
    });
  }
}
</script>
