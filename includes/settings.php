<?php
require_once('input-data-functions.php');



function settings()
{

    if (isset($_POST['save'])) {
        // Sanitize and save the settings
        update_option('columnQuery',        sanitize_text_field($_POST['column-query']));
        update_option('colHeader1',         sanitize_text_field($_POST['setting1']));
        update_option('colHeader2',         sanitize_text_field($_POST['setting2']));
        update_option('colHeader3',         sanitize_text_field($_POST['setting3']));
        update_option('colHeader4',         sanitize_text_field($_POST['setting4']));
        update_option('colHeader5',         sanitize_text_field($_POST['setting5']));
        update_option('colHeader6',         sanitize_text_field($_POST['setting6']));
        update_option('frontFormHeader',    sanitize_text_field($_POST['front-form-header']));
        update_option('frontFormBtn',       sanitize_text_field($_POST['search-button']));
        update_option('nothingFound',       sanitize_text_field($_POST['nothing-found-message']));
        update_option('tableHeaderBg',      sanitize_text_field($_POST['table-header-bg']));

        // Add an admin notice on successful save
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
        });
    }

    // Retrieve current settings
    $columnQuery        = get_option('columnQuery',     'setting1');
    $colHeader1         = get_option('colHeader1',      'نام آزمایشگاه');
    $colHeader2         = get_option('colHeader2',      'شماره پاکت');
    $colHeader3         = get_option('colHeader3',      'عیار');
    $colHeader4         = get_option('colHeader4',      'شماره آزمایشگاه');
    $colHeader5         = get_option('colHeader5',      'تلفن گویا');
    $colHeader6         = get_option('colHeader6',      'تلفن گویا');
    $frontFormHeader    = get_option('frontFormHeader', 'جستجو بر اساس شماره پاکت');
    $frontFormBtn       = get_option('frontFormBtn',    'جستجو');
    $nothingFound       = get_option('nothingFound',    'داده ای یافت نشد...');
    $tableHeaderBg      = get_option('tableHeaderBg',    '#f1eeea');

?>
    <form action="" method="post">
        <h2>تغییر نام تیتر ستون ها</h2>
        <table class="form-table">
            <tr>
                <th><label for="column-query">جستجو بر اساس</label></th>
                <td>
                    <select name="column-query" id="">
                        <option value="setting1" <?php echo $columnQuery === 'setting1' ? 'selected' : '' ?>>ستون اول</option>
                        <option value="setting2" <?php echo $columnQuery === 'setting2' ? 'selected' : '' ?>>ستون دوم</option>
                        <option value="setting3" <?php echo $columnQuery === 'setting3' ? 'selected' : '' ?>>ستون سوم</option>
                        <option value="setting4" <?php echo $columnQuery === 'setting4' ? 'selected' : '' ?>>ستون چهارم</option>
                        <option value="setting5" <?php echo $columnQuery === 'setting5' ? 'selected' : '' ?>>ستون پنجم</option>
                        <option value="setting6" <?php echo $columnQuery === 'setting6' ? 'selected' : '' ?>>ستون پنجم</option>
                    </select>
                </td>
                <h3>در تنظیم این فیلد ها دقت کنید تا بعدا به مشکل بر نخورید.</h3>
            </tr>

            <tr>
                <th><label for="setting1">ستون اول</label></th>
                <td><input type="text" name="setting1" value="<?php echo $colHeader1 ?>"></td>
            </tr>
            <tr>
                <th><label for="setting2">ستون دوم</label></th>
                <td><input type="text" name="setting2" value="<?php echo $colHeader2; ?>"></td>
            </tr>
            <tr>
                <th><label for="setting3">ستون سوم</label></th>
                <td><input type="text" name="setting3" value="<?php echo $colHeader3; ?>"></td>
            </tr>
            <tr>
                <th><label for="setting4">ستون چهارم</label></th>
                <td><input type="text" name="setting4" value="<?php echo $colHeader4; ?>"></td>
            </tr>
            <tr>
                <th><label for="setting5">ستون پنجم</label></th>
                <td><input type="text" name="setting5" value="<?php echo $colHeader5; ?>"></td>
            </tr>
            <tr>
                <th><label for="setting6">ستون ششم</label></th>
                <td><input type="text" name="setting6" value="<?php echo $colHeader6; ?>"></td>
            </tr>
        </table>

        <br>
        <h2>تنظیمات فرم جستجو ریگیری</h2>
        <table class="form-table">
            <tr>
                <th><label for="front-form-header">تیتر فرم</label></th>
                <td><input type="text" name="front-form-header" value="<?php echo $frontFormHeader ?>"></td>
            </tr>
            <tr>
                <th><label for="nothing-found-message">متن پیام در صورت عدم یافتن</label></th>
                <td><input type="text" name="nothing-found-message" value="<?php echo $nothingFound ?>"></td>
            </tr>
            <tr>
                <th><label for="search-button">تیتر دکمه جستجو</label></th>
                <td><input type="text" name="search-button" value="<?php echo $frontFormBtn ?>"></td>
            </tr>
            <tr>
                <th><label for="table-header-bg">پس زمینه هدر جدول</label></th>
                <td><input type="color" name="table-header-bg" value="<?php echo $tableHeaderBg ?>"></td>
            </tr>
        </table>
        <input type="submit" value="ذخیره تنظیمات" name="save" class="button-primary">

    </form>


<?php


}
