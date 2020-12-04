<template>
    <Header>
        <Input/>
        <Dropdown/>
    </Header>
</template>

<script>
  import Input from './Input.vue';
  import Dropdown from './Dropdown.vue';

  export default {
    name: 'SearchBar',
    components: {
        Input,
        Dropdown,
    },
    props: {
      msg: String
    }
  }
</script>

<style lang="scss">
</style>
