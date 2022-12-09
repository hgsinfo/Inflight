import { makeProxyRequest } from './wordpressApiClient';

const FORMS_PATH = `/forms/v2/forms`;

export interface IForm {
  guid: string;
  name: string;
}

export function fetchForms(searchQuery = '', offset = 0, limit = 10) {
  const queryParams: { [key: string]: any } = {
    offset,
    limit,
    formTypes: ['HUBSPOT'],
  };

  if (searchQuery) {
    queryParams.name__contains = searchQuery;
  }

  return makeProxyRequest('get', FORMS_PATH, {}, queryParams).then(
    (forms: IForm[]) => {
      const filteredForms: IForm[] = [];

      forms.forEach(currentForm => {
        const { guid, name } = currentForm;
        filteredForms.push({ name, guid });
      });

      return filteredForms;
    }
  );
}

export function createForm(payload: any) {
  return makeProxyRequest('post', FORMS_PATH, payload);
}
