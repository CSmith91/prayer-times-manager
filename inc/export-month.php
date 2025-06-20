<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// SHORTCODE FOR THE MONTHLY TABLE (FRONTEND)
function ptm_prayer_times_shortcode($atts) {
    ob_start();

    global $wpdb;
    $table_name = $wpdb->prefix . 'prayer_times';

    // Handle selected month/year via GET (default to current)
    $selected_month = isset($_GET['ptm_month']) ? intval($_GET['ptm_month']) : date('n');
    $selected_year = isset($_GET['ptm_year']) ? intval($_GET['ptm_year']) : date('Y');

    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE MONTH(prayer_date) = %d AND YEAR(prayer_date) = %d ORDER BY prayer_date ASC",
        $selected_month, $selected_year
    ));

    $months = array(
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    );
    ?>
    <div id="ptm-container">
        <div id="form-container">
            <form method="get" action="">
                <div class="input-container">
                    <label for="ptm_month">Month:</label>
                    <select name="ptm_month" id="ptm_month">
                        <?php foreach ($months as $num => $name): ?>
                            <option value="<?php echo $num; ?>" <?php selected($selected_month, $num); ?>><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-container">
                    <label for="ptm_year">Year:</label>
                    <select name="ptm_year" id="ptm_year">
                        <?php for ($y = date('Y') - 1; $y <= date('Y') + 3; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php selected($selected_year, $y); ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="submit-container">
                    <input type="submit" value="View">
                </div>
            </form>
        </div>

        <?php if ($rows): ?>
        <div id="download-container">
            <button id="download-btn" onclick="ptmDownloadPDF()">Download as PDF</button>
        </div>
        
        <table id="ptm-table" border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Fajr Begins</th>
                    <th>Fajr Iqamah</th>
                    <th>Sunrise</th>
                    <th>Zuhr Begins / First Jumu'ah</th>
                    <th>Zuhr Iqamah / Second Jumu'ah</th>
                    <th>Asr Begins</th>
                    <th>Asr Iqamah</th>
                    <th>Maghrib Begins</th>
                    <th>Maghrib Iqamah</th>
                    <th>Isha Begins</th>
                    <th>Isha Iqamah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $today = date('Y-m-d'); // Get today's date
                foreach ($rows as $row):
                    $row_date = date('Y-m-d', strtotime($row->prayer_date));
                    $day_of_week = date('l', strtotime($row->prayer_date));
                    $is_friday = ($day_of_week === 'Friday') ? 'friday' : '';
                    $is_today = ($row_date === $today) ? 'today' : '';
                    $row_class = trim("$is_friday $is_today");
                ?>
                    <tr class="<?php echo esc_attr($row_class); ?>">
                        <td><?php echo esc_html(date('j F Y', strtotime($row->prayer_date))); ?></td>
                        <td><?php echo esc_html(substr($row->fajr_begins, 0, 5)); ?></td>
                        <td><?php echo esc_html(substr($row->fajr_iqamah, 0, 5)); ?></td>
                        <td><?php echo esc_html(substr($row->sunrise_begins, 0, 5)); ?></td>
                        <?php if ($is_friday): 
                            $first   = !empty($row->jumuah_first)  ? $row->jumuah_first  : $row->zuhr_begins;
                            $second  = !empty($row->jumuah_second) ? $row->jumuah_second : $row->zuhr_iqamah;
                        ?>
                            <td><?php echo esc_html(substr($first, 0, 5)); ?></td>
                            <td><?php echo esc_html(substr($second, 0, 5)); ?></td>
                        <?php else: ?>
                            <td><?php echo esc_html(substr($row->zuhr_begins, 0, 5)); ?></td>
                            <td><?php echo esc_html(substr($row->zuhr_iqamah, 0, 5)); ?></td>
                        <?php endif; ?>
                        <td><?php echo esc_html(substr($row->asr_begins, 0, 5)); ?></td>
                        <td><?php echo esc_html(substr($row->asr_iqamah, 0, 5)); ?></td>
                        <td><?php echo esc_html(substr($row->maghrib_begins, 0, 5)); ?></td>
                        <td><?php echo esc_html(substr($row->maghrib_iqamah, 0, 5)); ?></td>
                        <td><?php echo esc_html(substr($row->isha_begins, 0, 5)); ?></td>
                        <td><?php echo esc_html(substr($row->isha_iqamah, 0, 5)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>We haven't added prayer times for <?php echo $months[$selected_month] . ' ' . $selected_year; ?> yet. Please check again soon!</p>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('prayer_times_table', 'ptm_prayer_times_shortcode');