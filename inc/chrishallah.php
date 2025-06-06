<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * The visual element of the shortcode / widget
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
                // Determine if today is Friday (WP weekday: Mon=1 ... Sun=7)
                $wp_ts      = current_time( 'timestamp' );
                $weekday    = date( 'N', $wp_ts );  // Friday == 5
                $is_friday  = ( $weekday == 5 );

                // Base set of prayers
                if ( $is_friday ) {
                    // On Friday, use Jumu'ah instead of Zuhr
                    $prayers = array(
                        'Fajr'        => array( 'fajr_start',        'fajr_iqamah'    ),
                        'Sunrise'     => array( 'sunrise'                            ),
                        'Jumu\'ah' => array( 'jumuah_first',  'jumuah_second'  ),
                        'Asr'         => array( 'asr_start',         'asr_iqamah'     ),
                        'Maghrib'     => array( 'maghrib_start',     'maghrib_iqamah' ),
                        'Isha'        => array( 'isha_start',        'isha_iqamah'    ),
                    );

                    // (Optional) Override icons for Jumu‘ah
                    $prayerIcons = array(
                        'Fajr'           => 'fas fa-mountain-sun',
                        'Sunrise'        => '',
                        'Jumu\'ah'   => 'fas fa-mosque',
                        'Asr'            => 'fas fa-cloud-sun',
                        'Maghrib'        => 'fas fa-cloud-moon',
                        'Isha'           => 'fas fa-moon',
                    );
                } else {
                    // Normal days
                    $prayers = array(
                        'Fajr'     => array( 'fajr_start', 'fajr_iqamah'    ),
                        'Sunrise'  => array( 'sunrise'                          ),
                        'Zuhr'     => array( 'zuhr_start', 'zuhr_iqamah'    ),
                        'Asr'      => array( 'asr_start',  'asr_iqamah'     ),
                        'Maghrib'  => array( 'maghrib_start','maghrib_iqamah'),
                        'Isha'     => array( 'isha_start', 'isha_iqamah'    ),
                    );

                    $prayerIcons = array(
                        'Fajr'     => 'fas fa-mountain-sun',
                        'Sunrise'  => '',
                        'Zuhr'     => 'fas fa-sun',
                        'Asr'      => 'fas fa-cloud-sun',
                        'Maghrib'  => 'fas fa-cloud-moon',
                        'Isha'     => 'fas fa-moon',
                    );
                }

                $labels = array(
                    'start'  => 'Begins',
                    'iqamah' => 'Iqamah',
                    'jumuah_first' => 'First',
                    'jumuah_second' => 'Second',
                );

                // Render each prayer cell
                foreach ($prayers as $prayerName => $fields): ?>
                    <div class="prayer-cell" id="<?php echo esc_attr( $prayerName ); ?>-cell">
                      <!-- Icon and name here... -->
                        <?php if (!empty($prayerIcons[$prayerName])): ?>
                            <i class="<?php echo esc_attr($prayerIcons[$prayerName]); ?> prayer-icon"></i>
                        <?php endif; ?>
                        <span class="prayer-name"><?php echo esc_html( $prayerName ); ?></span>
                      <div class="prayer-times-container">
                        <?php if ($is_friday && $prayerName == "Jumu'ah"): ?>
                          <!-- Jumu‘ah: use custom labels -->
                          <div class="prayer-jumuah-first">
                            <?php echo esc_html( $labels['jumuah_first'] ); ?><br>
                            <?php echo do_shortcode('[prayer_time prayer="' . $fields[0] . '"]'); ?>
                          </div>
                          <?php if (isset($fields[1])): ?>
                            <div class="prayer-jumuah-second">
                              <?php echo esc_html( $labels['jumuah_second'] ); ?><br>
                              <?php echo do_shortcode('[prayer_time prayer="' . $fields[1] . '"]'); ?>
                            </div>
                          <?php endif; ?>
                        <?php else: ?>
                          <!-- Standard labels for other prayers -->
                          <div class="prayer-time">
                            <?php echo esc_html( $labels['start'] ); ?><br>
                            <?php echo do_shortcode('[prayer_time prayer="' . $fields[0] . '"]'); ?>
                          </div>
                          <?php if (isset($fields[1])): ?>
                            <div class="prayer-iqamah">
                              <?php echo esc_html( $labels['iqamah'] ); ?><br>
                              <?php echo do_shortcode('[prayer_time prayer="' . $fields[1] . '"]'); ?>
                            </div>
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
        </div>
    </div>
</div>