<?php
/*
Plugin Name: Simple Reygiri
Description: This is a Simple Reygiri WordPress plugin.
Version: 1.5.6
Author: Nima Amani <metananima@gmail.com>
*/

require_once('includes/input-data-functions.php');
require_once('includes/edit-data-functions.php');
require_once('includes/settings.php');

register_activation_hook(__FILE__, 'simple_reygiri_activation');
function simple_reygiri_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'simple_reygiri_settings';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        setting1 varchar(255) NOT NULL,
        setting2 varchar(255) NOT NULL,
        setting3 varchar(255) NOT NULL,
        setting4 varchar(255) NOT NULL,
        setting5 varchar(255) NOT NULL,
        setting6 varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}




add_action('admin_menu', 'my_plugin_add_admin_menu');

function my_plugin_add_admin_menu()
{
    add_menu_page(
        'افزونه ریگیری',           // Page title
        'افزونه ریگیری',                  // Menu title
        'manage_options',               // Capability
        'simple-reygiri',               // Menu slug
        'reygiri_main_function',   // Function that outputs the page content
        'dashicons-admin-generic',      // Icon URL (optional)
        6                               // Position (optional)
    );
}

function my_plugin_add_submenus()
{
    add_submenu_page(
        'simple-reygiri',      // Parent slug: must match the top-level menu slug
        'افزودن اطلاعات',      // Page title
        'افزودن اطلاعات',      // Menu title
        'manage_options',      // Capability
        'input-data',          // Menu slug
        'input_data_page'      // Function to display the submenu page content
    );
    add_submenu_page(
        'simple-reygiri',      // Parent slug: must match the top-level menu slug
        'ویرایش اطلاعات',      // Page title
        'ویرایش اطلاعات',      // Menu title
        'manage_options',      // Capability
        'edit-data',           // Menu slug
        'edit_data_page'       // Function to display the submenu page content
    );
}

add_action('admin_menu', 'my_plugin_add_submenus');

function reygiri_main_function()
{
    // $columnQueryNumber = substr(get_option('columnQuery',     'setting1'), 7);
    echo '<div class="wrap">';
    echo '<h1>به افزونه ریگیری خوش آمدید</h1>';
    echo '<p>آزمایشگاه تعیین عیار طلا درودگر</p>';
    echo '<p>شورتکد: <code>[simple_reygiri]</code></p>';
    // echo '<p>توجه داشته باشید که فرم جستجو بر اساس ورودی ستون شماره ' . $columnQueryNumber . ' می‌باشد.</p>';
    settings();
    echo '</div>';
}

function setMessage($message = "")
{
    global $errorMessage;
    $errorMessage = $message;
}

function showMessage()
{
    global $errorMessage;
    echo $errorMessage;
}

function input_data_page()
{
    echo '<div class="wrap">';
    echo '<h1>افزودن اطلاعات</h1>';
    showMessage();
    if (isset($_POST['upload']) && !empty($_FILES['file']['name'])) {
        $uploadedfile = $_FILES['file'];
        // Additional file validation checks here...

        $file_path = $uploadedfile['tmp_name']; // Or wherever the file is stored
        process_excel_file($file_path); // Process the uploaded file
    }
    if (isset($_POST['some_other_action'])) {
        simple_reygiri_settings_page(); // Process other form submissions
    }
    simple_reygiri_settings_page();
    echo '</div>';
}

function edit_data_page()
{
    echo '<div class="wrap">';
    echo '<h1>ویرایش اطلاعات</h1>';
    showMessage();
    if (isset($_POST['upload']) && !empty($_FILES['file']['name'])) {
        $uploadedfile = $_FILES['file'];
        // Additional file validation checks here...

        $file_path = $uploadedfile['tmp_name']; // Or wherever the file is stored
        process_excel_file($file_path); // Process the uploaded file
    }
    if (isset($_POST['some_other_action'])) {
        simple_reygiri_edit_page(); // Process other form submissions
    }
    simple_reygiri_edit_page();
    echo '</div>';
}

