import chromedriver from 'chromedriver';

module.exports = {
  asyncHookTimeout: 30000,
  before(done) {
    chromedriver.start();
    done();
  },
  after(done) {
    chromedriver.stop();
    done();
  }
};
