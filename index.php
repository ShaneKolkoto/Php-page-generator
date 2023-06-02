<?php

/*
Plugin Name: SMK Dash
Plugin URI:  https://your-plugin-website.com
Description: Wordpress plugin that create forms and generate the pages based on forms.
Version:     1.0
Author:      Shane Kolkoto
Author URI:  https://shanekolkotoportfolio.netlify.app/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: smk-dash
Domain Path: /languages
*/

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
  wp_register_style('css-ui',get_template_directory_uri().'/css/style.css' );
  wp_enqueue_style('css-ui' ); }


// Create the admin menu
function smk_dash_admin_menu() {
    add_menu_page(
        'SMK Dash',
        'SMK Dash',
        'manage_options',
        'smk-dash',
        'smk_dash_home_page',
        'dashicons-admin-generic',
        99
    );

    add_submenu_page(
        'smk-dash',
        'Home',
        'Home',
        'manage_options',
        'smk-dash',
        'smk_dash_home_page'
    );

    add_submenu_page(
        'smk-dash',
        'Users',
        'Users',
        'manage_options',
        'smk-dash-users',
        'smk_dash_users_page'
    );

    add_submenu_page(
        'smk-dash',
        'Settings',
        'Settings',
        'manage_options',
        'smk-dash-settings',
        'smk_dash_settings_page'
    );
}
// Running the function
add_action('admin_menu', 'smk_dash_admin_menu');

// Page Callbacks
// Home
function smk_dash_home_page() {
    echo '<div class="wrap">';
    echo '<h1>SMK Dash</h1>';
    echo '<p>Plugin description goes here.</p>';
    echo '</div>';
}
// Users
function smk_dash_users_page() {

    echo '<div class="wrap">';
    echo '<h1>Users</h1>';

    global $wpdb;

    // Fetch all users from the 'users' table
    $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");

    // Display the users in a table format
    if (!empty($users)) {
        echo '<table style=" width: 100%;
        border-collapse: collapse;">';
        echo '<thead><tr><th style="padding: 8px;
        border: 1px solid #ccc; background-color: #f2f2f2;
        font-weight: bold;">ID</th><th style="padding: 8px;
        border: 1px solid #ccc; background-color: #f2f2f2;
        font-weight: bold;">Username</th><th style="padding: 8px;
        border: 1px solid #ccc; background-color: #f2f2f2;
        font-weight: bold;">Email</th><th style="padding: 8px;
        border: 1px solid #ccc; background-color: #f2f2f2;
        font-weight: bold;">Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td style="padding: 8px;
            border: 1px solid #ccc;">' . $user->ID . '</td>';
            echo '<td style="padding: 8px;
            border: 1px solid #ccc;">' . $user->user_login . '</td>';
            echo '<td style="padding: 8px;
            border: 1px solid #ccc;">' . $user->user_email . '</td>';
            echo '<td style="padding: 8px;
            border: 1px solid #ccc;"><button class="view-button" data-user-id="' . $user->ID . '">View</button></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No users found.</p>';
    }

    echo '</div>';

    // Enqueue the JavaScript file
    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('smk-dash-script', plugins_url('scripts/main.js', __FILE__));
}
// Settings 
function smk_dash_settings_page() {
    echo '<div class="wrap">';
    echo '<h1>Settings</h1>';

    // Display navigation tabs
    echo '<h2 class="nav-tab-wrapper">';
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'login';
    echo '<a href="?page=smk-dash-settings&tab=login" class="nav-tab' . ($active_tab === 'login' ? ' nav-tab-active' : '') . '">Login</a>';
    echo '<a href="?page=smk-dash-settings&tab=forms" class="nav-tab' . ($active_tab === 'forms' ? ' nav-tab-active' : '') . '">Forms</a>';
    echo '</h2>';

    // Check which tab is active and display the corresponding settings section
    if (isset($_GET['tab'])) {
        if ($_GET['tab'] === 'forms') {
            smk_dash_display_forms_settings();
        } else {
            smk_dash_display_login_settings();
            echo '<style>.nav-tab.nav-tab-active {background-color: #f1f1f1;}</style>';
        }
    } else {
        smk_dash_display_login_settings();
        echo '<style>.nav-tab.nav-tab-active {background-color: #f1f1f1;}</style>';
    }

    echo '</div>';
}

