
const pakage = require('../../package.json');

module.exports = () => {
  return {
    url: 'https://restrictedfs.dgrammatiko.dev',
    repo: pakage.data.repo,
    version: pakage.version,
    title: 'Restricted File System, for Joomla\'s Media Manager',
    sha256: pakage.data.sha256,
    sha384: pakage.data.sha384,
    sha512: pakage.data.sha512,
  };
}
