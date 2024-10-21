<?php
require_once plugin_dir_path(__FILE__) . '../lib/google-api-php-client--PHP8.0/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;
use Google\Service\Exception as Google_Service_Exception;
use Google\Exception as Google_Exception;

function brand_standards_get_drive_files($folder_id) {
    try {
        $client = new Client();
        $client->setApplicationName('Brand Standards Plugin');
        $client->setScopes(Drive::DRIVE_READONLY);
        $client->setAuthConfig(plugin_dir_path(__FILE__) . '../secure/credentials.json');

        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $tokenPath = plugin_dir_path(__FILE__) . '../secure/token.json';
        
        // Check if we have a saved token
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            error_log('Access token has expired or is not available. Attempting to refresh or create new token...');
            
            // If we have a refresh token, try to use it
            if ($client->getRefreshToken()) {
                error_log('Refresh token found. Attempting to refresh access token.');
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // If we don't have a refresh token, we need to get one
                error_log('No refresh token available. Initiating OAuth flow.');
                
                // Your redirect URI can be any registered one in your Google Cloud Console
                $redirect_uri = site_url('wp-admin/admin-ajax.php') . '?action=google_drive_oauth_callback';
                error_log('Redirect URI: ' . $redirect_uri);
                $client->setRedirectUri($redirect_uri);
                
                // Get authorization URL
                $auth_url = $client->createAuthUrl();
                error_log('Authorization URL: ' . $auth_url);
                
                // At this point, you would typically redirect the user to $auth_url
                // For debugging purposes, we'll throw an exception with instructions
                throw new Exception('Authentication required. Please visit the following URL to authenticate: ' . $auth_url);
            }
            
            // Save the token
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            error_log('New access token saved.');
        }

        $service = new Drive($client);
        
        $optParams = array(
            'q' => "'" . $folder_id . "' in parents and trashed = false",
            'fields' => 'files(id, name, webViewLink, mimeType, thumbnailLink)',
            'pageSize' => 1000
        );
        
        $results = $service->files->listFiles($optParams);
        
        error_log('API Response: ' . json_encode($results));
        
        return $results->getFiles();
    } catch (Google_Service_Exception $e) {
        error_log('Google Service Exception: ' . $e->getMessage());
        error_log('Error details: ' . json_encode($e->getErrors()));
        return null;
    } catch (Google_Exception $e) {
        error_log('Google Exception: ' . $e->getMessage());
        return null;
    } catch (Exception $e) {
        error_log('Exception: ' . $e->getMessage());
        return null;
    }
}



function handle_google_drive_oauth_callback() {
    if (!isset($_GET['code'])) {
        wp_die('Authorization code not received');
    }

    $client = new Client();
    $client->setApplicationName('Brand Standards Plugin');
    $client->setScopes(Drive::DRIVE_READONLY);
    $client->setAuthConfig(plugin_dir_path(__FILE__) . '../secure/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $redirect_uri = admin_url('admin-ajax.php?action=google_drive_oauth_callback');
    $client->setRedirectUri($redirect_uri);

    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        wp_die('Error fetching access token: ' . $token['error_description']);
    }

    // Save the token
    $tokenPath = plugin_dir_path(__FILE__) . '../secure/token.json';
    if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($token));

    echo 'Authentication successful! You can close this window.';
    exit;
}
add_action('wp_ajax_google_drive_oauth_callback', 'handle_google_drive_oauth_callback');
add_action('wp_ajax_nopriv_google_drive_oauth_callback', 'handle_google_drive_oauth_callback');


function brand_standards_display_campaign_assets($content) {
    if (!is_singular('campaign')) {
        return $content;
    }

    global $post;
    $folder_id = get_post_meta($post->ID, '_campaign_drive_folder_id', true);
    
    if (empty($folder_id)) {
        error_log('No folder ID found for campaign: ' . $post->ID);
        return $content;
    }

    $files = brand_standards_get_drive_files($folder_id);
    
    if ($files === null) {
        error_log('Error fetching files from Google Drive for campaign: ' . $post->ID);
        return $content . '<p>Error: Unable to fetch campaign assets. Please try again later.</p>';
    }
    
    if (empty($files)) {
        error_log('No files found in Google Drive folder for campaign: ' . $post->ID);
        return $content . '<p>No campaign assets found.</p>';
    }
    
    $assets_html = '<h3>Campaign Assets</h3><div class="campaign-assets">';
    foreach ($files as $file) {
        $assets_html .= '<div class="asset-item">';
        $assets_html .= '<h4>' . esc_html($file->getName()) . '</h4>';
        
        // Add preview image
        if ($file->getThumbnailLink()) {
            $assets_html .= '<img src="' . esc_url($file->getThumbnailLink()) . '" alt="' . esc_attr($file->getName()) . '" class="asset-preview">';
        } else {
            // Use a default image if no thumbnail is available
            $assets_html .= '<img src="' . esc_url(plugins_url('../assets/no-preview.jpeg', __FILE__)) . '" alt="No preview available" class="asset-preview">';
        }
        
        // Add download link
        $assets_html .= '<a href="' . esc_url($file->getWebViewLink()) . '" target="_blank" class="asset-download">Download / View in Google Drive</a>';
        
        $assets_html .= '</div>';
    }
    $assets_html .= '</div>';

    return $content . $assets_html;
}
add_filter('the_content', 'brand_standards_display_campaign_assets');