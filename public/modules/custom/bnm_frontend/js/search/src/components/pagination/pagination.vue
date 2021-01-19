<template>
    <div class="pagination">
      <button @click="this.previousPage()" class="pagination__button pagination__button--previous" :disabled="(currentPage - 1) === 0 ? true : false" aria-label="Go to previous page">
        <IconArrowBack/>
      </button>
      <nav role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination-triggers">
          <li v-for="paginationTrigger in paginationTriggers" :key="paginationTrigger" class="pagination-triggers__item">
            <PaginationTrigger :pageNumber="paginationTrigger" class="pagination-triggers__button" :class="{ 'is-active': paginationTrigger === currentPage}" :aria-label="paginationTrigger === currentPage ? 'Current page, Page ' + currentPage : 'Go to page ' +  paginationTrigger" :aria-current="paginationTrigger === currentPage ? true : false"></PaginationTrigger>
          </li>
        </ul>
      </nav>
      <button @click="this.nextPage()" class="pagination__button pagination__button--next" :disabled="currentPage === pageCount ? true : false" aria-label="Go to next page">
        <IconArrowForward/>
      </button>
    </div>
</template>

<script>
import useInitialData from '../../stores/data';
import PaginationTrigger from './pagination-trigger/pagination-trigger';
import IconArrowBack from '../../assets/icons/icon--arrow--back';
import IconArrowForward from '../../assets/icons/icon--arrow--forward';
import { watch } from '@vue/runtime-core';
import './pagination.scss';

export default {
  name: 'Pagination',
  components: {
    PaginationTrigger,
    IconArrowBack,
    IconArrowForward
  },
  data() {
    return {
      currentPage: 0,
      pageCount: 0,
    }
  },
  methods: {
    previousPage() {
      const { setPaginationPreviousPage } = useInitialData();
      setPaginationPreviousPage();

      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    },
    nextPage() {
      const { setPaginationNextPage } = useInitialData();
      setPaginationNextPage();

      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    },
  },
  mounted() {
    const { results } = useInitialData();

    watch(() => {
      this.currentPage = results._object.currentPage + 1;
      this.pageCount = results._object.pageCount + 1;
    });

    this.currentPage = results._object.currentPage + 1;
    this.pageCount = results._object.pageCount + 1;
  },
  computed: {
    paginationTriggers() {
      const { results } = useInitialData();

      const currentPage = results._object.currentPage + 1;
      const pageCount = results._object.pageCount + 1;
      const visiblePagesCount = 5
      const visiblePagesThreshold = (visiblePagesCount - 1) / 2
      const pagintationTriggersArray = Array(visiblePagesCount - 1).fill(0)

      if (currentPage <= visiblePagesThreshold + 1) {
        pagintationTriggersArray[0] = 1
        const pagintationTriggers = pagintationTriggersArray.map(
          (paginationTrigger, index) => {
            return pagintationTriggersArray[0] + index
          }
        )

        if (pageCount <= visiblePagesCount) {
          const stack = [];

          for (let i = 1; i <= pageCount; i++) {
            stack.push(i);
          }

          return stack;
        }

        pagintationTriggers.push(pageCount)

        return pagintationTriggers
      }

      if (currentPage >= pageCount - visiblePagesThreshold + 1) {
        const pagintationTriggers = pagintationTriggersArray.map(
          (paginationTrigger, index) => {
            return pageCount - index
          }
        )
        pagintationTriggers.reverse().unshift(1)

        return pagintationTriggers
      }

      pagintationTriggersArray[0] = currentPage - visiblePagesThreshold + 1
      const pagintationTriggers = pagintationTriggersArray.map(
        (paginationTrigger, index) => {
          return pagintationTriggersArray[0] + index
        }
      )
      pagintationTriggers.unshift(1);
      pagintationTriggers[pagintationTriggers.length - 1] = pageCount

      return pagintationTriggers
    }
  }
}
</script>
