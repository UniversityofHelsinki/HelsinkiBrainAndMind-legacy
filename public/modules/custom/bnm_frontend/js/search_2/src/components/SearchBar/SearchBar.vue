<template>
    <header>
        <Input />
        <Dropdown/>
        <KeywordList :keywords="keywords"></KeywordList>
    </header>
</template>

<script>
  import Input from './Input.vue';
  import Dropdown from './Dropdown.vue';
  import KeywordList from './KeywordList.vue';

  export default {
    name: 'SearchBar',
    components: {
        Input,
        Dropdown,
        KeywordList
    },
    data() {
      return {
        keywords: []
      }
    },
    methods: {
      addKeyword(keyword) {
        console.log('asd');
        this.keywords.push(keyword)
      }
    }
  }
</script>

<style lang="scss">
</style>
