=== WordPress + Microsoft Office 365 / Azure AD | INTRANET ===
Contributors: wpo365
Tags: office 365, O365, Microsoft 365, azure active directory, Azure AD, AAD, authentication, single sign-on, sso, SAML, SAML 2.0, OpenID Connect, OIDC, login, oauth, microsoft, microsoft graph, teams, microsoft teams, sharepoint online, sharepoint, spo, onedrive, SCIM, User synchronization, yammer, powerbi, power bi,
Requires at least: 4.8.1
Tested up to: 6.0
Stable tag: 18.0
Requires PHP: 5.6.40

== Description ==

Extends **WPO365 | LOGIN** and offers the deepest integration with the Microsoft Office 365 / Azure cloud, incl. apps for Power BI, SharePoint Online, Microsoft Graph and Yammer and support for Azure AD user provisioning (SCIM).

= Plugin Features =

== LOGIN (free) ==

- **Single sign-on (SSO)** for Microsoft Office 365 / Azure AD accounts [more](https://www.wpo365.com/sso-for-office-365-azure-ad-user/)
- Administrators can choose between **OpenID Connect** and **SAML** based single sign-on (SSO) [more](https://docs.wpo365.com/article/100-single-sign-on-with-saml-2-0-for-wordpress)
- New users that *Sign in with Microsoft* are **automatically registered** with your WordPress [more](https://www.wpo365.com/sso-for-office-365-azure-ad-user/)
- Restrict access to pages / posts in **intranet** mode [more](https://www.wpo365.com/make-your-wordpress-intranet-private/)
- Support for integration of your WordPress website into **Microsoft Teams** Tabs and Apps [more](https://docs.wpo365.com/article/70-adding-a-wordpress-tab-to-microsoft-teams-and-use-single-sign-on)
- **Send emails using Microsoft Graph** instead of SMTP from your WordPress website [more](https://docs.wpo365.com/article/108-sending-wordpress-emails-using-microsoft-graph)
- Support for **WordPress Multisite** [more](https://www.wpo365.com/support-for-wordpress-multisite-networks/)
- Client-side solutions can request access tokens e.g. for SharePoint Online and Microsoft Graph [more](https://www.wpo365.com/pintra-fx/)
- Authors can inject Pintra Framework apps into any page or post using a simple WordPress shortcode [more](https://www.wpo365.com/pintra-fx/)
- Developers can include a simple and robust API from [npm](https://www.npmjs.com/package/pintra-fx) [more](https://www.wpo365.com/pintra-fx/)
- **PHP hooks** for developers to build custom Microsoft Graph / Office 365 integrations [more](https://docs.wpo365.com/article/82-developer-hooks)

Now all editions of the plugin include four new modern Microsoft (Office) 365 apps

- Embed Microsoft **Power BI** content [more](https://www.wpo365.com/power-bi-for-wordpress/)
- **SharePoint Online** Library [more](https://www.wpo365.com/documents/) 
- **Microsoft Graph / Azure AD** based Employee Directory [more](https://www.wpo365.com/employee-directory/)
- **SharePoint Online** Search [more](https://www.wpo365.com/content-by-search/)


== PROFILE+ ==

- **All features of the LOGIN edition, plus ...**
- Complete the WordPress user profile with first, last and full name and email address [more](https://www.wpo365.com/sso-for-office-365-azure-ad-user/)

== LOGIN+ ==

- **All features of the PROFILE+ edition, plus ...**
- Let users choose to login with O365 or with WordPress [more](https://www.wpo365.com/redirect-to-login/)
- Require authentication for only a few **Private pages** [more](https://www.wpo365.com/private-pages/)
- Require authentication for all pages but not for the **Public homepage** [more](https://www.wpo365.com/public-homepage/)
- Redirect users to a **custom login error** page [more](https://www.wpo365.com/error-page/)
- Allow users from other Office 365 tenants to register (**Multitenant**) [more](https://www.wpo365.com/automatically-register-new-users-from-other-tenants/)
- Allow users with a Microsoft Services Account (**MSAL**) e.g. outlook.com to register (extranet) [more](https://www.wpo365.com/automatically-register-new-users-with-msal-accounts/)
- Prevent Office 365 user from changing their WordPress password and / or email address [more](https://www.wpo365.com/prevent-update-email-address-and-password/)
- Intercept manual login attempts for Office 365 users [more](https://www.wpo365.com/intercept-manual-login/)
- **Sign out from Microsoft Office 365** when signin out from your website [more](https://www.wpo365.com/intercept-manual-login/)
- Support for **single sign-out** [more](https://docs.wpo365.com/article/90-single-sign-out)

== SYNC ==

- **All features of the LOGIN+ edition, plus ...**
- (On-demand and scheduled) **User synchronization** from Azure Active Directory to WordPress (per user or in batches) [more](https://www.wpo365.com/synchronize-users-between-office-365-and-wordpress/)
- **Delete / de-activate** WordPress users without a matching Azure AD account [more](https://www.wpo365.com/synchronize-users-between-office-365-and-wordpress/)
- Dynamically assign **WordPress user role(s)** based on Azure AD group membership(s) [more](https://www.wpo365.com/role-based-access-using-azure-ad-groups/)
- Dynamically assign **WordPress user role(s)** based on Azure AD User properties [more](https://www.wpo365.com/role-based-access-using-azure-ad-user-properties/)
- Dynamically assign **itthinx Groups** based on Azure AD group membership(s) [more](https://www.wpo365.com/role-based-access-using-azure-ad-groups/)
- Dynamically assign **itthinx Groups** based on Azure AD User properties [more](https://www.wpo365.com/assign-itthinx-groups-based-on-azure-ad-user-properties/)
- Synchronize **WordPress and / or BuddyPress user profiles** with Azure AD e.g. job title, department and mobile phone [more](https://www.wpo365.com/extra-buddypress-profile-fields-from-azure-ad/)
- Replace a user's default **WordPress avatar** with a profile image downloaded from Office 365 [more](https://www.wpo365.com/office-365-profile-picture-as-wp-avatar/)
- **Azure AD group membership(s) based access** (and deny all others) [more](https://www.wpo365.com/role-based-access-using-azure-ad-groups/)
- Place a customizable **Sign in with Microsoft** link on a post, page or theme using a simple shortcode [more](https://www.wpo365.com/authentication-shortcode/)

== INTRANET ==

- **All features of the SYNC edition, plus ...**
- Support for Azure AD User provisioning (**SCIM**) [more](https://docs.wpo365.com/article/59-wordpress-user-provisioning-with-azure-ad-scim)
- Advanced versions of the INTRANET apps that can be customized using **Handlebars.js templates** [more](https://www.wpo365.com/working-with-handlebars-templates/)
- **SharePoint Online / OneDrive Library** with support for folder and breadcrumb navigation [more](https://www.wpo365.com/documents/)
- Recently used documents [more](https://www.wpo365.com/documents/)
- **SharePoint Online Search** with support for query templates, auto-search, templates and [more](https://www.wpo365.com/content-by-search/)
- Employee Directory with a builtin **interactive clickable org(anizational) chart** incl. support for user profile images and additional fields (Microsoft Graph / Azure AD) [more](https://www.wpo365.com/employee-directory/)
- Microsoft **Power BI** [more](https://www.wpo365.com/power-bi-for-wordpress/)
- **Yammer** feed(s) [more](https://www.wpo365.com/yammer-for-wordpress/)

https://youtu.be/aIdbkmbdDog

= Prerequisites =

- Make sure that you have disabled caching for your Website in case your website is an intranet and access to WP Admin and all pubished pages and posts requires authentication. With caching enabled, the plugin may not work as expected
- We have tested our plugin with Wordpress >= 4.8.1 and PHP >= 5.6.40
- You need to be (Office 365) Tenant Administrator to configure both Azure Active Directory and the plugin
- You may want to consider restricting access to the otherwise publicly available wp-content directory

= Support =

We will go to great length trying to support you if the plugin doesn't work as expected. Go to our [Support Page](https://www.wpo365.com/how-to-get-support/) to get in touch with us. We haven't been able to test our plugin in all endless possible Wordpress configurations and versions so we are keen to hear from you and happy to learn!

= Feedback =

We are keen to hear from you so share your feedback with us at info@wpo365.com and help us get better!

== Installation ==

Please refer to [this post](https://www.wpo365.com/how-to-install-wordpress-office-365-login-plugin/) for detailed installation and configuration instructions.

== Frequently Asked Questions ==

== Screenshots ==

== Upgrade Notice ==

* Please check the online version of the [release notes for version 11.0](https://www.wpo365.com/release-notes-v11-0/).

== Changelog ==

Please check the [online change log](https://www.wpo365.com/change-log/) for changes.