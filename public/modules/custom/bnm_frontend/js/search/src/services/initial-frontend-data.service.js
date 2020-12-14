import axios from 'axios'
import { BACKEND_URL } from '../constans/backend-url';

const getInitialFrontendData = async () => {
  try {
    const response = await axios.get(`${BACKEND_URL}/initial-frontend-data`, {}, {
      headers: {
        'Content-type': 'application/json',
      },
    });

    return response;
  } catch (error) {
    // eslint-disable-next-line
    console.error(error);
  }
}

export default getInitialFrontendData;