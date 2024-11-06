const fs = require('fs');
const archiver = require('archiver');

// Create zip file
const output = fs.createWriteStream('brand-standards.zip');
const archive = archiver('zip', {
    zlib: { level: 9 }
});

// Ignore patterns
const ignorePatterns = [
    '.DS_Store',
    'Thumbs.db',
    '.git',
    '.gitignore',
    'node_modules',
    '*.log'
];

output.on('close', () => {
    console.log(`Plugin zip has been created successfully`);
});

archive.on('error', (err) => {
    throw err;
});

archive.pipe(output);

// Create the plugin directory structure
const pluginPrefix = 'brand-standards';

// Add main plugin files
archive.file('brand-standards.php', { name: `${pluginPrefix}/brand-standards.php` });
archive.file('readme.txt', { name: `${pluginPrefix}/readme.txt` });
archive.file('LICENSE', { name: `${pluginPrefix}/LICENSE` });

// Add directories
// CSS
archive.directory('css/', `${pluginPrefix}/css`);

// JavaScript
archive.directory('js/', `${pluginPrefix}/js`);

// Includes
archive.directory('includes/', `${pluginPrefix}/includes`);

// Templates
archive.directory('templates/', `${pluginPrefix}/templates`);

// Patterns
archive.directory('patterns/', `${pluginPrefix}/patterns`);

archive.finalize();