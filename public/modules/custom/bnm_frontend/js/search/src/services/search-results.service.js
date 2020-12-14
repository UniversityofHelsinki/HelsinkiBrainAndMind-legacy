import axios from 'axios'
import { BACKEND_URL } from '../constans/backend-url';

const getSearchResults = async (endpoint, query) => {
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

export default getSearchResults;