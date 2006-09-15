<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2004 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file rview.php
*/

include_once( 'kernel/common/template.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurvey.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyresult.php' );

$Module =& $Params['Module'];
$surveyID =& $Params['SurveyID'];
$offset =& $Params['Offset'];
if ( !$offset )
    $offset = 0;
$limit = 1;
$viewParameters['offset'] = $offset;

$resultList =& eZSurveyResult::fetchResultArray( $surveyID, $offset, $limit );
if ( count( $resultList ) < 1 )
{
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}
$surveyResult =& $resultList[0];

$survey =& eZSurvey::fetch( $surveyID );
if ( !$survey )
{
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}

$surveyList =& $survey->fetchQuestionList();
$countList =& $survey->resultCount();

$tpl =& templateInit();

$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'survey_questions', $surveyList );
$tpl->setVariable( 'survey_metadata', array() );
$tpl->setVariable( 'result_id', $surveyResult->attribute( 'id' ) );
$tpl->setVariable( 'user_id', $surveyResult->attribute( 'user_id' ) );
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'count', $countList );

$Result = array();
$Result['content'] =& $tpl->fetch( 'design:survey/rview.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezi18n( 'survey', 'Survey' ) ),
                         array( 'url' => 'survey/result_list/' . $survey->attribute( 'id' ),
                                'text' => ezi18n( 'survey', 'Result' ) ),
                         array( 'url' => false,
                                'text' => ezi18n( 'survey', 'View' ) ) );
?>
