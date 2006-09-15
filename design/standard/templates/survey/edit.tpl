<form enctype="multipart/form-data" method="post" action={concat("/survey/edit/",$survey.id)|ezurl}>

<input type="hidden" name="SurveyID" value="$survey.id" />

<table class="layout" border="0" cellspacing="0" cellpadding="5">

<tr>
    <td colspan="2">
    <div class="maincontentheader">
    <h1>{"Edit Survey"|i18n( 'survey' )} '{$survey.title|wash('xhtml')}' ({"id"|i18n('survey')} {$survey.id})</h1>
    {include uri="design:survey/edit_validation.tpl"}
    </div>
    </td>
</tr>

<tr>
    <td valign="top">
    <label>{'Survey title'|i18n( 'survey' )}</label><div class="labelbreak"></div>
    <input type="text" name="SurveyTitle" value="{$survey.title|wash('xhtml')}" size="70" />
    </td>
    <td valign="top" align="center">
    <label>{'Enabled'|i18n( 'survey' )}</label><div class="labelbreak"></div>
    <input type="checkbox" name="SurveyEnabled" {section show=$survey.enabled|eq(1)}checked="checked"{/section} />
    </td>
</tr>

<tr>
    <td valign="top" colspan="2">
    <label>{'Valid from'|i18n('survey')}</label>
    <table border="0">
    <tr><td>{'Year'|i18n( 'survey' )}</td><td>{'Month'|i18n( 'survey' )}</td><td>{'Day'|i18n( 'survey' )}</td><td>{'Hour'|i18n( 'survey' )}</td><td>{'Minute'|i18n( 'survey' )}</td></tr>
    <tr>
      <td><input name="SurveyValidFromYear" size="5" value="{$survey.valid_from_array.year}" /></td>
      <td><input name="SurveyValidFromMonth" size="3" value="{$survey.valid_from_array.month}" /></td>
      <td><input name="SurveyValidFromDay" size="3" value="{$survey.valid_from_array.day}" /></td>
      <td><input name="SurveyValidFromHour" size="3" value="{$survey.valid_from_array.hour}" /></td>
      <td><input name="SurveyValidFromMinute" size="3" value="{$survey.valid_from_array.minute}" /></td>
    </tr>
    <tr>
      <td colspan="5"><input type="checkbox" name="SurveyValidFromNoLimit" value="1" {section show=$survey.valid_from_array.no_limit}checked{/section} /> {'No limitation'|i18n( 'survey' )}</td>
    </tr>
    </table>
    </td>
</tr>

<tr>
    <td valign="top" colspan="2">
    <label>{'Valid to'|i18n('survey')}</label>
    <table border="0">
    <tr><td>{'Year'|i18n( 'survey' )}</td><td>{'Month'|i18n( 'survey' )}</td><td>{'Day'|i18n( 'survey' )}</td><td>{'Hour'|i18n( 'survey' )}</td><td>{'Minute'|i18n( 'survey' )}</td></tr>
    <tr>
      <td><input name="SurveyValidToYear" size="5" value="{$survey.valid_to_array.year}" /></td>
      <td><input name="SurveyValidToMonth" size="3" value="{$survey.valid_to_array.month}" /></td>
      <td><input name="SurveyValidToDay" size="3" value="{$survey.valid_to_array.day}" /></td>
      <td><input name="SurveyValidToHour" size="3" value="{$survey.valid_to_array.hour}" /></td>
      <td><input name="SurveyValidToMinute" size="3" value="{$survey.valid_to_array.minute}" /></td>
    </tr>
    <tr>
      <td colspan="5"><input type="checkbox" name="SurveyValidToNoLimit" value="1" {section show=$survey.valid_to_array.no_limit}checked{/section} /> {'No limitation'|i18n( 'survey' )}</td>
    </tr>
    </table>
    </td>
</tr>

<tr>
    <td valign="top" colspan="2">
    {'After "Cancel" redirect to URL'|i18n('survey')} <input name="SurveyRedirectCancel" size="30" value="{$survey.redirect_cancel|wash('xhtml')}" />
    </td>
</tr>

<tr>
    <td valign="top" colspan="2">
    {'After "Submit" redirect to URL'|i18n('survey')} <input name="SurveyRedirectSubmit" size="30" value="{$survey.redirect_submit|wash('xhtml')}" />
    </td>
</tr>

<tr>
    <td valign="top" colspan="2">
    <input type="checkbox" name="SurveyPersistent" {section show=$survey.persistent|eq(1)}checked="checked"{/section} /> {'Persistent user input. ( Users will be able to edit survey later. )'|i18n('survey')}
    </td>
</tr>

<tr>
    <td>
    &nbsp;
    </td>
    <td align="center">
    <label>{'Order'|i18n( 'survey' )}</label>
    <br />
    <label>{'Selected'|i18n( 'survey' )}</label>
    </td>
</tr>

{section name=Question loop=$survey_questions sequence=array(bgdark,bglight)}
<tr class={$:sequence}>
    <td valign="top">
    <input type="hidden" name="SurveyQuestionList[]" value="{$:item.id}" />
    {survey_question_edit_gui question=$:item}
    </td>
    <td valign="top" align="center">
    <input type="checkbox" name="SurveyQuestionVisible_{$:item.id}" {section show=$:item.visible|eq(1)}checked="checked"{/section} />{"Visible"|i18n('survey')}
    <input type="input" size="2" name="SurveyQuestionTabOrder_{$:item.id}" value="{$:item.tab_order}" />&nbsp;
    <input type="image" name="SurveyQuestionCopy_{$:item.id}" src={"copy.gif"|ezimage} border="0" alt="{"Copy"|i18n('survey')}" title="{"Copy question"|i18n('survey')}" />
    {section show=$:item.can_be_selected}<input name="SurveyQuestion_{$:item.id}_Selected" type="checkbox" />{/section}
    </td>
</tr>
{/section}

<tr>
    <td colspan="2" valign="bottom" height="50">
    <select name="SurveyNewQuestionType">
    {section var=type loop=$survey.question_types}
        <option value="{$type.type}">{$type.name}</option>
    {/section}
    </select>
    <input class="button" type="submit" name="SurveyNewQuestion" value="{'Add question'|i18n( 'survey' )}" />
    &nbsp;&nbsp;
    <input class="button" type="submit" name="SurveyRemoveSelected" value="{'Remove selected'|i18n( 'survey' )}" />
    </td>
</tr>

<tr>
    <td colspan="2" valign="bottom" height="70">
    <div class="buttonblock">
    <input class="defaultbutton" type="submit" name="SurveyPublishButton" value="{'Publish'|i18n( 'survey' )}" />
    <input class="button" type="submit" name="SurveyApplyButton" value="{'Apply'|i18n( 'survey' )}"  />
    <input class="button" type="submit" name="SurveyPreviewButton" value="{'Apply and Preview'|i18n( 'survey' )}"  />
    <input class="button" type="submit" name="SurveyDiscardButton" value="{'Discard'|i18n( 'survey' )}" />
    </div>
    </td>
</tr>

</table>

</form>
