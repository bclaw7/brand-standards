const fs = require('fs');
const path = require('path');

// Ensure build directory exists
if (!fs.existsSync('build')) {
    fs.mkdirSync('build');
}

// Copy block.json to build directory
fs.copyFileSync(
    path.join('src', 'block.json'),
    path.join('build', 'block.json')
);

// Copy block.json to root directory
fs.copyFileSync(
    path.join('src', 'block.json'),
    'block.json'
);

console.log('Files copied successfully');