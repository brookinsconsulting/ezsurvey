<?php
//
// Created on: <28-Jun-2004 15:00:00 kk>
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

/*! \file result_list.php
*/

include_once( 'kernel/common/template.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurvey.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyresult.php' );

$http =& eZHTTPTool::instance();
$surveyID =& $Params['SurveyID'];
$offset =& $Params['Offset'];
if ( !$offset )
    $offset = 0;
$limit = 15;
$viewParameters['offset'] = $offset;

if ( $http->hasPostVariable( 'RemoveButton' ) )
{
    foreach( $http->postVariable( 'DeleteIDArray' ) as $resultID )
    {
        $surveyResult =& eZSurveyResult::fetch( $resultID );
        $surveyResult->remove();
    }
}

$resultList =& eZSurveyResult::fetchResultArray( $surveyID, $offset, $limit );
$survey =& eZSurvey::fetch( $surveyID );

$tpl =& templateInit();

$tpl->setVariable( 'result_list', $resultList );
$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );

$Result = array();
$Result['content'] =& $tpl->fetch( 'design:survey/result_list.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezi18n( 'survey', 'Survey' ) ),
                         array( 'url' => '/survey/result/' . $survey->attribute( 'id' ),
                                'text' => ezi18n( 'survey', 'Result' ) ),
                         array( 'url' => false,
                                'text' => ezi18n( 'survey', 'All' ) ) );


?>
