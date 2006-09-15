<h2>{"Receiver"|i18n( 'survey' )} (id {$question.id})</h2>

<div class="block">
{"N. B. If you enter just one email address, user will not see this question. Instead the posting
will be directly sent to the address."|i18n( 'survey' )}
<br />
<label>{"Text of question"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<input type="text" name="SurveyQuestion_{$question.id}_Text" value="{$question.text|wash('xhtml')}" size="70" />
<br />

<label>{"Receivers"|i18n('survey')}</label>
<table border="0" cellspacing="0" cellpadding="2">
<tr>
  <td><label>{"Name"|i18n( 'survey' )}</label></td>
  <td><label>{"Email"|i18n( 'survey' )}</label></td>
  <td><label>{"Preselected"|i18n( 'survey' )}</label></td>
  <td><label>{"Order"|i18n( 'survey' )}</label></td>
  <td><label>{"Selected"|i18n( 'survey' )}</label></td>
</tr>
{section name=option loop=$question.options sequence=array(bglight,bgdark)}
<tr class="{$:sequence}">
  <td><input name="SurveyReceiver_{$question.id}_{$:item.id}_Label" type="text" value="{$:item.label|wash('xhtml')}" size="30" /></td>
  <td><input name="SurveyReceiver_{$question.id}_{$:item.id}_Value" type="text" value="{$:item.value|wash('xhtml')}" size="20" /></td>
  <td align="center"><input name="SurveyReceiver_{$question.id}_{$:item.id}_Checked" type="checkbox" {section show=$:item.checked|eq(1)}checked{/section} /></td>
  <td><input name="SurveyReceiver_{$question.id}_{$:item.id}_TabOrder" type="text" size="2" value="{$:item.id|wash('xhtml')}" /></td>
  <td align="center"><input name="SurveyReceiver_{$question.id}_{$:item.id}_Selected" type="checkbox" /></td>
</tr>
{/section}
</table>

{section show=$disabled|not}
<div class="buttonblock">
<input class="smallbutton" type="submit" name="SurveyReceiver_{$question.id}_NewOption" value="{'New option'|i18n( 'survey' )}" />
<input class="smallbutton" type="submit" name="SurveyReceiver_{$question.id}_RemoveSelected" value="{'Remove selected'|i18n( 'survey' )}" />
</div>
{/section}

</div>
