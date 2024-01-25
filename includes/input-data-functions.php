<?php

// require_once plugin_dir_path(__FILE__) . 'libraries/PhpSpreadsheet/src/Bootstrap.php';
require_once plugin_dir_path(__FILE__) . '/libraries/PhpSpreadsheet/src/PhpSpreadsheet/IOFactory.php';
require_once('settings.php');


function process_excel_file($file_path)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'simple_reygiri_settings';

    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_path);
    $spreadsheet = $reader->load($file_path);
    $worksheet = $spreadsheet->getActiveSheet();

    foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even empty ones

        $row_data = [];
        foreach ($cellIterator as $cell) {
            $row_data[] = $cell->getValue();
        }

        // Skip header row or empty rows
        if ($row->getRowIndex() == 1 || count(array_filter($row_data)) == 0) {
            continue;
        }

        // Insert data into the database
        $wpdb->insert($table_name, array(
            'setting1' => $row_data[0],
            'setting2' => $row_data[1],
            'setting3' => $row_data[2],
            'setting4' => $row_data[3],
            'setting5' => $row_data[4],
            'setting6' => $row_data[5],
        ));
    }
}

function simple_reygiri_settings_page()
{
    global $errorMessage;
    global $wpdb;
    $table_name = $wpdb->prefix . 'simple_reygiri_settings';

    if (isset($_POST['remove_row'])) {
        foreach ($_POST['remove_row'] as $row_id => $value) {
            $wpdb->delete($table_name, array('id' => $row_id));
            break; // Stop after the first iteration since only one button can be clicked
        }
    }

    if (isset($_POST['add_row'])) {
        $new_data = array(
            'setting1' => sanitize_text_field($_POST['new_setting1']),
            'setting2' => sanitize_text_field($_POST['new_setting2']),
            'setting3' => sanitize_text_field($_POST['new_setting3']),
            'setting4' => sanitize_text_field($_POST['new_setting4']),
            'setting5' => sanitize_text_field($_POST['new_setting5']),
            'setting6' => sanitize_text_field($_POST['new_setting6']),
        );
        $existing_setting2 = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE setting2 = '{$new_data['setting2']}'");
        if ($existing_setting2 > 0) {
            setMessage('این شماره پاکت قبل ثبت شده. لطفا شماره پاکت جدید را وارد کنید.');
        } else {
            $wpdb->insert($table_name, $new_data);
        }
    }

    if (isset($_POST['submit'])) { // Assuming your save button has name="submit"
        foreach ($_POST['setting1'] as $row_id => $setting1_value) {
            // Sanitize the input values
            $sanitized_setting1 = sanitize_text_field($setting1_value);
            $sanitized_setting2 = isset($_POST['setting2'][$row_id]) ? sanitize_text_field($_POST['setting2'][$row_id]) : '';
            $sanitized_setting3 = isset($_POST['setting3'][$row_id]) ? sanitize_text_field($_POST['setting3'][$row_id]) : '';
            $sanitized_setting4 = isset($_POST['setting4'][$row_id]) ? sanitize_text_field($_POST['setting4'][$row_id]) : '';
            $sanitized_setting5 = isset($_POST['setting5'][$row_id]) ? sanitize_text_field($_POST['setting5'][$row_id]) : '';
            $sanitized_setting6 = isset($_POST['setting6'][$row_id]) ? sanitize_text_field($_POST['setting6'][$row_id]) : '';
            // Add similar lines for other settings if they exist

            // Prepare the data for update
            $data_to_update = array(
                'setting1' => $sanitized_setting1,
                'setting2' => $sanitized_setting2,
                'setting3' => $sanitized_setting3,
                'setting4' => $sanitized_setting4,
                'setting5' => $sanitized_setting5,
                'setting6' => $sanitized_setting6,
                // Add other settings here
            );

            // Specify which row to update
            $where = array('id' => intval($row_id));

            // Update the row in the database
            $wpdb->update($table_name, $data_to_update, $where);
        }
        // Add any additional logic or notifications needed after update
    }

    if (isset($_POST['upload']) && !empty($_FILES['file']['name'])) {
        $uploadedfile = $_FILES['file'];

        // Check if the file is an Excel file
        $file_type = wp_check_filetype(basename($uploadedfile['name']));
        if (!in_array($file_type['ext'], ['xls', 'xlsx'])) {
            // Handle error - file is not an Excel file
        }

        // Use WordPress's upload handling function
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            // File is uploaded successfully. You can now process it.
            $file_path = $movefile['file'];
            process_excel_file($file_path);
            echo "File uploaded successfully: " . $file_path;
        } else {
            // Handle error - file upload failed
            echo "File upload failed: " . $movefile['error'];
        }
    }
    $existing_data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
?>
    <div>
        <h2>افزودن اطلاعات جدید:</h2>
        <form method="post" action="" id="simple-reygiri-settings-form">
            <table class="form-table" id="simple-reygiri-table">
                <?php
                $colHeader1         = get_option('colHeader1',      'نام آزمایشگاه');
                $colHeader2         = get_option('colHeader2',      'شماره پاکت');
                $colHeader3         = get_option('colHeader3',      'عیار');
                $colHeader4         = get_option('colHeader4',      'شماره آزمایشگاه');
                $colHeader5         = get_option('colHeader5',      'نام و نام خانوادگی');
                $colHeader6         = get_option('colHeader6',      'تلفن گویا');
                ?>
                <tr valign="top">
                    <!-- <th class="firstCol" scope="row">ID</th> -->
                    <th scope="row"><?php echo $colHeader1; ?></th>
                    <th scope="row"><?php echo $colHeader2; ?></th>
                    <th scope="row"><?php echo $colHeader3; ?></th>
                    <th scope="row"><?php echo $colHeader4; ?></th>
                    <th scope="row"><?php echo $colHeader5; ?></th>
                    <th scope="row"><?php echo $colHeader6; ?></th>
                    <th scope="row"></th>
                </tr>
                <tr valign="top">
                    <!-- <td></td> -->
                    <td><input type="text" name="new_setting1" value="" /></td>
                    <td><input type="text" name="new_setting2" value="" /></td>
                    <td><input type="text" name="new_setting3" value="" /></td>
                    <td><input type="text" name="new_setting4" value="" /></td>
                    <td><input type="text" name="new_setting5" value="" /></td>
                    <td><input type="text" name="new_setting6" value="" /></td>
                    <td>
                        <input type="submit" name="add_row" value="افزودن" class="button-primary" />
                    </td>
                </tr>
                <?php
                foreach ($existing_data as $data) {
                    echo '<tr valign="top">' .
                        '<td>' . esc_attr($data['setting1']) . '</td>' .
                        '<td>' . esc_attr($data['setting2']) . '</td>' .
                        '<td>' . esc_attr($data['setting3']) . '</td>' .
                        '<td>' . esc_attr($data['setting4']) . '</td>' .
                        '<td>' . esc_attr($data['setting5']) . '</td>' .
                        '<td>' . esc_attr($data['setting6']) . '</td>' .
                        '<td></td>' .
                        '</tr>';
                }
                ?>
            </table>
        </form>
    </div>
    <br>
    <?php showMessage(); ?>
    <div>
        <!-- <h2>Insert Data From Excel File:</h2> -->
        <!-- <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="file" accept=".xls,.xlsx">
            <input type="submit" name="upload" value="Upload" class="button-primary">
        </form> -->
    </div>
    <script>
    </script>
<?php
}
