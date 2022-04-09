---
eleventyNavigation:
  key: Documentation
  url: '/documentation/index.html'
  order: 3
permalink: documentation/index.html
layout: base.njk
title: Documentation
description: Restricted FS Documentation
---

# Installation

{% assign fff = downloads | first %}

For the installation the procedure is the expected one and once the package is installed the fuctionality is immediately available. Here are the two different ways to install the package:
- Using drag and drop
  - Download the package [{{fff.version}}]({{ metaInfo.url }}/dist/{{fff.name}})
  - Login to your site's backend and go to system from the menu {% image "./site/images/install_1.png", "System Dashboard", "(min-width: 30em) 50vw, 100vw" %}

  - Click on the link `Extensions` in the `Install` card. The new page should have the tab `Upload Package File` selected, if not click that tab.   {% image "./site/images/install_2.png" "Drag and drop installation", "Drag and drop installation", "(min-width: 30em) 50vw, 100vw" %}

  - Drag and drop the file in the dropdown area. Done!
- Using a link
  - Login to your site's backend and go to system
  - Click on the link `Extensions` in the `Install` card
  - On the new page click on the tab `Install from URL`. {% image "./site/images/install_3.png" "Drag and drop installation", "Install from URL", "(min-width: 30em) 50vw, 100vw" %}
  - Paste the link: 
    `https://restrictedfs.dgrammatiko.dev/dist/{{fff.name}}`
    and click the button Check and Install. Done.

## Set up the user groups

The plugin has only one field that allows the administrator to set which groups will have access only to the restricted folder. It should be self explanatory, select the groups that users belonging to them will NOT access the full images folder but rather their own folder named as their own user name in a path like `images/users/{hased(username)}`.

{% image "./site/images/setup.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

It goes without saying that any selected User Group need to have the permissions adjusted in the Media: Options.
Eg.:

{% image "./site/images/setup2.png", "Joomla Media Manager Permissions", "(min-width: 30em) 50vw, 100vw" %}
