=== IntaSend Pay Button === 
Contributors: Felix Cheruiyot, Mugendi Gitonga
Donate link: https://intasend.com/
Tags: Payments,Checkout,Visa,Mastercard,M-Pesa,Payment Gateway
Requires at least: 6.2
Tested up to: 6.6.1
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Securely collect M-Pesa and card payments (Visa and Mastercard).

== Description ===

IntaSend enables businesses to get paid with mobile and card payments (Visa and Mastercard). We automatically help customers to complete payments at checkout. Customer securely pay for your goods and services and you are able to track from the IntaSend dashboard and monitor every transaction.

== Frequently Asked Questions ==

= How do I start using IntaSend for Payment Collection? =

1. Add the plugin "IntaSend Pay Button" to your site.
2. Create account at [IntaSend Payment Gateway](https://intasend.com) to get generate API keys.
3. Add the API keys to your plugin under wp-admin.
4. Publish and start accepting Card and Mobile payments.

= How do it test before live publishing? =

Generate test keys from the [sandbox](https://sandbox.intasend.com/) and configure the plugin to be on test mode. Use the following [test cards](https://developers.intasend.com/sandbox-and-live-environments#test-card-numbers) for your test.

= How much does it cost to use IntaSend? =

Please check our [pricing here](https://intasend.com/collect-payments/)

= How do I get support? =

Feel free to contact us through https://support.intasend.com for both transaction queries and developers support respectively.

== Use of Third-Party Services ==

This plugin utilizes the IntaSend Inline JS SDK to integrate payment functionalities into your WordPress site. The SDK, available at IntaSend Inline JS SDK, communicates with the IntaSend API to handle payment processing.
Service Details

    Service Name: IntaSend Inline JS SDK - https://www.npmjs.com/package/intasend-inlinejs-sdk/v/2.0.2
    API Documentation: IntaSend Payment Button Documentation - https://developers.intasend.com/docs/payment-button

== Data Transmission ==

By using this plugin, data is transmitted to and from the IntaSend service. We recommend reviewing IntaSend's Terms of Use(https://intasend.com/terms/) and Privacy Policy(https://intasend.com/privacy-policy/) to understand how your data is managed and protected.

For any legal inquiries or concerns regarding data transmissions, please refer to the IntaSend 

== Changelog ==

= 1.0.0 =
* Initial setup
* Enabled sandbox and live support
* Card and mobile payments
* Default currency setup

= 1.0.1 =
* Redirect URL fix


= 1.0.2 =
* Redirect URL per button

= 1.0.5 =
* Redirect URL per button complete

= 1.0.7 =
*Editable buttons