<template>
  <ul class="listing" :class="{'listing--col-3': col3}">
    <slot/>
  </ul>
</template>

<script>
import './listing.scss';

export default {
  name: 'Listing',
  props: {
    col3: Boolean
  }
}
</script>
