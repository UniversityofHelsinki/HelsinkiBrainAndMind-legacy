<template>
  <li class="footer__item">
    <CustomLink :link="link" class="footer__link"><slot/></CustomLink>
  </li>
</template>

<script>
import CustomLink from '../custom-link/custom-link.vue';

export default {
  name: 'FooterItem',
  components: {
    CustomLink
  },
  props: {
    link: String
  }
}
</script>
