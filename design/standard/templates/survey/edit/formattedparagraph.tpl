<h2>{"Formatted Paragraph"|i18n( 'survey' )} (id {$question.id})</h2>

<div class="block">
<label>{"Text of paragraph"|i18n( 'survey' )}</label><div class="labelbreak"></div>
<textarea name="SurveyQuestion_{$question.id}_Text" cols="70" rows="5">{$question.content|wash('xhtml')}</textarea>
</div>

<div class="block">
{$question.content}
</div>

{*

Code from eZXMLText content attribute

{$attribute.content.output.output_text}

{default input_handler=$attribute.content.input
         attribute_base='ContentObjectAttribute'}
  <textarea class="box" name="{$attribute_base}_data_text_{$attribute.id}" cols="97" rows="{$attribute.contentclass_attribute.data_int1}">{$input_handler.input_xml|wash(xhtml)}</textarea>
{/default}
*}
