<div class="left-menu">&nbsp;</div>
<div class="survey">
<form enctype="multipart/form-data" method="post" action={concat("survey/view/",$survey.id)|ezurl}>
<input type="hidden" name="SurveyID" value="{$survey.id}" />
<h1>{$survey.title|wash}</h1>
<p>{"Questions marked with %mark% are required."|i18n('survey', '', hash( '%mark%', '<span class="required">*</span>' ) )}</p>

{section show=$preview|not}
	{include uri="design:survey/view_validation.tpl"}
{/section}

{let question_results=$survey.question_results}
{section show=$question_results}

  {section var=question loop=$survey.questions}
    {section show=$question.visible}
      <div class="block">
      <input type="hidden" name="SurveyQuestionList[]" value="{$question.id}" />
      {survey_question_view_gui question=$question question_result=$question_results[$question.id]}
      </div>
    {/section}
  {/section}
{section-else}

  {section var=question loop=$survey.questions}
    {section show=$question.visible}
      <div class="block">
      <input type="hidden" name="SurveyQuestionList[]" value="{$question.id}" />
      {survey_question_view_gui question=$question question_result=0}
      </div>
    {/section}
  {/section}

{/section}
{/let}

<div class="block">

{section show=$preview}
</form>
<form enctype="multipart/form-data" method="post" action={concat("survey/edit/",$survey.id)|ezurl}>
<input class="button" type="submit" name="SurveyBackButton" value="{'Back'|i18n( 'survey' )}" alt="" />
{section-else}
	<input class="button" type="submit" name="SurveyStoreButton" value="{'Submit'|i18n( 'survey' )}" alt="Send" />
	<input class="button" type="submit" name="SurveyCancelButton" value="{'Cancel'|i18n( 'survey' )}" alt="Cancel" />
{/section}
</div>

</form>
</div>
