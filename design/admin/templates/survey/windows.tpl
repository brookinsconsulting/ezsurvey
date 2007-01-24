{literal}<script type="text/javascript">
function toggleLayer(whichLayer)
{
if (document.getElementById)
{
// this is the way the standards work
var style2 = document.getElementById(whichLayer).style;
style2.display = style2.display? "":"block";
}
else if (document.all)
{
// this is the way old msie versions work
var style2 = document.all[whichLayer].style;
style2.display = style2.display? "":"block";
}
else if (document.layers)
{
// this is the way nn4 works
var style2 = document.layers[whichLayer].style;
style2.display = style2.display? "":"block";
}
}
</script>
{/literal}
{* Children window.*}
{section show=$node.object.content_class.is_container}
    {include uri='design:survey/children.tpl'}
{section-else}
    {include uri='design:no_children.tpl'}
{/section}

<a href="javascript:toggleLayer('surveys-node-info');" title="show object details">+</a> 
<div id="surveys-node-info" style="display: none;">

    {include uri='design:details.tpl'}

    {include uri='design:translations.tpl'}



    {include uri='design:locations.tpl'}

    {include uri='design:relations.tpl'}

</div>
