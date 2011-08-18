<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="<comp:uri route="resource" parameters="resource=themes/default/lion.css"/>">

<title>lion|framework</title>
</head>

<body leftmargin="0" topmargin="0" class="LION_BODY">
<div class="Content">
<img src="<comp:uri parameters="resource=images/background/header_title.png" route="resource"/>" border="0" width="222" height="56">
<table border="0" cellpadding="0" style="border-collapse: collapse" width="95%">
	<tr>
		<td width="5%" valign="top" nowrap>
			<img src="<comp:uri parameters="resource=images/icons/box_blackandwhite.png" route="resource"/>" width="128" height="128" border="0" align="left">
		</td>
		<td width="80%" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="550">
		<tr><td valign="bottom" nowrap><font class="very_big_title">Bootstrap Assistant</font></td></tr>
		<tr><td height="1" nowrap><img border="0" src="<comp:uri parameters="resource=images/background/title_line.gif" route="resource"/>" width="100%" height="1"></td></tr>
		<tr><td valign="top" align="right" nowrap>Welcome to <i>Lion Framework</i> Bootstrap Assistant</td></tr></table>
		<br><br>
		Fill the following form in order to provide information regarding the application to bootstrap to:
		<br><br>
		<comp:form name="bootstrap_form">
		<table cellpadding="5" cellspacing="5" border="0">
		  <tr><td align="right" nowrap="nowrap">Application name</td><td><comp:inputbox class="INPUTBOX_STYLE" contextHelp="A name to identify your application. i.e. myForumApplication" name="application_name"/></td></tr>
		  <tr><td>&nbsp;</td><td><comp:commandbutton type="submit" name="submit" caption="Bootstrap!"/></td></tr>
		</table>
		</comp:form>
		</td>
	</tr>
</table>
<br><br><br>

<comp:validationrule component="application_name" validateOnlyOnSubmit="true" mandatory="true"/>
