<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

?>

<div class="prayer-times-widget">
    <!-- Date Section -->
    <div class="prayer-times-dates">
        <p><strong><?php echo do_shortcode('[prayer_date]'); ?></strong></p>
    </div>

    <!-- Countdown & Next Prayer -->
    <div class="prayer-times-next">
        <p id="prayer-next">Loading next prayer...</p>
        <p id="prayer-countdown">Loading countdown...</p>
    </div>

    <!-- Prayer Times List -->
    <div class="prayer-times-table">
        <div class="prayer-grid">
            <?php 
                // Define the prayers with corresponding shortcode attributes.
                $prayers = array(
                    'Fajr'     => array('fajr_start', 'fajr_iqamah'),
                    'Sunrise'  => array('sunrise'),
                    'Zuhr'     => array('zuhr_start', 'zuhr_iqamah'),
                    'Asr'      => array('asr_start', 'asr_iqamah'),
                    'Maghrib'  => array('maghrib_start', 'maghrib_iqamah'),
                    'Isha'     => array('isha_start', 'isha_iqamah'),
                );
                $labels = array(
                    'start'  => 'Begins',
                    'iqamah' => 'Iqamah'
                );
                
                foreach ($prayers as $prayerName => $fields) :
            ?>

                <div class="prayer-cell" id="<?php echo esc_attr($prayerName); ?>-cell">
                    <span class="prayer-name"><?php echo esc_html($prayerName); ?></span>
                    <span class="prayer-time">
                        <?php echo esc_html($labels['start']); ?><br>
                        <?php echo do_shortcode('[prayer_time prayer="' . $fields[0] . '"]'); ?>
                    </span>
                    <?php if (count($fields) > 1) : ?>
                        <span class="prayer-iqamah">
                            <?php echo esc_html($labels['iqamah']); ?><br>
                            <?php echo do_shortcode('[prayer_time prayer="' . $fields[1] . '"]'); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php

// // Load JavaScript for the countdown and CSS
// function enqueue_prayer_assets() {
//     if ( ! is_admin() ) { // Load only on frontend

//         // Enqueue JavaScript
//         wp_enqueue_script('ptm-prayer-times', plugin_dir_url(__FILE__) . 'assets/js/prayer-timer.js', array(), '1.0', true);

//         // Get prayer data from DB
//         global $wpdb;
//         $table = $wpdb->prefix . 'prayer_times';
//         $today = date('Y-m-d');
//         $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE prayer_date = %s", $today));
    
//         if ($row) { 
//             $iqamah = [
//                 'fajr'    => $row->fajr_iqamah,
//                 'zuhr'    => $row->zuhr_iqamah,
//                 'asr'     => $row->asr_iqamah,
//                 'maghrib' => $row->maghrib_iqamah,
//                 'isha'    => $row->isha_iqamah,
//             ];

//             $timestamps = [
//                 'timestamp-fajr'    => strtotime($row->fajr_iqamah),
//                 'timestamp-zuhr'    => strtotime($row->zuhr_iqamah),
//                 'timestamp-asr'     => strtotime($row->asr_iqamah),
//                 'timestamp-maghrib' => strtotime($row->maghrib_iqamah),
//                 'timestamp-isha'    => strtotime($row->isha_iqamah),
//             ];
    
//             $data = [
//                 'iqamah_times' => $iqamah,
//                 'timestamps'   => $timestamps
//             ];
//         } else {
//             $data = [];
//         }
    
//         wp_localize_script('ptm-prayer-times', 'ptmData', $data);

//         // Enqueue CSS
//         wp_enqueue_style(
//             'prayer-times-style', 
//             plugin_dir_url(__FILE__) . 'assets/css/styles.css', 
//             array(), 
//             '1.0.0'
//         );
//     }
// }
// add_action('wp_enqueue_scripts', 'enqueue_prayer_assets');

