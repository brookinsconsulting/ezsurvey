{$question.question_number}. {$question.text|wash('xhtml')}<br />

{let result=fetch('survey', 'multiple_choice_result_item', hash( 'question', $question, 'metadata', $metadata, 'result_id', $result_id ))}
<table border="0" cellspacing="1" cellpadding="0">
<tr>
  <td width="50">&nbsp;</td>
  <td>
  {section var=ans loop=$result}{$ans['value']|wash('xhtml')} {section show=$ans['label']|wash('xhtml')|count_chars}({$ans['label']|wash('xhtml')}){/section}{delimiter}, {/delimiter}{/section}
  </td>
</tr>
</table>
{/let}
<br />
