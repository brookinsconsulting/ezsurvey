{set-block scope=global variable=cache_ttl}0{/set-block}
{* Folder - Admin preview *}
{* Default preview template for admin interface. *}
{* Will be used if there is no suitable override for a specific class. *}
{* Display all the attributes using their default template. *}
{def $surveys=fetch('survey','list')}
{include uri="design:survey/full.tpl" content_template="design:survey/list.tpl" survey_list=$surveys}
{undef}
