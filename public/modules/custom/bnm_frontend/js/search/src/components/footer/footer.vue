<template>
  <footer class="footer" v-if="links.length > 0">
    <Container class="footer__container">
      <ul class="footer__items">
        <FooterItem :link="link.link" v-for="link in links" :key="link.link" :isExternal="link.external" :isDownloadable="link.downloadable">
          {{ link.title }}
        </FooterItem>
      </ul>
    </Container>
  </footer>
</template>

<script>
import Container from '../container/container.vue';
import FooterItem from './footer.item.vue';
import { watch } from '@vue/runtime-core'
import useInitialData from '../../stores/data';
import './footer.scss';

export default {
  name: 'Footer',
  components: {
    Container,
    FooterItem
  },
  data() {
    return {
      links: []
    }
  },
  async mounted() {
    const { results } = useInitialData();
    watch(()=>{
      this.links = results._object.footer_menu;
    });
  },
}
</script>
