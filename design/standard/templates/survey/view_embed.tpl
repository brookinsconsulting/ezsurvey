<form enctype="multipart/form-data" method="post" action={concat("survey/view/",$survey.id)|ezurl}>
	<input type="hidden" name="SurveyID" value="{$survey.id}" />
	{section var=question loop=$survey.questions}
		<div class="survey-block">
			<input type="hidden" name="SurveyQuestionList[]" value="{$question.id}" />
			{survey_question_view_gui question=$question}
			<br/>
		</div>
	{/section}
{if ne($preview,"yes")}
	<div class="block-end" ><img id="right" src={"end2.jpg"|ezimage} alt="" /></div>
	<div class="end-button">
		<br/>
		<input type="image" src={"send.gif"|ezimage} name="SurveyStoreButton" value="{'Submit'|i18n( 'survey' )}" alt="Send" onmouseover='javascript:this.src = {"send_ro.gif"|ezimage}' onmouseout='javascript:this.src = {"send.gif"|ezimage}' />
		<input type="image" src={"cancel.gif"|ezimage} name="SurveyCancelButton" value="{'Cancel'|i18n( 'survey' )}" alt="Cancel" onmouseover='javascript:this.src = {"cancel_ro.gif"|ezimage}' onmouseout='javascript:this.src = {"cancel.gif"|ezimage}' />
	</div>
{/if}
</form>
