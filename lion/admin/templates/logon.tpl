<script language="javascript">
<!-- {literal}
  var errorMsg="{/literal}{$errorMsg}{literal}";
  if(errorMsg != "") {
    alert(errorMsg);
  }
  var redirectPage="{/literal}{$redirectPage}{literal}";
  if(redirectPage != "") {
  	if(parent != null) {
    	parent.location.href=redirectPage;
  	}
  	else {
  		document.location.href=redirectPage;
  	}
  }
{/literal}
//-->
</script>