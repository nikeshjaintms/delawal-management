const fs = require('fs');
const path = require('path');
const root = path.join(__dirname, 'resources', 'views', 'admin');
function walk(dir) {
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap((entry) => {
    const full = path.join(dir, entry.name);
    return entry.isDirectory() ? walk(full) : entry.isFile() && full.endsWith('.blade.php') ? [full] : [];
  });
}
const pattern = /<td([^>]*)>(\s*<div class="table-action-buttons")/g;
let updatedCount = 0;
for (const file of walk(root)) {
  const text = fs.readFileSync(file, 'utf8');
  const newText = text.replace(pattern, (match, attrs, inner) => {
    if (/\bactions\b/.test(attrs) && /\baction-column\b/.test(attrs)) {
      return match;
    }
    if (/class\s*=\s*"([^"]*)"/.test(attrs)) {
      return '<td ' + attrs.replace(/class\s*=\s*"([^"]*)"/, (clsMatch, cls) => `class="${cls} actions action-column"`) + '>' + inner;
    }
    if (/class\s*=\s*'([^']*)'/.test(attrs)) {
      return '<td ' + attrs.replace(/class\s*=\s*'([^']*)'/, (clsMatch, cls) => `class='${cls} actions action-column'`) + '>' + inner;
    }
    return `<td class="actions action-column"${attrs}>${inner}`;
  });
  if (newText !== text) {
    fs.writeFileSync(file, newText, 'utf8');
    console.log('UPDATED', file);
    updatedCount += 1;
  }
}
console.log('TOTAL', updatedCount);
