<h2>{"Paragraph"|i18n( 'survey' )} (id {$question.id})</h2>

<div class="block">
<label>{"Text of paragraph"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<textarea name="SurveyQuestion_{$question.id}_Text" cols="70" rows="5" >{$question.text|wash('xhtml')}</textarea>
</div>
