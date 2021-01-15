import { reactive, toRefs } from 'vue';
// eslint-disable-next-line
import { initialDataRequest, searchResultRequest } from '../services/request-helper';

const state = reactive({
  // environment: 'https://bnm.docker.sh/',
  affiliates: [],
  footer_menu: [],
  results: [],
});

export default function useInitialData(environment){
  state.environment = environment;

  const fetchInitialData = async (environment) => {
    await initialDataRequest(environment)
    .then((response) => {
      state.affiliates = response.data.affiliates;
      state.footer_menu = response.data.footer_menu;
      state.results = response.data.initial_search_results;
    })
    .catch((error) => {
      return error
    });
  };

  const fetchResults = async (endpoint, query, environment) => {
    searchResultRequest(endpoint, query, environment)
    .then((response) => {
      state.results = response.data;
    })
    .catch(() => {
      // eslint-disable-next-line
      console.log(error);
    });
  };

  return {
    ...toRefs(state),
    fetchInitialData,
    fetchResults
  }
}
