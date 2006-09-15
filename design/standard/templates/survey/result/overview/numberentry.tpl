{default te_limit=5}
{$question.question_number}. {$question.text|wash('xhtml')}<br />

<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td width="50">&nbsp;</td>
  <td>
  {"Last answers"|i18n( 'survey' )}
  <ul>
  {let results=fetch('survey','text_entry_result',hash( 'question', $question, 'metadata', $metadata, 'limit', $te_limit ))}
  {section var=result loop=$results}
    <li>{$result.value|number($question.num)}</li>
  {/section}
  {/let}
  </ul>
  </td>
</tr>
</table>
<br />
{/default}
