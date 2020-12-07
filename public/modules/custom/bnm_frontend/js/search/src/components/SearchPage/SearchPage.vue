<template>
    <SearchBar @searchCompleted="showSearchResults"></SearchBar>
    <ResultList :results="results"></ResultList>
    <Footer></Footer>
</template>

<script>
import SearchBar from '../SearchBar/SearchBar.vue';
import ResultList from '../ResultList/ResultList.vue';
import Footer from './Footer.vue';

export default {
    name: 'SearchPage2',
    components: {
      SearchBar,
      ResultList,
      Footer
    },
    data() {
      return {
        results: []
      }
    },
    methods: {
      showSearchResults(results){
        this.results = Object.values(results);
      }
    }
}
</script>

<style lang="scss">
    #app {
        font-family: Avenir, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }
</style>
