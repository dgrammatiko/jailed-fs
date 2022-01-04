---
eleventyNavigation:
  key: Home
  url: '/'
  order: 1
permalink: /
layout: base.njk
title: Restricted FS
description: Restricted FS for Joomla\'s v4+ Media Manager
---

# Restricted FS

{% img %}

### Latest Version
{% assign fff = downloads | first%}

Download the latest version: [{{fff.version}}]({{ metaInfo.url }}/dist/{{fff.name}})

### What it does

Introduces a restricted access to a specific part of the images folder (`images/user/userName`, where `userName` is the actual user name). The plugin could be enabled per user group. The code is tiny, extremelly efficient and doesn't require any configuration other than selecting the user groups that will act upon. Also no nasty hacks or monkey patched code. It's a proper solution that doesn't need a total replacement of the Media Manager...

In pictures, the Media manager goes from accessing everything:

{% image "./site/images/before.png", "Default Joomla Media Manager allowing access to everything", "(min-width: 30em) 50vw, 100vw" %}

...to restricting access to a specific folder (for the current user):

{% image "./site/images/after.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

### Also it works, as expected both inside any Editor:

{% image "./site/images/after1.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

{% image "./site/images/after2.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

{% image "./site/images/after3.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

{% image "./site/images/after4.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

### ...and in any Form Field type `Media`:

{% image "./site/images/after-field1.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

{% image "./site/images/after-field2.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}

{% image "./site/images/after-field3.png", "Restricted Joomla Media Manager", "(min-width: 30em) 50vw, 100vw" %}
