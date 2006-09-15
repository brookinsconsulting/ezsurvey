<h2>{"Number entry"|i18n( 'survey' )} (id {$question.id})</h2>

<div class="block">
<label>{"Text of question"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<input type="text" name="SurveyQuestion_{$question.id}_Text" value="{$question.text|wash('xhtml')}" size="70" />
<br/>

<input type="hidden" name="SurveyQuestion_{$question.id}_Mandatory_Hidden" value="1" />
<input type="checkbox" name="SurveyQuestion_{$question.id}_Mandatory" value="1" {section show=$question.mandatory}checked{/section} />
<label>{"Mandatory answer"|i18n( 'survey' )}</label>
<br/>

<input type="hidden" name="SurveyQuestion_{$question.id}_Num_Hidden" value="1" />
<input type="checkbox" name="SurveyQuestion_{$question.id}_Num" value="1" {section show=$question.num|eq(1)}checked{/section} />
<label>{"Integer values only"|i18n( 'survey' )}</label>
<br/>

<label>{"Minimum value"|i18n( 'survey' )}</label>
<input type="text" size="20" name="SurveyQuestion_{$question.id}_Text2" value="{$question.text2|number($question.num)|wash('xhtml')}" />
<br/>

<label>{"Maximum value"|i18n( 'survey' )}</label>
<input type="text" size="20" name="SurveyQuestion_{$question.id}_Text3" value="{$question.text3|number($question.num)|wash('xhtml')}" />
<br/>

<label>{"Default answer"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<input type="text" size="20" name="SurveyQuestion_{$question.id}_Default" value="{$question.default_value|number($question.num)|wash('xhtml')}" />
</div>
