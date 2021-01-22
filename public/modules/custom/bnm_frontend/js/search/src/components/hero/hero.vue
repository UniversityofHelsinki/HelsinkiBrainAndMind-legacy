<template>
  <div class="hero">
    <Container>
      <h1 class="hero__title">{{ title }}</h1>
      <h2 class="hero__lead"><slot/></h2>
    </Container>
  </div>
</template>

<script>
import Container from '../container/container'
import './hero.scss';

export default {
  name: 'Hero',
  components: {
    Container,
  },
  props: {
    title: String,
  }
}
</script>
