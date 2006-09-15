<label>{$question.question_number}. {$question.text|wash('xhtml')} {switch match=$question.num}{case match=1}*{/case}{case match=2}*{/case}{/switch} </label><div class="labelbreak"></div>

{section show=$question_result}

{switch match=$question.num}
{case match=1}
{section var=option loop=$question.options}
    <div class="element"><label><input name="SurveyAnswer_{$question.id}" type="radio" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section}>{$option.label}</label></div>
{/section}
{/case}
{case match=2}
{section var=option loop=$question.options}
    <label><input name="SurveyAnswer_{$question.id}" type="radio" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section}>{$option.label}</label><div class="labelbreak"></div>
{/section}
{/case}
{case match=3}
{section var=option loop=$question.options}
    <div class="element"><label><input name="SurveyAnswer_{$question.id}[]" type="checkbox" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section}>{$option.label}</label></div>
{/section}
{/case}
{case match=4}
{section var=option loop=$question.options}
    <label><input name="SurveyAnswer_{$question.id}[]" type="checkbox" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section}>{$option.label}</label><div class="labelbreak"></div>
{/section}
{/case}
{case match=5}
<select name="SurveyAnswer_{$question.id}">
  {section var=option loop=$question.options}
    <option value="{$option.value}"{section show=is_set( $question_result[$option.value] )} selected="selected"{/section}>{$option.label}</option>
  {/section}
</select>
{/case}
{/switch}

{section-else}

{switch match=$question.num}
{case match=1}
{section var=option loop=$question.options}
    <div class="element"><label><input name="SurveyAnswer_{$question.id}" type="radio" value="{$option.value}"{section show=$option.toggled|eq(1)} checked{/section}>{$option.label}</label></div>
{/section}
{/case}
{case match=2}
{section var=option loop=$question.options}
    <label><input name="SurveyAnswer_{$question.id}" type="radio" value="{$option.value}"{section show=$option.toggled|eq(1)} checked{/section}>{$option.label}</label><div class="labelbreak"></div>
{/section}
{/case}
{case match=3}
{section var=option loop=$question.options}
    <div class="element"><label><input name="SurveyAnswer_{$question.id}[]" type="checkbox" value="{$option.value}"{section show=$option.toggled|eq(1)} checked{/section}>{$option.label}</label></div>
{/section}
{/case}
{case match=4}
{section var=option loop=$question.options}
    <label><input name="SurveyAnswer_{$question.id}[]" type="checkbox" value="{$option.value}"{section show=$option.toggled|eq(1)} checked{/section}>{$option.label}</label><div class="labelbreak"></div>
{/section}
{/case}
{case match=5}
<select name="SurveyAnswer_{$question.id}">
  {section var=option loop=$question.options}
    <option value="{$option.value}"{section show=$option.toggled|eq(1)} selected{/section}>{$option.label}</option>
  {/section}
</select>
{/case}
{/switch}

{/section}
