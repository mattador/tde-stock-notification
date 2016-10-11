TDE Stock Notifier
==================
 
last update - 2016-09-01

A custom Magento 2 module used to increase sales conversion, by notifying subscribed clients that products are back in stock, in real time. The module levages some of Magento 2's unique design patterns such as Plugins, D.I. and store scope emulation for emails.

The standard M2EE core Stock Notification module was not flexible enough for TDE's unique business use case. For instance customers have to create a Magento customer account to sign up for notifications, and the notifications are not in real time. Additionally, the core module was extremely buggy at the time of release, and TDE's clients were spammed on a daily basis with repeated notifications of the same products. This module aims to address some of these issues.

Todo
====
- Set up a proper service contract for data manipulation on custom resource
- Create a data API and collection factory for accessing data as per best practice in Magento 2
- Save missing timestamp on tde_stock_notifications
- Convert frontend styles to SCSS and store in proper location