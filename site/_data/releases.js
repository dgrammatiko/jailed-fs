const fs = require('fs');
const crypto = require('crypto');

module.exports = async () => {
  const dir = 'packages';
  let rels = JSON.parse(fs.readFileSync('site/releases.json'));
  const files = await fs.promises.readdir(dir);

  rels.map(rel => {
    if (files.includes(`plg_system_restrictedfs_v${rel.version}.zip`)) {
      rel.sha512 = crypto.createHash('sha512').update(fs.readFileSync(`${dir}/plg_system_restrictedfs_v${rel.version}.zip`)).digest('hex');
    }
  });

  return rels;
}
