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

      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
  }
}
</script>
