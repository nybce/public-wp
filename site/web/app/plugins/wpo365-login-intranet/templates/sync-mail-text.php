<?php defined( 'ABSPATH' ) or die(); ?>

<?php if ( empty( $summary[ 'info' ] ) ) : ?>

<?php $body = "
Hi there\r\n
WPO365 | User Synchronization SUCCEEDED on your site [%s] for the job with name %s.\r\n
\r\n
----- SUMMARY -----\r\n
ALL: %s\r\n
CREATED: %s\r\n
DELETED: %s\r\n
DEACTIVATED:  %s\r\n
UPDATED: %s\r\n
ERROR: %s\r\n
LOGGED: %s\r\n
SKIPPED: %s\r\n
----- END -----\r\n
\r\n
WPO365 - Connecting WordPress and Microsoft Office 365 / Azure AD\r\n
Zurich, Switzerland\r\n
\r\n
t https://twitter.com/WPO365\r\n
w https://www.wpo365.com\r\n
e support@wpo365.com\r\n
"; ?>

<?php else : ?>

<?php $body = "
Hi there\r\n
\r\n
WPO365 | User Synchronization FAILED on your site [%s] for the job with ID %s.\r\n
\r\n
----- ERROR -----\r\n
\r\n
%s\r\n
\r\n
----- END -----\r\n
\r\n
WPO365 - Connecting WordPress and Microsoft Office 365 / Azure AD\r\n
Zurich, Switzerland\r\n
\r\n
t https://twitter.com/WPO365\r\n
w https://www.wpo365.com\r\n
e support@wpo365.com\r\n
"; ?>

<?php endif ?>