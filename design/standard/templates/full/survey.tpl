<div class="survey">
<h3>{$node.data_map.survey_name.data_text|wash(xhtml)}</h3>
{def $survey=fetch( 'survey', 'survey', hash( 'id', $node.object.data_map.survey_number.data_int ) )}
{section show=$survey}
{include uri="design:survey/full.tpl" survey=$survey content_template="design:survey/view_embed.tpl"}
{/section}
{undef}
</div> 
