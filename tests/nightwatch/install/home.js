import { getBaseUrl } from '../shared';
const url = getBaseUrl();

module.exports = {
  homepageLoads(browser) {
    browser
      .url(`${url}/`)
      .waitForElementVisible('#block-material-admin-page-title', 3000)
      .assert.containsText('#block-material-admin-page-title', 'Welcome to Contenta CMS!')
      .end();
  }
};
