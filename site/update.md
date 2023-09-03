---
permalink: /update.xml
---
<?xml version="1.0" encoding="utf-8"?>
<updates>
{% for curItem in releases %}
  <update>
    <name>Restricted File System</name>
    <version>{{curItem.version}}</version>
    <description>Restricted File System</description>
    <infourl title="Restricted File System">></infourl>
    <downloads>
    <downloadurl type="full" format="zip">{{metaInfo.url}}/dist/plg_system_restrictedfs_v{{curItem.version}}.zip</downloadurl>
    </downloads>
    <targetplatform name="joomla" version="4"/>
    <tags><tag>{{curItem.type}}</tag></tags>
    <targetplatform name="joomla" version="{{curItem.joomlaVer}}"/>
    <sha512>{{curItem.sha512}}</sha512>
    <php_minimum>{{curItem.phpMin}}</php_minimum>
    <type>plugin</type>
    <element>restrictedfs</element>
    <client>site</client>
    <folder>system</folder>
  </update>
{% endfor %}
</updates>
