import { getBaseUrl } from '../shared';
const url = getBaseUrl();

module.exports = {
  homepageLoads(browser) {
    browser
      .url(`${url}/`)
      .waitForElementVisible('body', 1000)
      .assert.containsText('#block-materialize-contenta-page-title', 'Welcome to Contenta CMS!')
      .end();
  }
};
