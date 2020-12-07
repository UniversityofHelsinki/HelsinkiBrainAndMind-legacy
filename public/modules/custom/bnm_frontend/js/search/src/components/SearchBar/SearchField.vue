<template>
    <div class="hello">
        <input
                type="text"
                :value="currentKeywordValue"
                @input="autoComplete"
                @keyup.enter="submitKeyword"
                placeholder="Search...">
        <div v-if="autocompleteList" class="autocompletelist" >
            <ul>
                <li v-for="word in autocompleteList" v-bind:key="word" @click="setInputValue">{{word}}</li>
            </ul>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'SearchField',
    emits: ['keywordSubmitted', 'inputfieldValueUpdated'],
    data(){
      return {
        autocompleteList: [],
        currentKeywordValue: ''
      }
    },
    methods: {
      autoComplete(event){
        this.updateKeyword(event);
        // TODO add throttling
        if(event.target.value.length > 2) {
          //this.autocompleteList =  this.$http.axios.get().then().catch();
          this.autocompleteList = ['asd', 'boohoo'];
          this.currentKeywordValue = event.target.value;
        }
      },
      updateKeyword(event){
        this.$emit('inputfieldValueUpdated', event.target.value);
      },
      setInputValue(event){
        this.$emit('keywordSubmitted', event.target.innerHTML);
        this.resetAutocomplete();
      },
      submitKeyword(event){
        this.$emit('keywordSubmitted', event.target.value);
        this.resetAutocomplete();
      },
      resetAutocomplete(){
        this.autocompleteList = [];
        this.currentKeywordValue = '';
      }
    },
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
</style>
