import axios from 'axios'
import { BACKEND_URL } from '../constans/backend-url';

const getSearchSuggestions = async (keyword) => {
  try {
    const response = await axios.get(`${BACKEND_URL}/search_suggestions?q=${keyword}`, {}, {
      headers: {
        'Content-type': 'application/json',
      },
    });

    const { data: suggestions } = response;

    return suggestions;
  } catch (error) {
    // eslint-disable-next-line
    console.error(error);
  }
}

export default getSearchSuggestions;