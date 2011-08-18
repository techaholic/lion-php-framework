{literal}
<br><br>
<style type="text/css">
.settings_table {border-collapse: collapse;}
.center {text-align: center;}
.center table { margin-left: auto; margin-right: auto; text-align: left;}
.center th { text-align: center !important; }
.settings_table td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
.settings_h1 {font-size: 150%;}
.settings_h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccccff; font-weight: bold; color: #000000;}
.h {background-color: #9999cc; font-weight: bold; color: #000000;}
.v {background-color: #cccccc; color: #000000;}
.vr {background-color: #cccccc; text-align: right; color: #000000;}
hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
</style>
{/literal}
<div class="center">
<table class="settings_table" border="0" cellpadding="3" width="600">
<tr class="h"><td>
<h1 class="p">Lion {$lion_version} Info</h1>
</td></tr>
</table><br />
<table class="settings_table" border="0" cellpadding="3" width="600">
<tr><td class="e">Build Version </td><td class="v">{$lion_version}</td></tr>
<tr><td class="e">Build Date </td><td class="v">{$lion_build_date}</td></tr>
<tr><td class="e">Build Changelist </td><td class="v">{$lion_build_changelist}</td></tr>
</table><br />
<hr />
<h1 class="settings_h1">Configuration</h1>
<h2 class="settings_h2">Application settings</h2>
<table class="settings_table" border="0" cellpadding="3" width="600">
<tr class="h"><th>Directive</th><th>Value</th></tr>
{foreach from=$settings item=setting}
<tr><td class="e">{$setting.name}</td><td class="v">{$setting.value}</td></tr>
{/foreach}
</table><br />
<h2 class="settings_h2">Lion runtime directives</h2>
<table class="settings_table" border="0" cellpadding="3" width="600">
<tr class="h"><th>Directive</th><th>Value</th></tr>
{foreach from=$runtime_directives item=runtime_directive}
<tr><td class="e">{$runtime_directive.name}</td><td class="v">{$runtime_directive.value}</td></tr>
{/foreach}
</table>
<br><br>
