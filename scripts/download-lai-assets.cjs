const fs = require('fs');
const path = require('path');
const https = require('https');

const destDir = path.join(__dirname, '..', 'public', 'assets', 'images');
if (!fs.existsSync(destDir)) {
  fs.mkdirSync(destDir, { recursive: true });
}

const assets = [
  {
    name: 'mac-holdings-hands.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a0c6885098343af9e178600_dbc31723b8a5f673d32599bd54639147_mac-holdings-hands.webp'
  },
  {
    name: 'mac-screen.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a0c5f9c0d5badec015d4502_9bc3edd634bef51ee7929c33b8d95d89_mac-screen.webp'
  },
  {
    name: 'ring.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a09fa46b84cb43de0c43134_ba96536fda245c2cc2a1887d784bb20a_ring.webp'
  },
  {
    name: 'ring-bell.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a09fabe2b637c4230a27458_b93b34e79fd6de99259abea947221bc8_ring-bell.webp'
  },
  {
    name: 'lal-card-icon-1.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a09f144745bb701c7a7ef8e_886ee8c56587928eded653a6603cd1f4_lal-card-icon-1.webp'
  },
  {
    name: 'lal-card-icon-2.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a09f315f7d1f0234b818a8e_5c538c19be77f7d9b430e93b014fd226_lal-card-icon-2.webp'
  },
  {
    name: 'lal-card-icon-3.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a09f3149f239d2931619b7f_47ff28a8431cd4037fb34bc9b299195a_lal-card-icon-3.webp'
  },
  {
    name: 'lal-card-icon-4.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a09f3155005e0eb8aea733a_f8bd3f6693acdd6809503ecc96f48aff_lal-card-icon-4.webp'
  },
  {
    name: 'cream-top-cloud.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a102440165031ecf11db9e9_cream-top-cloud.webp'
  },
  {
    name: 'cream-bottom-cloud.webp',
    url: 'https://cdn.prod.website-files.com/6a06cefbe4aa4adf17559743/6a102440a5866121e04a0a68_cream-bottom-cloud.webp'
  }
];

function download(item) {
  return new Promise((resolve, reject) => {
    const filePath = path.join(destDir, item.name);
    https.get(item.url, (res) => {
      if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
        return https.get(res.headers.location, (redirectRes) => {
          const fileStream = fs.createWriteStream(filePath);
          redirectRes.pipe(fileStream);
          fileStream.on('finish', () => { fileStream.close(); console.log('Downloaded:', item.name); resolve(); });
        }).on('error', reject);
      }
      if (res.statusCode !== 200) {
        return reject(new Error(`Status ${res.statusCode} for ${item.name}`));
      }
      const fileStream = fs.createWriteStream(filePath);
      res.pipe(fileStream);
      fileStream.on('finish', () => {
        fileStream.close();
        console.log('Downloaded:', item.name);
        resolve();
      });
    }).on('error', reject);
  });
}

async function main() {
  console.log('Downloading LA International reference assets...');
  for (const a of assets) {
    try {
      await download(a);
    } catch (e) {
      console.error('Failed:', a.name, e.message);
    }
  }
  console.log('Done downloading assets.');
}

main();
