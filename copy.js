const fs = require('fs');
const path = require('path');

// Ensure build directory exists
if (!fs.existsSync('build')) {
    fs.mkdirSync('build');
}

// Define files to copy
const filesToCopy = [
    {
        src: path.join('src', 'block.json'),
        dest: path.join('build', 'block.json')
    },
    {
        src: path.join('src', 'block.json'),
        dest: 'block.json'
    },
    {
        src: path.join('src', 'logo-tabs', 'block.json'),
        dest: path.join('build', 'logo-tabs', 'block.json')
    }
];

// Create logo-tabs directory in build if it doesn't exist
if (!fs.existsSync(path.join('build', 'logo-tabs'))) {
    fs.mkdirSync(path.join('build', 'logo-tabs'), { recursive: true });
}

// Copy files
filesToCopy.forEach(file => {
    if (fs.existsSync(file.src)) {
        fs.copyFileSync(file.src, file.dest);
        console.log(`Copied ${file.src} to ${file.dest}`);
    } else {
        console.warn(`Warning: Source file ${file.src} does not exist`);
    }
});

console.log('Files copied successfully');