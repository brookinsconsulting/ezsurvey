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
$Module =& $Params['Module'];
$resultID =& $Params['ResultID'];

$surveyResult =& eZSurveyResult::fetch( $resultID );
if ( !$surveyResult )
{
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}

$survey =& eZSurvey::fetch( $surveyResult->attribute( 'survey_id' ) );
if ( !$survey )
{
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}

$validation = array();
$survey->processViewActions( $validation );

if ( $http->hasPostVariable( 'SurveyStoreButton' ) && $validation['error'] == false )
{
    $result = eZSurveyResult::instance( $surveyResult->attribute( 'survey_id' ),
                                        $surveyResult->attribute( 'user_id' ) );
    $result->storeResult();
    $Module->redirectTo( '/survey/result_list/' . $surveyResult->attribute( 'survey_id' ) );
}
else if ( $http->hasPostVariable( 'SurveyCancelButton' ) )
{
    $Module->redirectTo( '/survey/result_list/' . $surveyResult->attribute( 'survey_id' ) );
}

$tpl =& templateInit();

$tpl->setVariable( 'preview', false );

$tpl->setVariable( 'survey', $survey );

$tpl->setVariable( 'survey_result', $surveyResult );

$tpl->setVariable( 'survey_validation', $validation );

$ini =& eZINI::instance('ezsurvey.ini');

$path_text = $ini->variable( 'PathTextSettings', 'PathText' );

$path_node_id = $ini->variable('PathNodeIDSettings','PathNodeID');

$node = eZContentObjectTreeNode::fetch( $survey->attribute('node_id') );

$tpl->setVariable('node',$node);

$tpl->setVariable('content_template','design:survey/result_edit.tpl');

$tpl->setVariable('language_code',$node->CurrentLanguage);

$Result = array();

$Result['content'] =& $tpl->fetch( 'design:survey/full.tpl' );

$Result['path']=array();

for($i=0;$i<count($path_text);$i++){

         $Result['path'][$i]['text']=$path_text[$i];

}

$Result['path'][count($path_text)]['text']=$node->attribute('name');

for($i=0;$i<count($path_node_id);$i++){

        $Result['path'][$i]['node_id']=$path_node_id[$i];

}

$Result['path'][count($path_node_id)]['node_id']=$node->attribute('node_id');

?>
