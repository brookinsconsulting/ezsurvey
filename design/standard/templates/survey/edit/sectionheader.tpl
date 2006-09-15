<h2>{"Section header"|i18n( 'survey' )} (id {$question.id})</h2>

<div class="block">
<label>{"Text of header"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<input type="text" name="SurveyQuestion_{$question.id}_Text" value="{$question.text|wash('xhtml')}" size="70" />
</div>
