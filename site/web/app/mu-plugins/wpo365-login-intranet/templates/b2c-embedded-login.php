<?php

// Prevent public access to this script
defined('ABSPATH') or die();

if (is_user_logged_in()) : ?>

    <button>Sign-out</button>

<?php else : ?>
    <div id="wpo365OpenIdRedirect" />
    <script>
        let redirectTo = '<?php echo esc_html($redirect_to) ?>';
        setTimeout(() => wpo365.pintraRedirect.toMsOnline('', (redirectTo ? redirectTo : location.href), '', '<?php echo esc_html($b2c_policy) ?>', true), <?php echo $wait ?>);
    </script>
<?php endif ?>