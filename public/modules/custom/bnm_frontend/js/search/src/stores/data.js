import { reactive, toRefs } from 'vue';
// eslint-disable-next-line
import { initialDataRequest, searchResultRequest } from '../services/request-helper';
import chunkArray from '../utils/chunkArray';

const state = reactive({
  affiliates: [],
  footer_menu: [],
  results: [],
  resultsLoadingStatus: false,
  currentPage: 0,
  pageCount: 0,
});

export default function useInitialData(environment){
  state.environment = environment;

  const fetchInitialData = async (environment) => {
    await initialDataRequest(environment)
    .then((response) => {
      state.affiliates = response.data.affiliates;
      state.footer_menu = response.data.footer_menu;
      state.results = chunkArray(response.data.initial_search_results, 20);
      state.resultsLoadingStatus = false;
      state.pageCount = chunkArray(response.data.initial_search_results, 20).length - 1;
      state.currentPage = 0;
    })
    .catch((error) => {
      return error
    });
  };

  const fetchResults = async (endpoint, query, environment) => {
    searchResultRequest(endpoint, query, environment)
    .then((response) => {
      setResultsLoadingStatus();
      state.currentPage = 0;
      state.pageCount = chunkArray(response.data, 20).length - 1;
      state.results = chunkArray(response.data, 20);
    })
    .catch((error) => {
      // eslint-disable-next-line
      console.log(error);
    });
  };

  const getResultsCount = () => state.results.flat().length;

  const resetResults = () => {
    state.results = [];
  }

  const setResultsLoadingStatus = () => {
    state.resultsLoadingStatus = !state.resultsLoadingStatus;
  };

  const setPaginationNextPage = () => {
    if (state.currentPage === state.pageCount) return;
    state.currentPage = state.currentPage + 1;
  }

  const setPaginationPreviousPage = () => {
    if (state.currentPage === 0) return;
    state.currentPage = state.currentPage - 1;
  }

  const setPaginationTriggerPage = (pageNumber) => {
    state.currentPage = pageNumber - 1;
  }

  return {
    ...toRefs(state),
    fetchInitialData,
    fetchResults,
    resetResults,
    setResultsLoadingStatus,
    setPaginationNextPage,
    setPaginationPreviousPage,
    setPaginationTriggerPage,
    getResultsCount
  }
}
