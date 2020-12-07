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
    addKeyword(){
      this.selectedKeywords.push(this.currentKeyword);
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
      console.log('SUBMIT QUERY');
      console.log('lets search! Keywords: ',this.currentKeyword, this.selectedKeywords, 'from affiliation:', this.selectedFilter);
      /*
      const keywords = this.keywords.join(',');
      const affiliate = this.selectedFilter;



      const api_endpoint = 'researchgroup_search';
      const queryString = `?q=${keywords}&affiliate=${affiliate}`;
      this.searchResult.isLoading = true;
      // `http://Brain:bnm_2020@dev.bnm.druidfi.wod.by/${api_endpoint}${queryString}`
      // `bnm.docker.sh/${api_endpoint}${queryString}`

      this.$http.axios.get(`bnm.docker.sh/${api_endpoint}${queryString}`)
       .then((result) => {
          this.searchResult.isLoading = false;
          this.searchResult.isLoaded = true;
          this.searchResult.data = result.body;
       })
       .catch(() => {
          this.searchResult.isLoading = false;
          this.searchResult.isLoaded = true;
       });

       */
      this.searchResult.data = [{id: 1, name: 'test', main_affiliation: 'HY'}]
      this.$emit('searchCompleted', this.searchResult.data);
      console.log('RETURNED VALUE:', this.searchResult.data);
    }
  }

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">

</style>
