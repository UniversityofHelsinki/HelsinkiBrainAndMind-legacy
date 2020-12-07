<template>
    <div v-if="keywords.length > 0">
        selected keywords:
        <ul>
            <li v-for="keyword in keywords" v-bind:key="keyword">{{keyword}}</li>
        </ul>
    </div>


</template>

<script>
  export default {
    name: 'KeywordList',
    props: {
      keywords: Array
    },
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
</style>