<?php

/**
 * Plugin Name: LM_tarteaucitron
 * Description: Ajoute le script tarteaucitron.js à l'ouverture du site.
 * Version: 1.0
 * Author: Evans Parfait
 */



 function ajouter_tarteaucitron_scripts() {
    wp_enqueue_script(
        'tarteaucitron',
        plugin_dir_url(__FILE__) . 'tarteaucitron/tarteaucitron.min.js',
        [],
        null,
        true
    );

    $options = get_option('tarteaucitron_settings', []);

    $hashtag = $options['hashtag'] ?? '#tarteaucitron';
    $highPrivacy = $options['highPrivacy'] ?? 'true';
    $AcceptAllCta = $options['AcceptAllCta'] ?? 'true';
    $orientation = $options['orientation'] ?? 'middle';

    wp_add_inline_script(
        'tarteaucitron',
        "
        tarteaucitron.init({
            'privacyUrl': '',
            'bodyPosition': 'top',
            'hashtag': '$hashtag',
            'highPrivacy': $highPrivacy,
            'AcceptAllCta': $AcceptAllCta,
            'orientation': $orientation,
            'showIcon': true,
            'iconPosition': 'BottomRight',
        });
        "
    );
}
add_action('wp_enqueue_scripts', 'ajouter_tarteaucitron_scripts');


function m6_meteo()
{
    echo '<div class="tac_m6meteo" data-id="id" width="width" height="height"></div>';
}
add_action('wp_footer', 'm6_meteo');

function instagram()
{
    echo '<span class="tacLinkedin"></span><script type="IN/Share" data-counter="top"></script>';
}
add_action('wp_footer', 'instagram');



function tarteaucitron_menu_page()
{
    add_menu_page(
        'Tarteaucitron.js',
        'Tarteaucitron.js',
        'manage_options',
        'tarteaucitron-settings',
        'tarteaucitron_admin_page',
        'dashicons-admin-generic',
        80
    );
}
add_action('admin_menu', 'tarteaucitron_menu_page');

function myStyle() {
    ?>
    <style>
        
    </style>
    <?php 
}

add_action('wp_head', 'myStyle');

function tarteaucitron_admin_page()
{
?>
    <div class="wrap">
        <h1>Configuration de Tarteaucitron.js</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('tarteaucitron_options');
            do_settings_sections('tarteaucitron-settings');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

function tarteaucitron_register_settings()
{
    register_setting('tarteaucitron_options', 'tarteaucitron_settings');

    add_settings_section(
        'tarteaucitron_main_section',
        'Paramètres principaux',
        null,
        'tarteaucitron-settings'
    );

    add_settings_field(
        'hashtag',
        'Hashtag',
        'tarteaucitron_hashtag_callback',
        'tarteaucitron-settings',
        'tarteaucitron_main_section'
    );

    add_settings_field(
        'highPrivacy',
        'High Privacy',
        'tarteaucitron_highPrivacy_callback',
        'tarteaucitron-settings',
        'tarteaucitron_main_section'
    );

    add_settings_field(
        'AcceptAllCta',
        'Accepter tout CTA',
        'tarteaucitron_acceptAllCta_callback',
        'tarteaucitron-settings',
        'tarteaucitron_main_section'
    );

    add_settings_field(
        'orientation',
        'Banner Position',
        'tarteaucitron_orientation_callback',
        'tarteaucitron-settings',
        'tarteaucitron_main_section'
    );
}
add_action('admin_init', 'tarteaucitron_register_settings');

function tarteaucitron_hashtag_callback()
{
    $options = get_option('tarteaucitron_settings');
    echo '<input type="text" name="tarteaucitron_settings[hashtag]" value="' . esc_attr($options['hashtag'] ?? '#tarteaucitron') . '" />
          <span class="description" style="margin-left:10px;">Automatically open the panel with the hashtag.</span>';
}

function tarteaucitron_highPrivacy_callback()
{
    $options = get_option('tarteaucitron_settings');
    echo '<select name="tarteaucitron_settings[highPrivacy]">
            <option value="true" ' . selected($options['highPrivacy'] ?? 'true', 'true', false) . '>True</option>
            <option value="false" ' . selected($options['highPrivacy'] ?? 'true', 'false', false) . '>False</option>
          </select>
          <span class="description" style="margin-left:10px;">Disablig the auto consent feature on navigation ?</span>';
}

function tarteaucitron_acceptAllCta_callback()
{
    $options = get_option('tarteaucitron_settings');
    echo '<select name="tarteaucitron_settings[AcceptAllCta]">
            <option value="true" ' . selected($options['AcceptAllCta'] ?? 'true', 'true', false) . '>True</option>
            <option value="false" ' . selected($options['AcceptAllCta'] ?? 'true', 'false', false) . '>False</option>
          </select>
          <span class="description" style="margin-left:10px;">Show the accept all button when highPrivacy on ?</span>';
}

function tarteaucitron_orientation_callback() {
    $options = get_option('tarteaucitron_settings');
    $orientation = $options['orientation'] ?? 'middle';

    echo '<select name="tarteaucitron_settings[orientation]">
            <option value="top" ' . selected($orientation, 'top', false) . '>Top</option>
            <option value="bottom" ' . selected($orientation, 'bottom', false) . '>Bottom</option>
            <option value="popup" ' . selected($orientation, 'popup', false) . '>Popup</option>
            <option value="banner" ' . selected($orientation, 'banner', false) . '>Banner</option>
            <option value="middle" ' . selected($orientation, 'middle', false) . '>Middle</option>
          </select>
          <span class="description" style="margin-left:10px;">The big banner should be on \'top\' or \'bottom\' ?</span>';
}

