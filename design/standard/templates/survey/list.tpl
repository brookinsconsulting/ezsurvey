<form enctype="multipart/form-data" method="post" action={"/survey/list"|ezurl}>

<h1>{"Survey List"|i18n('survey')}</h1>

<table class="list" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
  <th>{"ID"|i18n('survey')}</th>
  <th>{"Survey title"|i18n('survey')}</th>
  <th>{"Enabled"|i18n('survey')}</th>
  <th>{"Published"|i18n('survey')}</th>
  <th>{"Validity"|i18n('survey')}</th>
  <th>{"Edit"|i18n('survey')}</th>
  <th>{"Copy"|i18n('survey')}</th>
  <th>{"Results"|i18n('survey')}</th>
  <th>{"Remove"|i18n('survey')}</th>
</tr>
{section name=Survey loop=$survey_list sequence=array('bglight','bgdark')}
{let can_view=and( $:item.enabled, $:item.published, $:item.valid )}
<tr class="{$:sequence}">
  <td align="right">{$:item.id}</td>
  <td>{section show=$:can_view}<a href={concat('/survey/view/',$:item.id)|ezurl}>{/section}{section show=$:item.title}{$:item.title|wash}{section-else}{'Survey no.'|i18n('survey')} {$:item.id}{/section}{section show=$:can_view}</a>{/section}</td>
  <td>
    {switch match=$:item.enabled}
      {case match=0}{"not enabled"|i18n('survey')}{/case}
      {case match=1}{"enabled"|i18n('survey')}{/case}
    {/switch}
  </td>
  <td>
    {switch match=$:item.published}
      {case match=0}{"not published"|i18n('survey')}{/case}
      {case match=1}{"published"|i18n('survey')}{/case}
    {/switch}
  </td>
  <td>
    {section show=$:item.valid}
    {"valid"|i18n('survey')}
    {section-else}
    {"not valid"|i18n('survey')}
    {/section}
  <td align="center">
  <a href={concat("/survey/edit/",$:item.id)|ezurl}><img src={"edit.png"|ezimage} border="0" title="{"Edit"|i18n('survey')}" /></a>
  </td>
  <td align="center">
  <a href={concat("/survey/copy/",$:item.id)|ezurl}><img src={"copy.gif"|ezimage} border="0" title="{"Copy and edit"|i18n('survey')}" /></a>
  </td>
  <td align="center">
  <a href={concat("/survey/result/",$:item.id)|ezurl}><img src={"attach.png"|ezimage} border="0" title="{"Results"|i18n('survey')}" /></a>
  </td>
  <td align="center">
  <a href={concat("/survey/remove/",$:item.id)|ezurl}><img src={"trash.png"|ezimage} border="0" title="{"Remove"|i18n('survey')}" /></a>
  </td>
</tr>
{/let}
{/section}
</table>

<div class="buttonblock">
<input class="defaultbutton" type="submit" name="SurveyNewSurveyButton" value="{'New Survey'|i18n( 'survey' )}" />
</div>

</form>
