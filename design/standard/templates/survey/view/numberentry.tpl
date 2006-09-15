<label class="intro" for="SurveyAnswer_{$question.id}">{$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}&nbsp;</label>
<div class="input">
{section show=$question_result}
  <input class="text" id="SurveyAnswer_{$question.id}" name="SurveyAnswer_{$question.id}" type="text" value="{$question_result.text|number($question.num)|wash('xhtml')}" />
{section-else}
  <input class="text" id="SurveyAnswer_{$question.id}" name="SurveyAnswer_{$question.id}" type="text" value="{$question.answer|number($question.num)|wash('xhtml')}" />
{/section}
</div>