// Admnin Dashboard sections
// Display forms login section
function smk_dash_display_login_settings() {
    echo '<div id="login" class="class="form-container"">';
    echo '<h2>Login Settings</h2>';

    // Check if the form is submitted
    if (isset($_POST['submit_login_settings'])) {
        // Process the form data and create the WordPress page
        $form_title = sanitize_text_field($_POST['form_title']);
        $required_fields = isset($_POST['required_fields']) ? $_POST['required_fields'] : array();

        // Create the WordPress page
        $page_id = wp_insert_post(array(
            'post_title' => $form_title,
            'post_content' => '[login_form]',
            'post_status' => 'publish',
            'post_type' => 'page'
        ));

        // Save the required fields as custom meta data for the page
        update_post_meta($page_id, 'required_fields', $required_fields);

        echo '<div class="notice notice-success"><p>Form settings saved successfully. Page created with ID: ' . $page_id . '</p></div>';
    }

    // Display the form
    echo '<form method="post">';
    echo '<label for="form_title">Form Title:</label><br>';
    echo '<input type="text" name="form_title" id="form_title"><br><br>';

    echo 'Required Fields:<br>';
    echo '<label><input type="checkbox" name="required_fields[]" value="username"> Username</label><br>';
    echo '<label><input type="checkbox" name="required_fields[]" value="email"> Email</label><br>';
    echo '<label><input type="checkbox" name="required_fields[]" value="password"> Password</label><br>';
    // Add additional fields as needed

    echo '<br>';
    submit_button('Save Settings', 'primary', 'submit_login_settings');
    echo '</form>';

    echo '</div>';
}
// Display forms settings section
function smk_dash_display_forms_settings() {
    echo '<div id="forms" class="smk-dash-tab-content">';
    echo '<h2>Forms Settings</h2>';
    
    // Fetch all the generated pages
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'required_fields', // Adjust the key based on your implementation
                'compare' => 'EXISTS',
            ),
        ),
    );
    $pages = get_posts($args);
    
    // Display the pages in a table format
    if (!empty($pages)) {
        echo '<table class="smk-dash-table" style=" width: 100%;
            border-collapse: collapse;">';
        echo '<thead><tr><th style="padding: 8px;
            border: 1px solid #ccc; background-color: #f2f2f2;
            font-weight: bold;">ID</th><th style="padding: 8px;
            border: 1px solid #ccc; background-color: #f2f2f2;
            font-weight: bold;">Title</th><th style="padding: 8px;
            border: 1px solid #ccc; background-color: #f2f2f2;
            font-weight: bold;">Required Fields</th><th style="padding: 8px;
            border: 1px solid #ccc; background-color: #f2f2f2;
            font-weight: bold;">Shortcode</th><th style="padding: 8px;
            border: 1px solid #ccc; background-color: #f2f2f2;
            font-weight: bold;">Delete</th></tr></thead>';
        echo '<tbody>';
        foreach ($pages as $page) {
            $page_title = get_the_title($page);
            $required_fields = get_post_meta($page->ID, 'required_fields', true);
    
            echo '<tr>';
            echo '<td class="smk-dash-table-cell" style="padding: 8px;
                border: 1px solid #ccc;">' . $page->ID . '</td>';
            echo '<td class="smk-dash-table-cell" style="padding: 8px;
                border: 1px solid #ccc;">' . $page_title . '</td>';
            echo '<td class="smk-dash-table-cell" style="padding: 8px;
                border: 1px solid #ccc;">' . implode(', ', $required_fields) . '</td>';
            echo '<td class="smk-dash-table-cell" style="padding: 8px;
                border: 1px solid #ccc;">[' . $page->post_name . ']</td>';
            echo '<td class="smk-dash-table-cell" style="padding: 8px; text-align: center;
                border: 1px solid #ccc;"><button style=" background-color: red; color: white; border: 1px soild black; border-radius: 10px" class="delete-button" data-page-id="' . $page->ID . '">Delete</button></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No generated pages found.</p>';
    }
    
    echo '</div>';

    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('smk-dash-script', plugins_url('scripts/main.js', __FILE__));
    
}


// Shortcodes
// Login [login_form]
function smk_dash_login_form_shortcode() {
    ob_start();
    // Retrieve the required fields from the custom meta data of the current page
    global $post;
    $required_fields = get_post_meta($post->ID, 'required_fields', true);

    // Code to generate the login form HTML based on the required fields
    ?>
    <form id="login-form" action="<?php echo esc_url(wp_login_url()); ?>" method="post">
        <?php if (in_array('username', $required_fields)) : ?>
            <p>
                <label for="username">Username:</label>
                <input type="text" name="log" id="username" required>
            </p>
        <?php endif; ?>
        <?php if (in_array('email', $required_fields)) : ?>
            <p>
                <label for="email">Email:</label>
                <input type="text" name="log" id="email" required>
            </p>
        <?php endif; ?>
        <?php if (in_array('password', $required_fields)) : ?>
            <p>
                <label for="password">Password:</label>
                <input type="password" name="pwd" id="password" required>
            </p>
        <?php endif; ?>
        <?php // Add additional required fields based on the selected options ?>

        <p>
            <input type="submit" value="Log In">
        </p>
    </form>
    <?php

    return ob_get_clean();
}
add_shortcode('login_form', 'smk_dash_login_form_shortcode');
