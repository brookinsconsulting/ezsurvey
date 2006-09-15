{* Folder - Admin preview *}
{def $surveys=fetch('survey','list')}
{include uri="design:survey/list.tpl" survey_list=$surveys}
