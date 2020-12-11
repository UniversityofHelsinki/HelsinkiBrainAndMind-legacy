<template>
  <footer class="footer">
    <Container class="footer__container">
      <ul class="footer__items">
        <FooterItem :link="link.link" v-for="link in links" :key="link.link" :isExternal="link.external">
          {{ link.title }}
        </FooterItem>
      </ul>
    </Container>
  </footer>
</template>

<script>
import Container from '../container/container.vue';
import FooterItem from './footer.item.vue';
import './footer.scss';

const BACKEND_URL = process.env.VUE_APP_BACKEND_URL;

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
  mounted() {
    this.axios.get(`${BACKEND_URL}/initial-frontend-data`, {}, {
      headers: {
        'Content-type': 'application/json',
      },
    })
    .then(({data: { footer_menu: links}}) => {
      this.links = links;
    }).catch((error) => {
      // eslint-disable-next-line
      console.log(error);
    });
  }
}
</script>
