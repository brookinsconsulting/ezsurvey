<form enctype="multipart/form-data" method="post" action={concat("survey/view/",$survey.id)|ezurl}>
	<input type="hidden" name="SurveyID" value="{$survey.id}" />
	{section var=question loop=$survey.questions}
		<div class="block">
			<input type="hidden" name="SurveyQuestionList[]" value="{$question.id}" />
			{survey_question_view_gui question=$question}
		</div>
	{/section}
{if ne($preview,"yes")}
	<div class="block">
		<input class="button" type="submit" name="SurveyStoreButton" value="{'Submit'|i18n( 'survey' )}" alt="Send" />
		<input class="button" type="submit" name="SurveyCancelButton" value="{'Cancel'|i18n( 'survey' )}" alt="Cancel" />
	</div>
{/if}
</form>
