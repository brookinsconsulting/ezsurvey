{include uri='design:infocollection_validation.tpl'}
{if ne($content_template,'design:survey/list.tpl')}
{include uri='design:window_controls.tpl'}
{/if}
<div class="content-navigation">

{* Content window. *}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

{let hide_status=""}
{section show=$node.is_invisible}
{set hide_status=concat( '(', $node.hidden_status_string, ')' )}
{/section}
<h1 class="context-title"><a href={concat( '/class/view/', $node.object.contentclass_id )|ezurl} onclick="ezpopmenu_showTopLevel( event, 'ClassMenu', ez_createAArray( new Array( '%classID%', {$node.object.contentclass_id}, '%objectID%', {$node.contentobject_id}, '%nodeID%', {$node.node_id}, '%currentURL%', '{$node.url|wash( javascript )}' ) ), '{$node.class_name|wash(javascript)}', 20 ); return false;">{$node.class_identifier|class_icon( normal, $node.class_name )}</a>&nbsp;{$node.name|wash}&nbsp;[{$node.class_name|wash}]&nbsp;{$hide_status}</h1>
{/let}
{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr">

<div class="context-information">
<p class="modified">{'Last modified'|i18n( 'design/admin/node/view/full' )}: {$node.object.modified|l10n(shortdatetime)}, <a href={$node.object.current.creator.main_node.url_alias|ezurl}>{$node.object.current.creator.name|wash}</a></p>
<p class="translation">{$language_code|locale().intl_language_name}&nbsp;<img src="{$language_code|flag_icon}" alt="{$language_code}" style="vertical-align: middle;" /></p>
<div class="break"></div>
</div>

{* Content preview in content window. *}
{section show=and(ezpreference( 'admin_navigation_content'),eq($node.object.class_identifier,'survey'))}
<div class="mainobject-window" title="{$node.name|wash} {'Node ID'|i18n( 'design/admin/node/view/full' )}: {$node.node_id}, {'Object ID'|i18n( 'design/admin/node/view/full' )}: {$node.object.id}">
<div class="fixedsize">{* Fix for overflow bug in Opera *}
<div class="holdinplace">{* Fix for some width bugs in IE *}
{* start of survey content *}
{include uri=$content_template survey=$survey}
{* end of survey content *}
</div>
</div>
<div class="break"></div>{* Terminate overflow bug fix *}
</div>
{/section}

</div></div>

{* Buttonbar for content window. *}
<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<input type="hidden" name="TopLevelNode" value="{$node.object.main_node_id}" />
<input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
<input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
<input type="hidden" name="ContentObjectLanguageCode" value="{$language_code}" />

<div class="block">

<div class="left">
{if eq($node.object.class_identifier,'survey')}
{* Edit button. *}
{section show=$node.can_edit}
<input class="button" type="button" name="EditButton" value="{'Edit'|i18n( 'Survey' )}" onclick='window.location={concat("survey/edit/",$survey.id)|ezurl}' />
{section-else}
    <input class="button-disabled" type="button" name="EditButton" value="{'Edit'|i18n( 'survey' )}" disabled="disabled" />
{/section}
{/if}

{if eq($node.object.class_identifier,'survey')}
{* Copy button. *}
<input class="button" type="button" name="EditButton" value="{'Copy'|i18n( 'Survey' )}" onclick='window.location={concat("survey/copy/",$survey.id)|ezurl}' />
{/if}

{if eq($node.object.class_identifier,'survey')}
{* Copy button. *}
<input class="button" type="button" name="EditButton" value="{'Results'|i18n( 'Survey' )}" onclick='window.location={concat("survey/result/",$survey.id)|ezurl}' />
{/if}

{* Remove button. *}
{if eq($node.object.class_identifier,'survey')}
{section show=$node.can_remove}
    <input class="button" type="button" name="ActionRemove" value="{'Remove'|i18n( 'Survey' )}" onclick='window.location={concat("survey/remove/",$survey.id)|ezurl}' />
{section-else}
    <input class="button-disabled" type="button" name="ActionRemove" value="{'Remove'|i18n( 'Survey' )}" disabled="disabled" />
{/section}
{/if}
</div>

{* Custom content action buttons. *}
<div class="right">
{section var=ContentActions loop=$node.object.content_action_list}
    <input class="button" type="submit" name="{$ContentActions.item.action}" value="{$ContentActions.item.name}" />
{/section}
</div>

{* The preview button has been commented out. Might be absent until better preview functionality is implemented. *}
{* <input class="button" type="submit" name="ActionPreview" value="{'Preview'|i18n('design/admin/node/view/full')}" /> *}

<div class="break"></div>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

</div>

{include uri="design:survey/windows.tpl"}

</div>
