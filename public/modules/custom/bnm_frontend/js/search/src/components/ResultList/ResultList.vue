<template>
    <div class="wrapper" v-if="results.length > 0">
        <div class="result" v-for="result in results" :key="result.id">
            <ul>
                <li>{{result}}</li>
            </ul>
        </div>
    </div>
    <div v-else>
        No results...
    </div>
</template>

<script>
  export default {
    name: 'ResultList',
    props: {
        results: Array
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
</style>