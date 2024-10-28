<?php
// Save this as check-structure.php in your plugin root directory

function check_plugin_structure() {
    $required_directories = array(
        'templates',
        'includes',
        'css',
        'build'
    );

    $required_files = array(
        'templates/single-brand-standard.php',
        'includes/brand-standard-functions.php',
        'css/brand-standards.css',
        'brand-standards.php'
    );

    echo "Checking plugin structure...\n\n";

    // Check directories
    echo "Checking directories:\n";
    foreach ($required_directories as $dir) {
        if (is_dir($dir)) {
            echo "✓ Found directory: $dir\n";
        } else {
            echo "✗ Missing directory: $dir\n";
        }
    }

    echo "\nChecking files:\n";
    // Check files
    foreach ($required_files as $file) {
        if (file_exists($file)) {
            echo "✓ Found file: $file\n";
        } else {
            echo "✗ Missing file: $file\n";
        }
    }
}

check_plugin_structure();