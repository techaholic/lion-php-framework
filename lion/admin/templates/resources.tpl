<link type="text/css" rel="stylesheet" href="<comp:uri parameters="resource=forms/sortabletable/css/sortabletable.css" route="resource"/>"/>
<center>
<table border="0" cellpadding="0" style="border-collapse: collapse" width="95%" id="table1">
<comp:form name="search_resources" method="POST" controllerCode="resources">
	<tr>
		<td width="5%" nowrap>
			<img src="<comp:uri parameters="resource=images/icons/big_resources.gif" route="resource"/>" width="64" height="64" border="0" align="left">
		</td>
		<td width="80%"><font class="big_title"><comp:resource key="resources_title"/></font><br><img border="0" src="<comp:uri parameters="resource=images/background/title_line.gif" route="resource"/>" width="200" height="1"><br>
		<comp:resource key="resources_brief_description"/>
		</td>
	</tr>
</table>
<table cellpadding=0 cellspacing=0 width="95%">
	<tr><td width="100%">
	<table border=0 cellpadding="2" style="border-collapse: collapse" width="100%" height="300">
		<tr><td valign="top">
		<!-- SEARCH resource TAB -->
        <comp:tabpane name="search_resources_tab">
          <comp:tabpage name="searchTabPage">
            <comp-property name="caption"><comp:resource key="search_tab"/></comp-property>
		  <table border="0" cellpadding="2" style="border-collapse: collapse" width="20%" id="table2" height="300">
			<tr>
				<td align="right" width="20%" valign="top" height="16"></td>
				<td width="43%" valign="top" height="16" align="right">
				    <comp:commandbutton class="button_style" name="search">
				      <comp-property name="caption"><comp:resource key="search"/></comp-property>
				    </comp:commandbutton>
				</td>
			</tr>
			<tr>
				<td align="right" width="20%" height="16" valign="top"><comp:resource key="resource_key"/></td>
				<td width="43%" height="16">
				    <comp:inputbox name="resource_key" fillLastValue="true" size="30" class="inputbox_style" style="height:16"/>
				    <comp:errorlabel name="resource_key_validation"/>
					<comp:validation_rule
					  component        = "resource_key"
					  componentStyleOnError = "inputbox_style_error"
					  validFormat      = "identifier"
					  validateOnKeyUp  = "true"
					  validateOnChange = "true"
					  reportComponent  = "resource_key_validation"
					/>				    
				</td>
			</tr>
			<tr>
				<td align="right" width="20%" height="16" valign="top"><comp:resource key="resource_storage_medias"/></td>
				<td width="80%" height="16">
				    <comp:combobox name="resource_category" style="height:16px; width:195px"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="63%" colspan="2" valign="top"></td>
			</tr>
		  </table>
		  </comp:tabpage>
		</comp:tabpane>
		</td>
		<td width="80%" colspan="2" valign="top" align="center">

		</td>
		</tr>
	</table>
	</td>
	</tr>
</comp:form>
</table>
<br><br>
