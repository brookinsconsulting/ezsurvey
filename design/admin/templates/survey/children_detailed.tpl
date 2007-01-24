{def $child_survey = false()}
<div class="content-navigation-childlist">
    <table class="list" cellspacing="0">
    <tr>
        {* ID column *}
        <th class="remove">ID</th>

        {* Survey Title column *}
        <th class="name">{'Survey Title'|i18n( 'design/admin/node/view/full' )}</th>

        {* Enabled column *}
        <th class="hidden_invisible">{'Enabled'|i18n( 'design/admin/node/view/full' )}</th>

        {* Publish column *}
        <th class="class">{'Published'|i18n( 'design/admin/node/view/full' )}</th>

        {* Validity column *}
        <th class="modifier">{'Validity'|i18n( 'design/admin/node/view/full' )}</th>

        {* Priority column *}
        {section show=eq( $node.sort_array[0][0], 'priority' )}
            <th class="priority">{'Priority'|i18n( 'design/admin/node/view/full' )}</th>
        {/section}

        {* Edit column *}
        <th class="modified">&nbsp;</th>

        {* Copy column *}
        <th class="copy">&nbsp;</th>

        {* Results column *}
        <th class="move">&nbsp;</th>

        {* Remove column *}
        <th class="edit">&nbsp;</th>
    </tr>

    {section var=Nodes loop=$children sequence=array( bglight, bgdark )}
    {let child_name=$Nodes.item.name|wash
         node_name=$node.name}

        <tr class="{$Nodes.sequence}">

        {* Survey ID *}
        <td>{$Nodes.item.object.data_map.survey_number.data_int}</td>

        {* Survey Title *}
        {def $child_survey=fetch('survey','survey',hash('id',$Nodes.item.data_map.survey_number.data_int))}
        
        <td>{include uri="design:survey/line.tpl"}</td>

        {* Enabled *}
        <td class="nowrap">
        {switch match=$child_survey.enabled}
        {case match=0}{"not enabled"|i18n('survey')}{/case}
        {case match=1}{"enabled"|i18n('survey')}{/case}
        {/switch}
        </td>

        {* Published *}
        <td class="class">
        {switch match=$child_survey.published}
        {case match=0}{"not published"|i18n('survey')}{/case}
        {case match=1}{"published"|i18n('survey')}{/case}
        {/switch}
        </td>

        {* Validity *}
        <td class="modifier">
        {switch match=$child_survey.valid}
        {case match=0}{"not valid"|i18n('survey')}{/case}
        {case match=1}{"valid"|i18n('survey')}{/case}
        {/switch}
        </td>

        {* Priority *}
        {section show=eq( $node.sort_array[0][0], 'priority' )}
            <td>
            {section show=$node.can_edit}
                <input class="priority" type="text" name="Priority[]" size="3" value="{$child_survey_node.priority}" title="{'Use the priority fields to control the order in which the items appear. Use positive and negative integers. Click the "Update priorities" button to apply the changes.'|i18n( 'design/admin/node/view/full' )|wash}" />
                <input type="hidden" name="PriorityID[]" value="{$Nodes.item.node_id}" />
                {section-else}
                <input class="priority" type="text" name="Priority[]" size="3" value="{$child_survey_node.priority}" title="{'You are not allowed to update the priorities because you do not have permissions to edit <%node_name>.'|i18n( 'design/admin/node/view/full',, hash( '%node_name', $node_name ) )|wash}" disabled="disabled" />
            {/section}
            </td>
        {/section}

        {* Edit button *}
        <td>
        {section show=$Nodes.item.can_edit}
            <a href={concat( 'survey/edit/', $child_survey.id )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'survey' )}" title="{'Edit'|i18n('survey')|wash}" /></a>
        {section-else}
            <img src={'edit-disabled.gif'|ezimage} alt="{'Edit'|i18n( 'survey' )}" /></a>
        {/section}
        </td>

    {* Copy button *}
    <td><a href={concat( 'survey/copy/', $child_survey.id )|ezurl}><img src={'copy.gif'|ezimage} alt="{'Copy'|i18n( 'survey' )}" title="{'Copy'|i18n('survey')|wash}" /></a></td>

    {* Results button. *}
    <td>
    <a href={concat( 'survey/result/', $child_survey.id )|ezurl}><img src={'attach.png'|ezimage} alt="{'Results'|i18n( 'survey' )}" title="{'Results.'|i18n('survey')|wash}" /></a>
    </td>

    {* Remove button *}
    <td>
        {section show=$Nodes.item.can_remove}
            <a href={concat( 'survey/remove/', $child_survey.id )|ezurl}><img src={'trash.png'|ezimage} alt="{'Remove'|i18n( 'survey' )}" title="{'Remove'|i18n( 'Survey')|wash}" /></a>
        {section-else}
            <img src={'transh.png'|ezimage} alt="{'Remove'|i18n( 'survey' )}"/>
        {/section}
    </td>
  </tr>

{/let}
{/section}

</table>
</div>
