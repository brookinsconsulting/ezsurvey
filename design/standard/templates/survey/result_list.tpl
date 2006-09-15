{let can_edit_results=$survey.can_edit_results}

<form enctype="multipart/form-data" method="post" action={concat("/survey/result_list/", $survey.id )|ezurl}>

<h1>{"Survey results for %title"|i18n('survey', '', hash( '%title', $survey.title|wash ) )}</h1>

<table class="list" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
  <th>{"ID"|i18n('survey')}</th>
  <th>{"Result"|i18n('survey')}</th>
  <th>{"Participant"|i18n('survey')}</th>
  <th>{"Evaluated"|i18n('survey')}</th>
  <th>{"Edit"|i18n('survey')}</th>
  <th>{"Remove"|i18n('survey')}</th>
</tr>
{section loop=$result_list sequence=array('bglight','bgdark')}
<tr class="{$:sequence}">
  <td>{$:item.id}</td>
 {* <td><a href={concat( "/survey/rview/", $:item.id )|ezurl}>{"View"|i18n('survey')}</a></td>*}
  <td><a href={concat( "/survey/rview/", $:survey.id,"/offset/",$:index )|ezurl}>{"View"|i18n('survey')}</a></td>
  {let user=fetch( content, object, hash( object_id, $:item.user_id ) )}
  <td><a href={$user.main_node.url_alias|ezurl}>{$user.name|wash}</a></td>
  {/let}
  <td>{$:item.tstamp|l10n(datetime)}</td>
  <td align="center">
    {section show=$can_edit_results}
      <a href={concat( "/survey/result_edit/", $:item.id )|ezurl}><img src={"edit.png"|ezimage} border="0" title="{'Edit'|i18n('survey')}" /></a>
    {section-else}
      &nbsp;
    {/section}
  </td>
  <td align="right">
    {section show=$can_edit_results}
      <input type="checkbox" name="DeleteIDArray[]" value="{$:item.id}">
    {section-else}
      &nbsp;
    {/section}
  </td>
</tr>
{/section}
<tr>
  <td colspan="5"><div class="buttonblock"></div></td>
  <td align="right"><input type="image" name="RemoveButton" value="Remove" src={"trash.png"|ezimage} /></td>
</tr>



</table>

</form>

{/let}

{include name=navigator
         uri='design:navigator/google.tpl'
	 page_uri=concat('/survey/result_list/',$survey.id)
	 item_count=$survey.result_count
	 view_parameters=$view_parameters
	 item_limit=$limit}
