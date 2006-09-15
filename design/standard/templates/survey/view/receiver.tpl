<input type="hidden" name="SurveyReceiverID" value="{$question.id}" />
{section show=$question.options|count|gt(1)}
<label>{$question.question_number}. {$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}</label><div class="labelbreak"></div>
<select name="SurveyAnswer_{$question.id}">
  {section var=option loop=$question.options}
    <option value="{$option.id}"{section show=$option.toggled|eq(1)} selected{/section}>{$option.label}</option>
  {/section}
</select>
{/section}
