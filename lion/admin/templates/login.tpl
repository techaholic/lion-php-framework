<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="<comp:uri route="resource" parameters="resource=themes/default/lion.css"/>">

<title>lion|framework</title>
</head>

<body leftmargin="0" topmargin="0" class="LION_BODY">
<div class="Content">
<img src="<comp:uri parameters="resource=images/background/header_title.jpg" route="resource"/>" border="0" width="222" height="56">

<table border="0" cellpadding="0" style="border-collapse: collapse" width="100%">
	<tr>
		<td width="10" height="22" style="border-left-width: 1px; border-top-width: 1px">&nbsp;</td>
		<td width="59" height="22" style="border-bottom:1px dashed #999999; border-left-width: 1px; border-right: 1px dashed #999999; border-top-width: 1px; ">&nbsp;</td>
		<td height="22"  valign="bottom" width="543" nowrap style="border-left-width: 1px; border-right-width: 1px; border-top-width: 1px; border-bottom: 1px dashed #999999">
		&nbsp;{$welcome_to_lion}</td>
		<td height="22" valign="bottom">
		&nbsp;</td>
	</tr>
	<tr>
		<td width="70" nowrap style="border-left-width: 1px; border-right: 1px dashed #999999; border-top-width: 1px; border-bottom-width: 1px" valign="top" align="center" colspan="2">
		<br>
		<img src="<comp:uri parameters="resource=images/multilanguage/index_english.gif" route="resource"/>" border="0" width="40" height="40" alt="English">
		<br>
		<img src="<comp:uri parameters="resource=images/multilanguage/index_german.gif" route="resource"/>" border="0" width="40" height="40" alt="Deutsch">
		<br>
		<img src="<comp:uri parameters="resource=images/multilanguage/index_french.gif" route="resource"/>" border="0" width="40" height="40" alt="Français">
		<br>
		<img src="<comp:uri parameters="resource=images/multilanguage/index_spanish.gif" route="resource"/>" border="0" width="40" height="40" alt="Español">
		<br>
		<img src="<comp:uri parameters="resource=images/multilanguage/index_italian.gif" route="resource"/>" border="0" width="40" height="40" alt="Italiano">
		<br>
		<img src="<comp:uri parameters="resource=images/multilanguage/index_dutch.gif" route="resource"/>" border="0" width="40" height="40" alt="Nederlands">
		</td>
		<td valign="top" width="100%" colspan="2">
		<table border="0" cellpadding="0" style="border-collapse: collapse" width="100%" id="table2">
			<tr>
				<td height="50" colspan="4" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<font class="stitle"><comp:resource key="form_logon_title"/></font></td>
			</tr>
			<tr>
				<td width="80" nowrap heigth="40">&nbsp;</td>
					<comp:form name="logon" method="post" target="hidden_frame" actionCode="logon">
                    <comp:formparameter parameterName="prop:REQUEST_LION_ADMIN_AREA" parameterValue="1"/>
					<td valign="baseline" align="center" width="500" height="40">

					<table cellpadding="0" cellspacing="0" border="0" width="414">
					<tr>
						<td colspan="3" height="34">
						    <table border="0" cellpadding="0" style="border-collapse: collapse" width="413" height="34" background="<comp:uri route="resource" parameters="resource=images/background/form_index_logon.gif"/>">
								<tr>
								<td align="center" valign="bottom">{$login_label} 
								<input type="text" class="inputbox_style" style="height:16" size="15" maxlength="30" name="login" id="login" tabindex="1" onkeydown="javascript: if(event.keyCode == 13) document.forms['logon'].submit();">&nbsp; 
								<comp:resource key="password"/> 
								<input type="password" class="inputbox_style" style="height:16" size="15" maxlength="32" name="password" id="password" tabindex="2" onkeydown="javascript: if(event.keyCode == 13) document.forms['logon'].submit();">&nbsp; 
						        <comp:commandlink>
						          <comp-property name="caption">{$enter_label}</comp-property>
						        </comp:commandlink>		
								<img src="<comp:uri parameters="resource=images/arrows/link_arrow.gif" route="resource"/>" border="0">
								</td>
								</tr>
								<tr>
									<td align="center" valign="top" height="11">
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
					<td width="278" height="11" valign="top">&nbsp;
					</td>
					<td height="11" nowrap>&nbsp;
					</td>
					</tr>
					</table>
				</td>
				</comp:form>				

			</tr>
			<tr>
				<td width="80" nowrap >&nbsp;</td>
				<td nowrap colspan="2" align="left">
				<table border="0" cellpadding="0" style="border-collapse: collapse" width="500" height="200" background="<comp:uri route="resource" parameters="resource=images/background/lion_logo_big.gif"/>">
					<tr>
						<td height="31">
						<p>&nbsp;</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<font class="stitle"><comp:resource key="index_advise_title"/></font>
						<br>
						<comp:resource key="index_advise_description"/><p>&nbsp;</td>
					</tr>
				</table>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<br><br><br><br><br>
<IFRAME name=hidden_frame width="1" height="1" frameborder="no"></IFRAME>
<IFRAME name=hidden_frame width="1" height="1" frameborder="no"></IFRAME>
<br><br>
</div>
<script language="JavaScript" type="text/javascript">
	document.logon.login.focus();
</script>
