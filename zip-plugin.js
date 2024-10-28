const fs = require('fs');
const path = require('path');
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

// Function to add directory with ignore patterns
function addDirectoryToArchive(directory, archivePath) {
    archive.glob('**/*', {
        cwd: directory,
        dot: false,
        ignore: ignorePatterns,
    }, { prefix: `brand-standards/${archivePath}` });
}

// Add main plugin files
archive.file('brand-standards.php', { name: 'brand-standards/brand-standards.php' });
archive.file('block.json', { name: 'brand-standards/block.json' });
archive.file('readme.txt', { name: 'brand-standards/readme.txt' });

// Add directories with ignore patterns
addDirectoryToArchive('build', 'build');
addDirectoryToArchive('includes', 'includes');
addDirectoryToArchive('templates', 'templates');
addDirectoryToArchive('css', 'css');
addDirectoryToArchive('js', 'js');

archive.finalize();