<template>
  <footer class="footer">
    <Container class="footer__container">
      <ul class="footer__items">
        <slot/>
      </ul>
    </Container>
  </footer>
</template>

<script>
import Container from '../container/container.vue';

export default {
  name: 'Footer',
  components: {
    Container
  }
}
</script>
