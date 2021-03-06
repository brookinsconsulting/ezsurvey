<h2>{"Single/Multiple choice"|i18n( 'survey' )} (id {$question.id})</h2>

<div class="block">
<label>{"Text of question"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<input type="text" name="SurveyQuestion_{$question.id}_Text" value="{$question.text|wash('xhtml')}" size="70"  />


<label>{"Rendering style"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<select name="SurveyQuestion_{$question.id}_Num" >
  <option value="1"{section show=$question.num|eq(1)} selected{/section}>{"Radio buttons in a row"|i18n( 'survey' )}</option>
  <option value="2"{section show=$question.num|eq(2)} selected{/section}>{"Radio buttons in a column"|i18n( 'survey' )}</option>
  <option value="3"{section show=$question.num|eq(3)} selected{/section}>{"Checkboxes in a row"|i18n( 'survey' )}</option>
  <option value="4"{section show=$question.num|eq(4)} selected{/section}>{"Checkboxes in a column"|i18n( 'survey' )}</option>
  <option value="5"{section show=$question.num|eq(5)} selected{/section}>{"Selector"|i18n( 'survey' )}</option>
</select>
<br />

<label>Options</label>
<table border="0" cellspacing="0" cellpadding="2">
<tr>
  <td><label>{"Option label"|i18n( 'survey' )}</label></td>
  <td><label>{"Value"|i18n( 'survey' )}</label></td>
  <td><label>{"Checked"|i18n( 'survey' )}</label></td>
  <td><label>{"Order"|i18n( 'survey' )}</label></td>
  <td><label>{"Selected"|i18n( 'survey' )}</label></td>
</tr>
{section name=option loop=$question.options sequence=array(bglight,bgdark)} {* used namespace instead of var because of the bug with the 'sequence' *}
<tr class="{$:sequence}">
  <td><input name="SurveyMC_{$question.id}_{$:item.id}_Label" type="text" value="{$:item.label|wash('xhtml')}" size="30" /></td>
  <td><input name="SurveyMC_{$question.id}_{$:item.id}_Value" type="text" value="{$:item.value|wash('xhtml')}" size="5"  /></td>
  <td align="center"><input name="SurveyMC_{$question.id}_{$:item.id}_Checked" type="checkbox" {section show=$:item.checked|eq(1)}checked{/section} /></td>
  <td><input name="SurveyMC_{$question.id}_{$:item.id}_TabOrder" type="text" size="2" value="{$:item.id|wash('xhtml')}" /></td>
  <td align="center"><input name="SurveyMC_{$question.id}_{$:item.id}_Selected" type="checkbox" ></td>
</tr>
{/section}
</table>

<div class="buttonblock">
<input class="button" type="submit" name="SurveyMC_{$question.id}_NewOption" value="{'New option'|i18n( 'survey' )}" />
<input class="button" type="submit" name="SurveyMC_{$question.id}_RemoveSelected" value="{'Remove selected'|i18n( 'survey' )}" />
</div>
</div>
