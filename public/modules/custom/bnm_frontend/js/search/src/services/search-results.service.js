import axios from 'axios'
import { BACKEND_URL } from '../constants/backend-url';
import getInitialFrontendData from './initial-frontend-data.service';

export const getInitialSearchResults = async () => {
  try {
    const response = await getInitialFrontendData();

    const { initial_search_results } = response.data;

    return initial_search_results;
  } catch (error) {
    // eslint-disable-next-line
    console.error(error);
  }
}

export const getSearchResults = async (endpoint, query) => {
  try {
    const response = await axios.get(`${BACKEND_URL}/${endpoint}${query}`, {}, {
      headers: {
        'Content-type': 'application/json',
      },
    });

    const { data: results } = response;

    return results;
  } catch (error) {
    // eslint-disable-next-line
    console.error(error);
  }
}
