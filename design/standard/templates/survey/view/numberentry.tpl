<label>{$question.question_number}.
{$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}</label><div class="labelbreak"></div>
{section show=$question_result}
  <input size="10" name="SurveyAnswer_{$question.id}" type="text" value="{$question_result.text|number($question.num)|wash('xhtml')}" />
{section-else}
  <input size="10" name="SurveyAnswer_{$question.id}" type="text" value="{$question.answer|number($question.num)|wash('xhtml')}" />
{/section}
