const fs = require('fs');
const path = require('path');
const archiver = require('archiver');

// Create directory if it doesn't exist
function ensureDirectoryExists(dir) {
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
    }
}

// Create zip file
const output = fs.createWriteStream('brand-standards.zip');
const archive = archiver('zip', {
    zlib: { level: 9 }
});

output.on('close', () => {
    console.log('Plugin zip has been created successfully');
});

archive.on('error', (err) => {
    throw err;
});

archive.pipe(output);

// Add main plugin file
archive.file('brand-standards.php', { name: 'brand-standards/brand-standards.php' });
archive.file('block.json', { name: 'brand-standards/block.json' });
archive.file('readme.txt', { name: 'brand-standards/readme.txt' });

// Add build directory
archive.directory('build/', 'brand-standards/build');

// Add includes directory
archive.directory('includes/', 'brand-standards/includes');

// Add templates directory
archive.directory('templates/', 'brand-standards/templates');

// Add css directory
archive.directory('css/', 'brand-standards/css');

archive.finalize();