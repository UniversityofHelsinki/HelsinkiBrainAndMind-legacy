<template>
  <SearchBar/>
  <ResultList/>
  <Footer/>
</template>

<script>
import SearchBar from './components/SearchBar/SearchBar.vue'
import ResultList from './components/Results/ResultList.vue'
import Footer from './components/Footer/Footer.vue'

export default {
  name: 'App',
  components: {
    SearchBar,
    ResultList,
    Footer
  }
}
</script>

<style lang="scss">
  @import "./scss/_variables.scss";

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
