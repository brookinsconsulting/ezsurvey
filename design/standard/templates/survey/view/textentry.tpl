<label class="intro" for="SurveyAnswer_{$question.id}">{$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}&nbsp;</label>
<div class="input">
{switch match=$question.num2}
{case match=1}
{section show=$question_result}
  <input class="text" id="SurveyAnswer_{$question.id}" name="SurveyAnswer_{$question.id}" type="text" value="{$question_result.text|wash('xhtml')}" />
{section-else}
  <input class="text" id="SurveyAnswer_{$question.id}" name="SurveyAnswer_{$question.id}" type="text" value="{$question.answer|wash('xhtml')}" />
{/section}
{/case}
{case}
{section show=$question_result}
  <textarea id="SurveyAnswer_{$question.id}" name="SurveyAnswer_{$question.id}" rows="{$question.num2}" cols="{$question.num}">{$question_result.text|wash('xhtml')}</textarea>
{section-else}
  <textarea id="SurveyAnswer_{$question.id}" name="SurveyAnswer_{$question.id}" rows="{$question.num2}" cols="{$question.num}">{$question.answer|wash('xhtml')}</textarea>
{/section}
{/case}
{/switch}
</div>
