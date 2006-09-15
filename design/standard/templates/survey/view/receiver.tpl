<input type="hidden" name="SurveyReceiverID" value="{$question.id}" />
{section show=$question.options|count|gt(1)}
	<label class="intro" for="SurveyAnswer_{$question.id}" >{$question.text|wash('xhtml')} {section show=$question.mandatory}*{/section}&nbsp;</label>
	<div class="input">
		<select name="SurveyAnswer_{$question.id}" id="SurveyAnswer_{$question.id}">
		  {section var=option loop=$question.options}
			<option value="{$option.id}"{section show=$option.toggled|eq(1)} selected{/section}>{$option.label}</option>
		  {/section}
		</select>
	</div>
{/section}
