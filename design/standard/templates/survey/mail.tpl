{set-block scope=root variable=subject}Filled Survey{/set-block}
The following information was collected as the result of the survey '{$survey.title}':


{section var=question loop=$survey_questions}
{survey_question_result_gui view=mail question=$question}


{/section}
