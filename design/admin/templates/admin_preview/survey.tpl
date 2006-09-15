{set-block scope=global variable=cache_ttl}0{/set-block}
{def $survey_full=fetch( 'survey', 'survey', hash( 'id', $node.object.data_map.survey_number.data_int ) )}
{def $view_template="design:survey/view_embed.tpl"}
{* Default preview template for admin interface. *}
{* Will be used if there is no suitable override for a specific class. *}
{* Display all the attributes using their default template. *}
{section show=$survey_full}
{include uri="design:survey/full.tpl" survey=$survey_full content_template=$view_template preview="yes"}
{/section}
{undef}
