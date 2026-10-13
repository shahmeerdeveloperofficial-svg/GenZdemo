const fs = require('fs');
const content = fs.readFileSync('C:/Users/M-Shahmer/.gemini/antigravity/brain/1ec37954-1519-4be6-bd66-a58b29ec4ac2/.system_generated/steps/4/content.md', 'utf8');
const urls = [...content.matchAll(/https:\/\/cdn\.prod\.website-files\.com\/[^\s"'<>)]+/g)].map(m => m[0]);
console.log('Total URLs found:', urls.length);
const uniqueUrls = [...new Set(urls)];
uniqueUrls.forEach(u => {
  const lower = u.toLowerCase();
  if (lower.includes('hand') || lower.includes('bell') || lower.includes('ring') || lower.includes('cloud') || lower.includes('mac') || lower.includes('lal') || lower.includes('adm') || lower.includes('book') || lower.includes('icon') || lower.includes('tag')) {
    console.log(u);
  }
});
