<?php
/**
 * Plugin Name: LM_tarteaucitron
 * Description: Ajoute le script tarteaucitron.js pour la gestion des cookies.
 * Version: 1.0
 * Author: Evans Parfait
 */

/**
 * Ajoute le script tarteaucitron.js au chargement du site.
 */
function ajouter_tarteaucitron_scripts()
{
    wp_enqueue_script(
        'tarteaucitron',
        plugin_dir_url(__FILE__) . 'tarteaucitron/tarteaucitron.min.js',
        [],
        null,
        true
    );

    // Récupération des options du plugin
    $options = get_option('tarteaucitron_settings', []);

    // Définition des paramètres avec des valeurs par défaut
    $hashtag = $options['hashtag'] ?? '#tarteaucitron';
    $highPrivacy = $options['highPrivacy'] ?? 'true';
    $AcceptAllCta = $options['AcceptAllCta'] ?? 'true';
    $orientation = $options['orientation'] ?? 'middle';
    $adblocker = $options['adblocker'] ?? 'false';
    $showAlertSmall = $options['showAlertSmall'] ?? 'false';
    $showIcon = $options['showIcon'] ?? 'false';

    // Ajout du script d'initialisation de tarteaucitron.js
    wp_add_inline_script(
        'tarteaucitron',
        "tarteaucitron.init({
            'privacyUrl': '',
            'bodyPosition': 'top',
            'hashtag': " . json_encode($hashtag) . ",
            'cookieName': 'tarteaucitron',
            'orientation': " . json_encode($orientation) . ",
            'groupServices': true,
            'showDetailsOnClick': true,
            'serviceDefaultState': 'wait',
            'showAlertSmall': " . json_encode($showAlertSmall === 'true') . ",
            'cookieslist': false,
            'closePopup': true,
            'showIcon': " . json_encode($showIcon === 'true') . ",
            'iconPosition': 'BottomRight',
            'adblocker': " . json_encode($adblocker === 'true') . ",
            'DenyAllCta': true,
            'AcceptAllCta': " . json_encode($AcceptAllCta === 'true') . ",
            'highPrivacy': " . json_encode($highPrivacy === 'true') . ",
            'alwaysNeedConsent': false,
            'handleBrowserDNTRequest': false,
            'removeCredit': false,
            'moreInfoLink': true,
            'useExternalCss': false,
            'useExternalJs': false,
            'readmoreLink': '',
            'mandatory': true,
            'mandatoryCta': false,
            'googleConsentMode': true,
            'partnersList': true
        });"
    );
}
add_action('wp_enqueue_scripts', 'ajouter_tarteaucitron_scripts');

/**
 * Ajoute les éléments spécifiques des services (M6 Météo, LinkedIn, Discord, etc.)
 */
function m6_meteo() {
    echo '<div class="tac_m6meteo" data-id="id" width="width" height="height"></div>';
}
add_action('wp_footer', 'm6_meteo');

function linkedin() {
    echo '<span class="tacLinkedin"></span><script type="IN/Share" data-counter="right"></script>';
}
add_action('wp_footer', 'linkedin');

function discord() {
    echo '<div class="discord_widget" width="width" height="height" guildID="guildID"></div>';
}
add_action('wp_footer', 'discord');

function twitter() {
    echo '<span class="tacTwitter"></span><a href="https://twitter.com/share" class="twitter-share-button" data-via="twitter_username" data-count="vertical" data-dnt="true"></a>';
}
add_action('wp_footer', 'twitter');

function pinterest() {
    echo '<span class="tacPinterest"></span><a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark" data-pin-color="white"></a>';
}
add_action('wp_footer', 'pinterest');

/**
 * Ajoute une page de menu dans l'administration WordPress pour gérer les paramètres du plugin.
 */
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

/**
 * Génère la page d'administration du plugin.
 */
function tarteaucitron_admin_page() {
    echo '<div class="wrap">
        <h1>Configuration de Tarteaucitron.js</h1>
        <form method="post" action="options.php">';
            settings_fields('tarteaucitron_options');
            do_settings_sections('tarteaucitron-settings');
            submit_button();
    echo '</form></div>';
}

/**
 * Enregistre les paramètres du plugin dans WordPress.
 */
function tarteaucitron_register_settings() {
    register_setting('tarteaucitron_options', 'tarteaucitron_settings');
    
    add_settings_section('tarteaucitron_main_section', 'Paramètres principaux', null, 'tarteaucitron-settings');
    
    add_settings_field('hashtag', 'Hashtag', 'tarteaucitron_hashtag_callback', 'tarteaucitron-settings', 'tarteaucitron_main_section');
    add_settings_field('highPrivacy', 'High Privacy', 'tarteaucitron_highPrivacy_callback', 'tarteaucitron-settings', 'tarteaucitron_main_section');
    add_settings_field('AcceptAllCta', 'Accepter tout CTA', 'tarteaucitron_acceptAllCta_callback', 'tarteaucitron-settings', 'tarteaucitron_main_section');
    add_settings_field('orientation', 'Orientation', 'tarteaucitron_orientation_callback', 'tarteaucitron-settings', 'tarteaucitron_main_section');
    add_settings_field('adblocker', 'Adblocker', 'tarteaucitron_adblocker_callback', 'tarteaucitron-settings', 'tarteaucitron_main_section');
    add_settings_field('showAlertSmall', 'Afficher une alerte réduite', 'tarteaucitron_showAlertSmall_callback', 'tarteaucitron-settings', 'tarteaucitron_main_section');
    add_settings_field('showIcon', 'Afficher l\'icône', 'tarteaucitron_showIcon_callback', 'tarteaucitron-settings', 'tarteaucitron_main_section');
};
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

function tarteaucitron_orientation_callback()
{
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

function tarteaucitron_adBlocker_callback()
{
    $options = get_option('tarteaucitron_settings');
    echo '<select name="tarteaucitron_settings[adblocker]">
            <option value="true" ' . selected($options['adblocker'] ?? 'true', 'true', false) . '>True</option>
            <option value="false" ' . selected($options['adblocker'] ?? 'true', 'false', false) . '>False</option>
          </select>
          <span class="description" style="margin-left:10px;">Display a message if an adblocker is detected ?</span>';
}

function tarteaucitron_showAlertSmall_callback()
{
    $options = get_option('tarteaucitron_settings');
    echo '<select name="tarteaucitron_settings[showAlertSmall]">
            <option value="true" ' . selected($options['showAlertSmall'] ?? 'true', 'true', false) . '>True</option>
            <option value="false" ' . selected($options['showAlertSmall'] ?? 'true', 'false', false) . '>False</option>
          </select>
          <span class="description" style="margin-left:10px;">Show the small banner on bottom/top right ?</span>';
}

function tarteaucitron_showIcon_callback()
{
    $options = get_option('tarteaucitron_settings');
    echo '<select name="tarteaucitron_settings[showIcon]">
            <option value="true" ' . selected($options['showIcon'] ?? 'true', 'true', false) . '>True</option>
            <option value="false" ' . selected($options['showIcon'] ?? 'true', 'false', false) . '>False</option>
          </select>
          <span class="description" style="margin-left:10px;">Show Icon ?</span>';
}
