<?php
/**
 * Plugin Name: Coparentes Microsoft Clarity
 * Description: Ładuje Microsoft Clarity po zgodzie na cookies analityczne (zgodnie z banerem cookies motywu).
 * Version: 1.0.0
 * Author: Coparentes
 */

if (!defined('ABSPATH')) {
    exit;
}

const COPARENTES_CLARITY_OPTION = 'coparentes_clarity_project_id';

add_action('admin_init', function () {
    register_setting(
        'coparentes_clarity',
        COPARENTES_CLARITY_OPTION,
        [
            'type' => 'string',
            'sanitize_callback' => static function ($value) {
                return preg_replace('/[^a-zA-Z0-9]/', '', (string) $value);
            },
            'default' => '',
        ]
    );
});

add_action('admin_menu', function () {
    add_options_page(
        'Microsoft Clarity',
        'Microsoft Clarity',
        'manage_options',
        'coparentes-clarity',
        'coparentes_clarity_render_settings_page'
    );
});

/**
 * @return void
 */
function coparentes_clarity_render_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Microsoft Clarity</h1>
        <p>Wklej <strong>Project ID</strong> z panelu <a href="https://clarity.microsoft.com/" target="_blank" rel="noopener noreferrer">clarity.microsoft.com</a>.</p>
        <p>Skrypt uruchomi się dopiero po zgodzie użytkownika na cookies analityczne.</p>
        <form method="post" action="options.php">
            <?php settings_fields('coparentes_clarity'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="<?php echo esc_attr(COPARENTES_CLARITY_OPTION); ?>">Project ID</label></th>
                    <td>
                        <input
                            type="text"
                            class="regular-text"
                            id="<?php echo esc_attr(COPARENTES_CLARITY_OPTION); ?>"
                            name="<?php echo esc_attr(COPARENTES_CLARITY_OPTION); ?>"
                            value="<?php echo esc_attr(get_option(COPARENTES_CLARITY_OPTION, '')); ?>"
                            autocomplete="off"
                            spellcheck="false"
                        />
                    </td>
                </tr>
            </table>
            <?php submit_button('Zapisz'); ?>
        </form>
    </div>
    <?php
}

add_action('wp_head', 'coparentes_clarity_print_script', 5);

/**
 * @return void
 */
function coparentes_clarity_print_script()
{
    if (is_admin()) {
        return;
    }

    $project_id = get_option(COPARENTES_CLARITY_OPTION, '');
    if ($project_id === '') {
        return;
    }
    ?>
    <script type="text/plain" data-cookie-category="analytics" data-no-optimize="1">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", <?php echo wp_json_encode($project_id); ?>);
    </script>
    <?php
}
