{section show=and( is_set( $survey_validation ), or( $survey_validation.error, $survey_validation.warning ))}
<div class="warning">
<h2>{"Warning"|i18n( 'survey' )}</h2>
<ul>
{section var=error loop=$survey_validation.errors}
  <li>
  {$error}
  </li>
{/section}
{section var=warning loop=$survey_validation.warnings}
  <li>
  {$warning}
  </li>
{/section}
</ul>
</div>
<br/ >
{/section}