function simple_reygiri_shortcode()
{
    ob_start(); // Start output buffering

    $frontFormHeader    = get_option('frontFormHeader', 'Search You Number:');
    $frontFormBtn       = get_option('frontFormBtn', 'Search');
?>
    <form id="simple_reygiri_form" class="search-form" action="" method="post">
        <h2 class="search-form-header"><?php echo $frontFormHeader; ?></h2>
        <div class="search-form-inputs">
            <input type="text" name="search">
            <input type="submit" name="submit" class="button-primary" value="<?php echo $frontFormBtn; ?>">
        </div>
    </form>
    <div id="results_container"></div>
    <?php
    return ob_get_clean(); // Return the buffered output
}

function simple_reygiri_ajax_handler()
{
    global $wpdb;
    $table_name         = $wpdb->prefix . 'simple_reygiri_settings';
    $columnQuery        = get_option('columnQuery',     'setting1');
    $colHeader1         = get_option('colHeader1', 'تلفن گویا');
    $colHeader2         = get_option('colHeader2', 'شماره آزمایشگاه');
    $colHeader3         = get_option('colHeader3', 'عیار');
    $colHeader4         = get_option('colHeader4', 'شماره پاکت');
    $colHeader5         = get_option('colHeader5', 'نام آزمایشگاه');
    $colHeader6         = get_option('colHeader6', 'نام آزمایشگاه');
    $nothingFound       = get_option('nothingFound', 'داده ای یافت نشد...');
    $tableHeaderBg      = get_option('tableHeaderBg', '#f1eeea');
    $table_name = $wpdb->prefix . 'simple_reygiri_settings';
    if (isset($_POST['search'])) {
        $data = $wpdb->get_results("SELECT * FROM $table_name WHERE $columnQuery = '$_POST[search]'", ARRAY_A);
        if ($data) {
            echo '<table class="search-results-table">';
            // echo "<tr class='header-row' style='background-color: {$tableHeaderBg}'>
            echo "<tr class='header-row'>
                <th>{$colHeader1}</th>
                <th>{$colHeader2}</th>
                <th>{$colHeader3}</th>
                <th>{$colHeader4}</th>
                <th>{$colHeader5}</th>
                <th>{$colHeader6}</th>
                </tr>";
            foreach ($data as $row) {
                echo '<tr>';
                echo '<td>' . $row['setting1'] . '</td>';
                echo '<td>' . $row['setting2'] . '</td>';
                echo '<td>' . $row['setting3'] . '</td>';
                echo '<td>' . $row['setting4'] . '</td>';
                echo '<td>' . $row['setting5'] . '</td>';
                echo '<td>' . $row['setting6'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<h3 class="nothing-found-message">' . $nothingFound . '</h2>';
        }
    }
    ?>
    
<?php
    wp_die(); // Always include at the end of AJAX handlers
}

add_action('wp_ajax_simple_reygiri_ajax_handler', 'simple_reygiri_ajax_handler'); // Handling for logged-in users
add_action('wp_ajax_nopriv_simple_reygiri_ajax_handler', 'simple_reygiri_ajax_handler'); // Handling for non-logged-in users


function simple_reygiri_enqueue_scripts()
{
    wp_enqueue_script('simple_reygiri_ajax', plugin_dir_url(__FILE__) . 'js/simple-reygiri-ajax.js', array('jquery'), null, true);
    wp_localize_script('simple_reygiri_ajax', 'simple_reygiri_ajax_obj', array('ajaxurl' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'simple_reygiri_enqueue_scripts');

function simple_reygiri_enqueue_styles() {
    wp_enqueue_style('simple_reygiri', plugin_dir_url(__FILE__) . 'css/simple-reygiri.css');
}

add_action('wp_enqueue_scripts', 'simple_reygiri_enqueue_styles');


add_shortcode('simple_reygiri', 'simple_reygiri_shortcode');
