<label class="intro" for="SurveyAnswer_question_result_{$question.id}">{$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}&nbsp;</label>
<div class="input">
{section show=$question_result}
  <input id="SurveyAnswer_question_result_{$question.id}" class="text" name="SurveyAnswer_{$question.id}" type="text" value="{$question_result.text|wash('xhtml')}" />
{section-else}
  <input id="SurveyAnswer_question_result_{$question.id}" class="text" name="SurveyAnswer_{$question.id}" type="text" value="{$question.answer|wash('xhtml')}" />
{/section}
</div>
