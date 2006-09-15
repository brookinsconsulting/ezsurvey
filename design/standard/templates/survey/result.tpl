<h1>{$survey.title}</h1>

{section show=$count|gt(0)}
<ul>
<li><a href={concat('/survey/result_list/',$survey.id)|ezurl}>All evaluations</a></li>
<li><a href={concat('/survey/export/',$survey.id)|ezurl}>Export CSV</a></li>
<li>Summary</li>
</ul>

{section var=question loop=$survey_questions}
<div class="block">
{survey_question_result_gui view=overview question=$question metadata=$survey_metadata}
</div>
{/section}
{section-else}
{"No results yet."|i18n('survey')}
{/section}
