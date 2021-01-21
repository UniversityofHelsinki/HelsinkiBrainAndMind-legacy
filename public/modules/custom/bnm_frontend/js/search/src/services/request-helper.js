import axios from "axios";

export function initialDataRequest(environment) {
  const endpoint = '/initial-frontend-data';
  const axios = request(environment);
  return axios.get(endpoint);
}

export function searchResultRequest(apiendpoint, query, environment) {
  const endpoint = `/${apiendpoint}${query}`
  const axios = request(environment);
  return axios.get(endpoint);

}

export function searchSuggestionRequest(keyword, environment) {
  const endpoint = `/search_suggestions?q=${keyword}`;
  const axios = request(environment)
  return axios.get(endpoint);
}

export function getFooterData(environment){
  return initialDataRequest(environment);
}

function request(baseurl = 'https://research.helsinkibrainandmind.fi') {
  if (baseurl === null || baseurl.includes('localhost')) {
    // Needs to be changed/updated if app is standalone.
    baseurl = 'https://bnm.docker.sh';
  }

  const headers = {
    'Content-Type': 'application/json',
  };
  return axios.create({baseURL: baseurl, headers: headers});
}
