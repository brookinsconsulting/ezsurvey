<label class="intro">{$question.text|wash('xhtml')} {switch match=$question.num}{case match=1}*{/case}{case match=2}*{/case}{/switch}&nbsp;</label>
<div class="input">
{section show=$question_result}

{switch match=$question.num}
	{case match=1}
		{section var=option loop=$question.options}
			<div class="child">
				<input class="radio" id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" name="SurveyAnswer_{$question.id}" type="radio" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section} />
				<label for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" class="txt">{$option.label}</label>
			</div>
		{/section}
	{/case}
	{case match=2}
		{section var=option loop=$question.options}
			<div class="child">
				<input class="radio" id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" name="SurveyAnswer_{$question.id}" type="radio" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section} />
				<label for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" class="txt">{$option.label}</label>
			</div>
		{/section}
	{/case}
	{case match=3}
		{section var=option loop=$question.options}
			<div class="child">
				<input id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" class="checkbox" name="SurveyAnswer_{$question.id}[]" type="checkbox" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section} />
				<label for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" class="txt">{$option.label}</label>
			</div>
		{/section}
	{/case}
	{case match=4}
	{section var=option loop=$question.options}
		<div class="child">
			<input id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" class="checkbox" name="SurveyAnswer_{$question.id}[]" type="checkbox" value="{$option.value}"{section show=is_set( $question_result[$option.value] )} checked="checked"{/section} />
			<label for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" class="txt">{$option.label}</label>
		</div>
	{/section}
	{/case}
	{case match=5}
		<select name="SurveyAnswer_{$question.id}" id="SurveyAnswer_{$question.id}" >
		  {section var=option loop=$question.options}
			<option value="{$option.value}"{section show=is_set( $question_result[$option.value] )} selected="selected"{/section}>{$option.label}</option>
		  {/section}
		</select>
	{/case}
{/switch}

{section-else}

{switch match=$question.num}
	{case match=1}
        <div class="child">
	{section var=option loop=$question.options}
        <input name="SurveyAnswer_{$question.id}" id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" type="radio" value="{$option.value}" {section show=$option.toggled|eq(1)} checked{/section} style="float:left;margin-right:10px;"/>
	<label for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" style="float:left;margin-right:10px;">{$option.label}</label>
	{/section}
        </div>
	{/case}
	{case match=2}
	{section var=option loop=$question.options}
		<div class="child">
			<input name="SurveyAnswer_{$question.id}" id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" type="radio" value="{$option.value}"{section show=$option.toggled|eq(1)} checked{/section} />
			<label class="txt" for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}">{$option.label}</label>
		</div>
	{/section}
	{/case}
	{case match=3}
	<div class="child">
	{section var=option loop=$question.options}
	<input name="SurveyAnswer_{$question.id}[]" id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" type="checkbox" value="{$option.value}"{section show=$option.toggled|eq(1)} checked{/section} style="float:left;margin-right:10px;" />
			<label for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" style="float:left;margin-right:10px;" >{$option.label}</label>
	{/section}
	</div>
	{/case}
	{case match=4}
	{section var=option loop=$question.options}
		<div class="child">
			<input name="SurveyAnswer_{$question.id}[]" id="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" type="checkbox" value="{$option.value}"{section show=$option.toggled|eq(1)} checked{/section} />
			<label class="txt" for="SurveyAnswer_{$question.id}_{$option.id}_{$option.value}" >{$option.label}</label>
		</div>
	{/section}
	{/case}
	{case match=5}
	<select name="SurveyAnswer_{$question.id}" >
	  {section var=option loop=$question.options}
		<option value="{$option.value}"{section show=$option.toggled|eq(1)} selected{/section}>{$option.label}</option>
	  {/section}
	</select>
	{/case}
{/switch}

{/section}
</div>
