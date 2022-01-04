---
eleventyNavigation:
  key: Downloads
  url: '/downloads/index.html'
  order: 2
permalink: downloads/index.html
layout: base.njk
title: Downloads
---
# Releases
## Versions

{% for dl in downloads %}
- [{{dl.version}}]({{ metaInfo.url }}/dist/{{dl.name}})
{% else %}
- If you see this message the site is broken, please report it.
{% endfor %}

{% img %}
