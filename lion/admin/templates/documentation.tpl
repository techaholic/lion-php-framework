<table cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td valign="top" class="doc_content">
    {$doc_content}
    </td>
    {if $show_toc}
    <td valign="top" width="234px" valign="top" style="padding-bottom: 5px; padding-top: 10px;" class="left_column_main">
      <table class="highlighted" width="234" height="100%" cellspacing="0" border="0">
      <tr>
        <td class="highlighted_up" valign="top"></td>
      </tr>
      <tr>
        <td class="highlighted_body" nowrap="nowrap" valign="top">
        <b><a href="<comp:uri route="lion_documentation" parameters="page=index"/>">Reference Manual</a></b> 
        {$toc}
        <b>See also:</b><br>
        <ul>
          <li><a href="<comp:uri route="lion_documentation" parameters="page=elementindex.html"/>">Index of all elements</a></li>
        </ul>
        </td>
      </tr>
      <tr>
        <td class="highlighted_bottom" valign="top"></td>
      </tr>
      </table>
    </td>
    {/if}
  </tr>
</table>

