<h1>{$survey.title}</h1>

<a href={concat("/survey/result_list/",$survey.id)|ezurl}>{"Back to the result overview"|i18n('survey')}</a><br />

{let user=fetch( 'content', 'object', hash( 'object_id', $user_id ))}
{"Participiant:"|i18n('survey')} {$user.name}<br/>
{/let}

{section var=question loop=$survey_questions}
<div class="block">
{survey_question_result_gui view=item question=$question result_id=$result_id metadata=$survey_metadata}
<br />
</div>
{/section}

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat('/survey/rview/',$survey.id)
         item_count=$count
         view_parameters=$view_parameters
         item_limit=$limit}
