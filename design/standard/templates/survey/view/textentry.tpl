<label>{$question.question_number}. {$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}</label><div class="labelbreak"></div>

{switch match=$question.num2}
{case match=1}
{section show=$question_result}
  <input name="SurveyAnswer_{$question.id}" type="text" size="{$question.num}" value="{$question_result.text|wash('xhtml')}" />
{section-else}
  <input name="SurveyAnswer_{$question.id}" type="text" size="{$question.num}" value="{$question.answer|wash('xhtml')}" />
{/section}
{/case}
{case}
{section show=$question_result}
  <textarea name="SurveyAnswer_{$question.id}" rows="{$question.num2}" cols="{$question.num}">{$question_result.text|wash('xhtml')}</textarea>
{section-else}
  <textarea name="SurveyAnswer_{$question.id}" rows="{$question.num2}" cols="{$question.num}">{$question.answer|wash('xhtml')}</textarea>
{/section}
{/case}
{/switch}
