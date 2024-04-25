=== WordPress + Microsoft Office 365 / Azure AD | LOGIN ===
Contributors: wpo365
Tags: office 365, O365, Microsoft 365, azure active directory, Azure AD, AAD, authentication, single sign-on, sso, SAML, SAML 2.0, OpenID Connect, OIDC, login, oauth, microsoft, microsoft graph, teams, microsoft teams, sharepoint online, sharepoint, spo, onedrive, SCIM, User synchronization, yammer, powerbi, power bi, mail, smtp, phpmailer, wp_mail, email
Requires at least: 4.8.1
Tested up to: 6.1
Stable tag: 21.5
Requires PHP: 5.6.40
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

With WPO365 | LOGIN users can sign in with their corporate or school (Azure AD / Microsoft Office 365) account to access your WordPress website: No username or password required (OIDC or SAML 2.0 based SSO). Plus you can send email using Microsoft Graph instead of SMTP from your WordPress website.

= SINGLE SIGN-ON (SSO) =

- Supported Identity Providers (IdPs): **Azure Active Directory** and **Azure AD B2C** [more](https://docs.wpo365.com/article/158-select-identity-provider-idp)
- Supported SSO protocols: **OpenID Connect** and **SAML 2.0** [more](https://docs.wpo365.com/article/159-select-sso-protocol)
- Supported OpenID Connect User Flows: Authorization Code User Flow (recommended) and Hybrid User Flow [more](https://docs.wpo365.com/article/156-why-the-authorization-code-user-flow-is-now-recommended)

= NEW USERS =

- New users that sign in with Microsoft automatically become WordPress users [more](https://www.wpo365.com/sso-for-office-365-azure-ad-user/)

= INTRANET =

- Configure the **intranet** authentication mode to restrict access to all front-end posts and pages [more](https://www.wpo365.com/make-your-wordpress-intranet-private/)
- Hide the  **WordPress Admin Bar** for specific roles [more](https://docs.wpo365.com/article/150-hide-wp-admin-bar-for-roles)

= MICROSOFT TEAMS =

- Support for (seamless) integration of your WordPress website into a **Microsoft Teams** Tabs and Apps [more](https://docs.wpo365.com/article/70-adding-a-wordpress-tab-to-microsoft-teams-and-use-single-sign-on)

= MAIL =

- **Send emails using Microsoft Graph** instead of SMTP from your WordPress website [more](https://docs.wpo365.com/article/108-sending-wordpress-emails-using-microsoft-graph)
- Send as **HTML**
- Save to the **Sent Items** folder
- Support for **file attachments**

= WORDPRESS MULTISITE =

- Support for **WordPress Multisite** [more](https://www.wpo365.com/support-for-wordpress-multisite-networks/)

= POWER BI =

- Embed Microsoft **Power BI** content (user owns data) [more](https://www.wpo365.com/power-bi-for-wordpress/)

= SHAREPOINT =

- Embed a **SharePoint Online** library using a [Gutenberg Block](https://docs.wpo365.com/article/128-embedded-sharepoint-library-powerful-gutenberg-blocks-for-wordpress) or as [simple shortcode](https://docs.wpo365.com/article/126-sharepoint-library-for-wordpress)
- Embed a **SharePoint Online** search experience into a front-end post or page using simple to generate shortcode [more](https://www.wpo365.com/content-by-search/)

= EMPLOYEE DIRECTORY =

- Embed an intuitve Azure AD / Microsoft Graph based **Employee Directory** into a front-end post or page [more](https://www.wpo365.com/employee-directory/)

= REST API ENDPOINT PROTECTION =

- Protect your **WordPress REST API** endpoints with a combination of a WordPress cookie and a nonce for delegated access [more](https://docs.wpo365.com/article/151-wordpress-cookies-based-protection-for-the-wordpress-rest-api)

= DEVELOPERS =

- Developers can now connect to a RESTful API for Microsoft Graph in their favorite programming language and without the hassle of authentication and authorization [more](https://docs.wpo365.com/article/129-a-restful-proxy-to-microsoft-graph-inside-wordpress)
- *PHP hooks* for developers to build custom Microsoft Graph / Office 365 integrations [more](https://docs.wpo365.com/article/82-developer-hooks)

https://youtu.be/6ti71O4nh0s

== ADD FUNCTIONALITY WITH PREMIUM EXTENSIONS ==

= PROFILE+ =

- Update a WordPress **user profile** with (first, last, full) name, email and UPN from Azure AD [more](https://www.wpo365.com/downloads/wordpress-office-365-login-plus/)

= SINGLE SIGN-ON =

- Visitors are required to sign in with Azure AD / Microsoft but will not be automatically logged in to WordPress [more](https://docs.wpo365.com/article/36-authentication-scenario)

= AUDIENCES =

- Azure AD group based **access restriction** for individual front-end posts and pages [more](https://docs.wpo365.com/article/139-audiences)

= SYNC =

- On-demand / scheduled **user synchronization** from Azure AD to WordPress [more](https://www.wpo365.com/synchronize-users-between-office-365-and-wordpress/)

= AVATAR =

- Replace the default WordPress / BuddyPress **avatar** with a Microsoft 365 profile picture [more](https://www.wpo365.com/downloads/wpo365-avatar/)

= ROLES + ACCESS =

- WordPress roles assignments / access restrictions based on *Azure AD groups* / user attributes [more](https://www.wpo365.com/downloads/wpo365-roles-access/)

= LOGIN+ =

- Map **Microsoft Graph user resource properties** to custom WordPress / BuddyPress user profile fields [more](https://docs.wpo365.com/article/98-synchronize-microsoft-365-azure-ad-profile-fields)
- Map **custom claims** in an Azure AD B2C ID token to custom WordPress / BuddyPress user profile fields [more](https://docs.wpo365.com/article/130-azure-ad-b2c-based-single-sign-on-for-wordpress)
- Map **custom claims** from SAML 2.0 response to custom WordPress / BuddyPress user profile fields [more](https://docs.wpo365.com/article/130-azure-ad-b2c-based-single-sign-on-for-wordpress)
- Support for so-called **Multi-Tenancy** [more](https://docs.wpo365.com/article/47-support-for-multitenant-apps)
- Require **Proof Key for Code Exchange (PKCE)** for increased protection when requesting oauth tokens from Azure AD [more](https://docs.wpo365.com/article/149-require-proof-key-for-code-exchange-pkce)
- Other features: [Enable SSO for the login page](https://docs.wpo365.com/article/120-enable-sso-for-the-default-custom-login-page), [Dual login](https://docs.wpo365.com/article/81-enable-dual-login) and [Private Pages](https://docs.wpo365.com/article/38-private-pages)

= MAIL =

- Send **large attachments** (> 3 Mb)
- Send from **Microsoft 365 Shared Mailbox**
- **Log every email** sent from your WordPress website, review errors and try to send unsuccessfully sent mails again. [more](https://www.wpo365.com/downloads/wpo365-mail/)
- Allow forms / plugins / themes to dynamically set the **From** address
- Send all emails by default as BCC

= GROUPS =

- Deep integration with the **(itthinx) Groups** plugin for group membership and access control [more](https://www.wpo365.com/downloads/wpo365-groups/)

= MICROSOFT 365 APPS =

- Advanced versions of the apps to embed content of Microsoft 365 services such as **Power BI** (with support for *application owns data* scenarios) and **SharePoint Online** (with support for anonymous users)

= SCIM =

- (SCIM based) **Azure AD User Provisioning** to WordPress [more](https://www.wpo365.com/downloads/wpo365-scim/)

= REST API ENDPOINT PROTECTION =

- Enable **Azure AD** based protection for your **WordPress REST API** endpoints [more](https://docs.wpo365.com/article/147-azure-ad-based-protection-for-the-wordpress-rest-api)

= CONFIGURATION =

- Save multiple configurations
- Directly edit (the JSON representation of) a configuration

== Prerequisites ==

- Make sure that you have disabled caching for your Website in case your website is an intranet and access to WP Admin and all pubished pages and posts requires authentication. With caching enabled, the plugin may not work as expected
- We have tested our plugin with Wordpress >= 4.8.1 and PHP >= 5.6.40
- You need to be (Office 365) Tenant Administrator to configure both Azure Active Directory and the plugin
- You may want to consider restricting access to the otherwise publicly available wp-content directory

== Support ==

We will go to great length trying to support you if the plugin doesn't work as expected. Go to our [Support Page](https://www.wpo365.com/how-to-get-support/) to get in touch with us. We haven't been able to test our plugin in all endless possible Wordpress configurations and versions so we are keen to hear from you and happy to learn!

== Feedback ==

We are keen to hear from you so share your feedback with us on [Twitter](https://twitter.com/WPO365) and help us get better!

== Open Source ==

When you’re a developer and interested in the code you should have a look at our repo over at [WordPress](http://plugins.svn.wordpress.org/wpo365-login/).

== Installation ==

Please refer to [these **Getting started** articles](https://docs.wpo365.com/category/21-getting-started) for detailed installation and configuration instructions.

== Frequently Asked Questions ==

== Screenshots ==
1. Microsoft / Azure AD based Single Sign-on
2. Embedded Power BI for WordPress
3. Embedded SharePoint Online Documents for WordPress
4. Embedded SharePoint Online Search for WordPress
5. Employee Directory
6. Support for Azure AD B2B and Azure AD B2C
7. Sending WordPress email using Microsoft Graph
8. Synchronizing users from Azure AD to WordPress
9. Embed WordPress in a Teams Tab or App
10. Assign WordPress roles / Deny access based on Azure AD groups

== Upgrade Notice ==

* Please check the online version of the [release notes for version 11.0](https://www.wpo365.com/release-notes-v11-0/).

== Changelog ==

= v21.5 =
* Fix: The recently added *ID token verification* did not take the mail-authorization flow into account. [LOGIN]
* Improvement: Administrators can now re-configure the WPO365 | LOGIN plugin to skip the *ID token verification* altogether, on the plugin's *Miscellaneous* configuration page (but this is not recommended for production environments). [LOGIN]

= v21.4 =
* Fix: The built-in update checker for premium extensions might incorrectly indicate that an update for some extensions would be available. [LOGIN]

= v21.3 =
* Fix: The plugin would cause a fatal crash when using PHP 7.2 or lower. [ALL]

= v21.2 =
* Change: The WPO365 | LOGIN plugin will now verify the tenant that issued the ID token and the audience for which the ID token was issued. [LOGIN]
* Fix: Various issues with the built-in license and update checker for premium extensions and bundles.
* Fix: The Employee Directory app now will only take the host portion of the SharePoint home URL when dynamically constructing the permissions scope. [M365 APPS, INTRANET]
* Fix: The User Sync test case will skip the check for custom domains when Azure AD B2C has been selected. [SYNC, INTRANET]

= v21.1 =
* Fix: License check for premium extensions and bundles would show "unknown error occurred" for valid licenses.
* Fix: Update check for premium extensions and bundles now better aligned with the recently updated license management service.

= v21.0 =
* Improvement: Various aspects of user synchronization have been improved / refactored in an attempt to make it easier to configure, track and start / stop jobs. [SYNC, INTRANET]
* Improvement: The WPO365 plugin will now - by default - first try to look up an existing WordPress user by its Azure AD Object ID. This value uniquely identifies a user in Azure AD and is automatically configured when WPO365 creates a new user (or updates an existing one). [ALL]
* Improvement: To support Azure AD B2C user synchronization, newly created user synchronization jobs will now - by default - skip the domain check (whereby the login domain of the username of users retrieved from Microsoft Graph is matched against a list of supported custom domains on the plugin's *User registration* configuration page). Existing user synchronization jobs must be updated manually. [SYNC, INTRANET]
* Improvement: If WPO365 User synchronization has been configured, the default WordPress User list will be enhanced automatically. A column is added to show the date and time a user was last updated by WPO365 User synchronization. A second column will show a button that allows administrators to reactivate a user in case that user has been de-activated / soft-deleted by WPO365 User synchronization. [SYNC, INTRANET]
* Improvement: Support for Azure AD B2C custom login domains. See [online documentation](https://docs.wpo365.com/article/167-use-custom-domain-for-b2c-login) for details. [LOGIN+, SYNC, INTRANET]
* Improvement: Administrators can now configure website buttons targetting a specific Azure AD B2C user flow or custom policy sign-up, sign-in or reset password. See [online documentation](https://docs.wpo365.com/article/168-allow-multiple-b2c-policies) for details. [LOGIN+, SYNC, INTRANET]
* Improvement: It is now possible to configure an embedded login experience for Azure AD B2C. See [online documentation](https://docs.wpo365.com/article/170-azure-ad-b2c-embedded-login) for details. [LOGIN+, SYNC, INTRANET]
* Fix: The *Source for custom user fields* (ID token, Microsoft Graph or SAML response) selector was not always visible on the plugin's *User sync* configuration page. [LOGIN+, CUSTOMER USER FIELDS, SYNC, INTRANET]
* Fix: The *Allow forms to override "From" address* was only enabled for application-level *Mail.Send* permissions. [MAIL, SYNC, INTRANET]
* Fix: Overriding the "From" address was sometimes ignored. [MAIL, SYNC, INTRANET]
* Fix: Sending from a Shared Mailbox was sometimes ignored. [MAIL, SYNC, INTRANET]
* Fix: Version bump for all WPO365 plugins. [ALL]
* Fix: License for premium extensions are now checked regularly and a notification will be shown if the license is expired. [ALL]
* Fix: The "Authorized!" label on the plugin's *Mail* configuration page is now green instead of red to indicate succes

= v20.4 =
* Fix: The mail authorization may falsely indicate that the plugin is not authorized to send emails using Microsoft Graph due to how the plugin compared permissions. [ALL]

= v20.3 =
* Feature: Websites that are using the [Mail Integration for Office 365/Outlook](https://wordpress.org/plugins/mail-integration-365/) are now urged to switch to [WPO365 | MICROSOFT GRAPH MAILER](https://wordpress.org/plugins/wpo365-msgraphmailer/) or configure the builtin Microsoft Graph mail function of the WPO365 | LOGIN plugin. Consult the [online migration guide](https://docs.wpo365.com/article/165-migrate-from-mail-integration-for-office-365-outlook-to-wpo365-microsoft-graph-mailer) for further details. [ALL]
* Improvement: Administrators can check an option to **Use alternative CDN** (on the plugin's Integration page). If checked, the plugin will download the react-js and react-dom.js packages from the CloudFlare CDN (instead of from the default UNPKG CND). However, administrators can also choose to self-host these dependencies. In this case they can override the CDN configuration using a constant that must defined in wp-config.php. See the [online documentation](https://docs.wpo365.com/article/163-use-alternative-cdn-for-javascript-libraries) for details. [ALL]
* Fix: The avatar method updated in v20.0 now also overrides the get_avatar hook to avoid conflicts with other plugins such as Ultimate Member. [AVATAR, SYNC, INTRANET]

= v20.2 =
* Improvement: Administrators can now define a constant in wp-config.php to override the default CDN used to download the react.js and react-dom.js packages. This constant must be defined immediately after the line "/* That's all, stop editing! Happy publishing. */" as an array as follows: define('WPO_CDN', array('react' => 'https://cdnjs.cloudflare.com/ajax/libs/react/16.14.0/umd/react.production.min.js', 'react_dom' => 'https://cdnjs.cloudflare.com/ajax/libs/react-dom/16.14.0/umd/react-dom.production.min.js'));

= v20.1 =
* Fix: The renaming of an option (to allow retrieval of oauth tokens by client side apps) prevented existing configurations to update this value. [ALL]

= v20.0 =
* Feature: The (premium version of the) Microsoft Graph Mailer can now send attachments larger than 3 MB. [MAIL, SYNC, INTRANET]
* Feature: The (premium version of the) Microsoft Graph Mailer can now send emails from a Shared Mailbox. [MAIL, SYNC, INTRANET]
* Improvement: The LOGIN+ extension now also allows administrators to save multiple configurations (on the plugin's Import / Export configuration page). [LOGIN+]
* Improvement: Administrators can now define the name of the WordPress user meta for user attributes synchronized from Azure AD to WordPress. [LOGIN+, CUSTOM USER FIELDS, SYNC, INTRANET]
* Improvement: The Avatar method now replaces the URL of the profile image instead (by filtering the pre_get_avatar_data function instead of the get_avatar function). [AVATAR, SYNC, INTRANET]
* Improvement: Now supports reading custom claims in a SAML response and save them as WordPress user meta. [LOGIN+, CUSTOM USER FIELDS, SYNC, INTRANET]
* Improvement: Administrators can now choose to skip updating a user WordPress user's displayname. [LOGIN+, USER FIELDS, SYNC, INTRANET]
* Improvement: Some parts of the source code have been updated to improve compatibility with PHP 8.1. [ALL]
* Fix: The Audiences feature now also prevents access to posts and pages using a direct-edit link. [ROLES + ACCESS, SYNC, INTRANET]
* Fix: Sign out of Microsoft now also works as expected for Azure AD B2C. [LOGIN+, SYNC, INTRANET]
* Fix: Custom formatting of a WordPress user's displayname now works as expected for SAML 2.0 based Single Sign-on. [LOGIN+, CUSTOM USER FIELDS SYNC, INTRANET]
* Fix: The shortcode properties of a Micrsoft 365 App are now HTML-decoded to handle the case where WordPress updates shortcode properties when an author edits a page. [ALL]
* Fix: The div that encapsulates a Microsoft 365 App can now be referenced by its unique classname "wpo365-app-root". [ALL]
* Fix: Some WPO365 options have been removed / renamed to avoid triggering ModSecurity OWASP CRS causing an 418 "I am not a teapot" HTTP errors, for example when hosting a site at DreamHost. [ALL]
* Fix: The plugin now correctly tries again to get a user's (Azure AD) group memberships with Group.Read.All permissions when the administrator has not (yet) granted permissions to do so using GroupMember.Read.All permissions. [ROLES + ACCESS, SYNC, INTRANET]

= v19.4 =
* Fix: Mail authorization for delegated access would fail with the error "Could not retrieve a tenant and application specific JSON Web Key Set and thus the JWT token cannot be verified successfully". [LOGIN, MICROSOFT GRAPH MAILER]
* Fix: Embedded PowerBI reports will now try to refresh the acquired access token when the browser tab is left open. [LOGIN, INTRANET, M365 APPS]
* Fix: Encoding of parameters for embedded SharePoint Online apps (Search and Documents) have been improved. [LOGIN, INTRANET, M365 APPS]
* Fix: The Audiences custom meta box has been updated and produces valid HTML. [ROLES + ACCESS, SYNC, INTRANET]

= v19.3 =
* Fix: The delegated mail authorization feature would - under circumstances - fail to get the mail specific tenant ID and as a result an attempt to refresh the access token may fail. [LOGIN, MICROSOFT GRAPH MAILER]

= v19.2 =
* Fix: The Redirect URL field for the mail authorization is no longer greyed out and can be changed by administrators. [LOGIN]

= v19.1 =
* Fix: A backward-compatibility issue with Audiences would cause a critical error when editing a page. Administrators with any of the following extensions installed must update as soon as possible: ROLES + ACCESS, SYNC, INTRANET.

= v19.0 =
* Change: Sending WordPress emails using Microsoft Graph can now also be configured with **delegated** permissions. Administrators are urged to review the [documentation](https://docs.wpo365.com/article/108-sending-wordpress-emails-using-microsoft-graph) and to update their configuration. [LOGIN, MICROSOFT GRAPH MAILER]
* Feature: **Audiences** - used to target posts and pages to specific Azure AD groups - can now also be used on a post or page using a custom metabox in the sidebar. Consult the [updated documentation](https://docs.wpo365.com/article/139-audiences) for details. [ROLES + ACCESS, SYNC, INTRANET]
* Feature: Azure Active Directory secrets can now be stored in the website's **WP-Config.php** and removed from the database. [MAIL]
* Improvement: A number of **plugin self-tests** have been improved to help administrators find loopholes in the configuration e.g. of User synchronization and the integration of various SharePoint Online services. [LOGIN]
* Fix: The plugin no longer "hijacks" a state parameter when sent in the header of any request. This prevented - amongst other things - enabling / disabling of WordPress auto-updates. [LOGIN]
* Fix: The **Employee Directory** app now shows profile information when users are searched for using SharePoint. [M365 APPS, INTRANET]
* Fix: Version bump for all WPO365 plugins.

= v18.2 =
* Fix: Recent changes to the built-in notification service could cause a fatal error for older PHP versions that has now been fixed. [LOGIN]

= v18.1 =
* Fix: If the plugin is configured to send WordPress emails using Microsoft Graph then it will now always replace the "from" email address if WordPress tries to sent emails from "wordpress@[sitename]". WordPress will propose this email address is no email is set by the plugin sending the email (e.g. Contact Form 7). This email may pass checks as a valid email address but in reality this email address most likely does not exist. The option to fix the "localhost" issue has been removed since this fix improves the behavior for all hosts (incl. localhost). [ALL]
* Improvement: Various wp-admin banners as well as some translations have been updated. Also a teaching bubble is shown on the Single Sign-on page to help admins quickly find the WPO365 documentation center at [https://docs.wpo365.com/](https://docs.wpo365.com/). [ALL]

= v18.0 =
* Change: Administrators who selected **OpenID Connect** based single sign-on, can now choose between the **Hybrid Flow** and the **Authorization Code Flow**. New installations will automatically be configured using Authorization Code Flow. [Read more](https://docs.wpo365.com/article/153-hybrid-flow-versus-authorization-code-flow) [LOGIN]
* Change: Support for **Azure AD B2C** custom policies (sign-up, sign-in and password reset) is no longer a premium feature. [LOGIN]
* Change: All features of **WPO365 | CUSTOM USER FIELDS** extension are from now on supported by the **WPO365 | LOGIN+ extension**. See [our website](https://www.wpo365.com/downloads/wordpress-office-365-login-professional/) for details and pricing. [CUSTOM USER FIELDS, LOGIN+]
* Change: A new **WPO365 Features Dashboard** has been added that allows administrators to toggle features such as e.g. SSO, MAIL and SYNC on or off. [LOGIN]
* Feature: Admins can now choose to hide the **WordPress Admin Bar** for specific roles. [LOGIN]
* Feature: Requesting access tokens from Azure AD can now be further secured using a **Proof Key for Code Exchange (PKCE)**. [LOGIN+, SYNC, INTRANET]
* Feature: Protect and secure your **WordPress REST API** with Azure AD generated oauth access tokens (PREMIUM). [LOGIN+, SYNC, INTRANET]
* Feature: Protect and secure your **WordPress REST API** with WordPress REST cookies. [LOGIN]
* Improvement: **Azure AD B2C** custom claims sent in the ID token can now be mapped to custom WordPress user meta fields. [LOGIN+, SYNC, INTRANET]
* Improvement: When specified in - for example - an email form the "From" address will be used to send the email from (instead of the configured "From" address and if the address specified in the form appears to be valid). This behavior is a premium feature and not enabled by default. [MAIL, SYNC, INTRANET]
* Improvement: Admins can now set a different Azure AD tenant for sending WordPress emails using Microsoft Graph when the plugin is configured for **Azure AD B2C based** single sign-on. [ALL]
* Improvement: Admins can now update the priority for the get_avatar hook on the plugin's *User sync* page (default 1). [AVATAR, SYNC, INTRANET]
* Improvement: The plugin is now able to work with the more appropriate **GroupMember.Read.All** permissions instead of *Group.Read.All* and admins who configured role based access restriction are advised to update the **API permissions** for the registered application in *Azure AD*. [ROLES+ACCESS, SYNC, INTRANET]
* Fix: The logic to detect the blog ID in a WordPress Multisite (WPMU) will always test with a trailing slash. [LOGIN]
* Fix: A (custom) login message - for example created with LoginPress - will now show as expected. [ALL]
* Fix: Non-dynamic roles in an identities configuration used to enable RLS when embedding Power BI content no longer causes a fatal error. [M365 APPS, INTRANET]
* Fix: It is now possible to save empty custom user profile fields when manually updating a user's profile. [CUSTOM USER FIELDS, SYNC, INTRANET]

= Older versions =

Please check the [online change log](https://www.wpo365.com/change-log/) for previous changelogs.
