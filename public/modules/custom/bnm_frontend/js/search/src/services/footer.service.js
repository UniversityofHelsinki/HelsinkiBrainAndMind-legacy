import getInitialFrontendData from './initial-frontend-data.service';

const getFooterMenuItems = async () => {
  try {
    const response = await getInitialFrontendData();

    const { footer_menu } = response.data;

    return footer_menu;
  } catch (error) {
    // eslint-disable-next-line
    console.error(error);
  }
}

export default getFooterMenuItems;
