<template>
  <button @click="this.handleClick()">
    {{ pageNumber }}
  </button>
</template>

<script>
import useInitialData from '../../../stores/data';

export default {
  name: 'PaginationTrigger',
  props: {
    pageNumber: {
      type: Number,
      required: true
    }
  },
  methods: {
    handleClick() {
      const { setPaginationTriggerPage } = useInitialData();
      setPaginationTriggerPage(this.pageNumber);
    }
  }
}
</script>
