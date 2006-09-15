<div class="survey">
<form enctype="multipart/form-data" method="post" action={concat("survey/result_edit/",$survey_result.id)|ezurl}>

<input type="hidden" name="SurveyID" value="{$survey.id}" />

<h1>{$survey.title|wash}</h1>

{include uri="design:survey/view_validation.tpl"}

{let question_results=$survey_result.question_results}

  {section var=question loop=$survey.questions}
    <div class="block">
    <input type="hidden" name="SurveyQuestionList[]" value="{$question.id}" />
    {section show=is_set( $question_results[$question.id] )}
      {survey_question_view_gui question=$question question_result=$question_results[$question.id]}
    {section-else}
      {survey_question_view_gui question=$question}
    {/section}
    <div class="break"></div>
    </div>
  {/section}

{/let}

<div class="buttonblock">
<input class="defaultbutton" type="submit" name="SurveyStoreButton" value="{'Submit'|i18n( 'survey' )}" />
<input class="button" type="submit" name="SurveyCancelButton" value="{'Cancel'|i18n( 'survey' )}" />
</div>

</form>
</div>
