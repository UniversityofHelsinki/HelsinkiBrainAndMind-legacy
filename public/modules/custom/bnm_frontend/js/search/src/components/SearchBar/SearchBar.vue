<template>
  <div>
    <SearchField @keywordSubmitted="addKeyword"
                 @inputfieldValueUpdated="updateCurrentKeyword">
    </SearchField>
    <SearchFilter @filterChanged="updateSelectedFilter"></SearchFilter>
    <KeywordList :keywords="selectedKeywords"></KeywordList>
    <button type="submit" @click="submitSearch">search</button>
  </div>
  <hr>
</template>

<script>
import SearchField from './SearchField.vue';
import SearchFilter from './SearchFilter.vue';
import KeywordList from './KeywordList.vue';

export default {
  name: 'SearchBar',
  components: {
    SearchField,
    SearchFilter,
    KeywordList,
  },
  data(){
    return {
      currentKeyword: '',
      selectedKeywords: [],
      selectedFilter: 0,
      searchResult: {
        isLoading: false,
        isLoaded: false,
        data: []
      }
    }
  },
  methods: {
    updateSelectedFilter(id) {
      this.selectedFilter = id;
    },
    updateCurrentKeyword(currentKeyword){
      this.currentKeyword = currentKeyword;
    },
    // TODO prevent adding same keyword twice
    addKeyword(keyword){
      this.selectedKeywords.push(keyword);
      this.currentKeyword = '';
    },
    // TODO
    removeKeyword(keyword) {
      const index = this.keywords.indexOf(keyword);
      if (index > -1) {
        this.selectedKeywords.splice(index, 1);
      }
    },
    // TODO
    removeAllKeywords(){
      this.selectedKeywords = [];
    },
    submitSearch(){
      const keywords = this.selectedKeywords.join(',');
      const affiliate = this.selectedFilter;

      const api_endpoint = 'researchgroup_search';
      const queryString = `?q=${keywords}&affiliate=${affiliate}`;

      this.searchResult.isLoading = true;
      this.axios.get(`https://bnm.docker.sh/${api_endpoint}${queryString}`)
       .then((result) => {
          console.log('result', result);
          this.searchResult.isLoading = false;
          this.searchResult.isLoaded = true;
          this.searchResult.data = result.data;
          this.$emit('searchCompleted', this.searchResult.data);
       })
       .catch((error) => {
          console.log('error',error);
          this.searchResult.isLoading = false;
          this.searchResult.isLoaded = true;
       });


    }
  }

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">

</style>
