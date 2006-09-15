<div class="left-menu">&nbsp;</div>
<div class="survey">
<form enctype="multipart/form-data" method="post" action={concat("survey/view/",$survey.id)|ezurl}>
<input type="hidden" name="SurveyID" value="{$survey.id}" />
<h4>{$survey.title|wash}</h4>
{"Questions marked with %mark% are required."|i18n('survey', '', hash( '%mark%', '<strong>*</strong>' ) )}

{section show=$preview|not}
	{include uri="design:survey/view_validation.tpl"}
{/section}

{let question_results=$survey.question_results}
{section show=$question_results}

  {section var=question loop=$survey.questions}
    {section show=$question.visible}
      <div class="survey-block">
      <input type="hidden" name="SurveyQuestionList[]" value="{$question.id}" />
      {survey_question_view_gui question=$question question_result=$question_results[$question.id]}
      <br/>
      </div>
    {/section}
  {/section}
{section-else}

  {section var=question loop=$survey.questions}
    {section show=$question.visible}
      <div class="survey-block">
      <input type="hidden" name="SurveyQuestionList[]" value="{$question.id}" />
      {survey_question_view_gui question=$question question_result=0}
      <br/>
      </div>
    {/section}
  {/section}

{/section}
{/let}
<div class="block-end" ><img id="right" src={"end2.jpg"|ezimage} alt="" /></div>
<div class="end-button">
<br/>
{section show=$preview}
</form>
<form enctype="multipart/form-data" method="post" action={concat("survey/edit/",$survey.id)|ezurl}>
<input type="image" src={"cancel.gif"|ezimage} name="SurveyBackButton" value="{'Back'|i18n( 'survey' )}" alt="" onmouseover='javascript:this.src = {"cancel_ro.gif"|ezimage}' onmouseout='javascript:this.src = {"cancel.gif"|ezimage}' />
{section-else}
	<input type="image" src={"send.gif"|ezimage} name="SurveyStoreButton" value="{'Submit'|i18n( 'survey' )}" alt="Send" onmouseover='javascript:this.src = {"send_ro.gif"|ezimage}' onmouseout='javascript:this.src = {"send.gif"|ezimage}' />
	<input type="image" src={"cancel.gif"|ezimage} name="SurveyCancelButton" value="{'Cancel'|i18n( 'survey' )}" alt="Cancel" onmouseover='javascript:this.src = {"cancel_ro.gif"|ezimage}' onmouseout='javascript:this.src = {"cancel.gif"|ezimage}' />
{/section}
</div>

</form>
</div>
