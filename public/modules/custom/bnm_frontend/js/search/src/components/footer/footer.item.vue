<template>
  <li class="footer__item">
    <CustomLink :link="link" class="footer__link" :isBlank="isExternal" :isDownloadable="isDownloadable"><slot/></CustomLink>
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
    link: String,
    isExternal: Boolean,
    isDownloadable: Boolean
  }
}
</script>
