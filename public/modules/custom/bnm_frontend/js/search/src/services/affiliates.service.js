import getInitialFrontendData from './initial-frontend-data.service';

const getAffiliates = async () => {
  try {
    const response = await getInitialFrontendData();

    const { affiliates } = response.data;

    return affiliates;
  } catch (error) {
    // eslint-disable-next-line
    console.error(error);
  }
}

export default getAffiliates;
