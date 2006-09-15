<label>{$question.question_number}.
{$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}</label><div class="labelbreak"></div>
{section show=$question_result}
  <input class="box" name="SurveyAnswer_{$question.id}" type="text" size="20" value="{$question_result.text|wash('xhtml')}" />
{section-else}
  <input class="box" name="SurveyAnswer_{$question.id}" type="text" size="20" value="{$question.answer|wash('xhtml')}" />
{/section}