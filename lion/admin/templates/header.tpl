<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="<comp:uri parameters="resource=themes/default/lion.css" route="resource"/>">
<title>lion|framework</title>
</head>
<body leftmargin="0" topmargin="0" class="LION_BODY">
<comp:menu configuration="config://menu.pc.xml"/>
<div class="Content">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="100%" height="56">
	<tr>
		<td align="left">
			<a href="<comp:uri route="lion" controller="index"/>"><img src="<comp:uri parameters="resource=images/background/header_title.png" route="resource"/>" name="header_title" alt="<comp:resource key="go_to_home"/>" border="0" width="222" height="56"></a></td>
		<td height="12" valign="bottom" align="right" colspan="4"><img src="<comp:uri parameters="resource=images/icons/editing.gif" route="resource"/>" border=0 align="absmiddle">{$application_in_edition}&nbsp;&nbsp;</td>
	</tr>
</table>
<!-- Menu: -->
<table border="0" cellpadding="0" style="border-collapse: collapse" width="100%" id="menu_bar" height="20" bgcolor="#6D3E2B">
	<tr>
		<td align="left"></td><td class="menu" align=right><font color="#FFFFFF">(?) <a href="<comp:uri route="lion_documentation" parameters="page=index"/>" target=_blank><font color="#FFFFFF"><b>Documentation</b></font></a> | <a href="<comp:uri route="lion" controller="index"/>"><font color="#FFFFFF"><b>
		<img src="<comp:uri parameters="resource=images/icons/header_home.gif" route="resource"/>" width="15" height="12" border="0" align="absbottom"><comp:resource key="home"/></b></font></a>
<!-- {section name=logout_access show=$logout_access} -->
		| <a href="<comp:uri action="logout" parameters="prop:REQUEST_LION_ADMIN_AREA=1"/>"><font color="#FFFFFF"><b><img src="<comp:uri parameters="resource=images/icons/header_exit.gif" route="resource"/>" width="15" height="12" border="0" align="absbottom"><comp:resource key="logout"/></b></font></a> 
<!-- {/section} -->		
		</font>&nbsp;&nbsp;</td>
	</tr>
</table>
<table border="0" cellpadding="0" style="border-collapse: collapse" width="100%" height="8" background="<comp:uri parameters="resource=images/background/upper_bar_shadow.gif" route="resource"/>">
	<tr>
		<td></td>
	</tr>
</table>
<table height="400px" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td valign="top">