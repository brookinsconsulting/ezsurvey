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

/*! \file view.php
*/

include_once( 'kernel/common/template.php' );
include_once( 'kernel/common/eztemplatedesignresource.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurvey.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyresult.php' );
include_once( 'lib/ezutils/classes/ezmail.php' );
include_once( 'lib/ezutils/classes/ezmailtransport.php' );


$http =& eZHTTPTool::instance();

$Module =& $Params['Module'];

$surveyID =& $Params['SurveyID'];

$survey = eZSurvey::fetch( $surveyID );

$current_site_access = $GLOBALS['eZCurrentAccess'];



if ( !$survey || !$survey->published() || !$survey->enabled() || !$survey->valid() )
{
    // survey not avialable do we need to display it for the backend?
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}
if ( $http->hasPostVariable( 'SurveyCancelButton' ) )
{

    if( $survey->attribute( 'redirect_cancel' ) )
    {
       return $Module->redirectTo( $survey->attribute( 'redirect_cancel' ) );
    }
    else
    {
       return $Module->redirectTo('survey/list');
    }
}

$validation = array();

$survey->processViewActions( $validation );

if ( $http->hasPostVariable( 'SurveyStoreButton' ) && $validation['error'] == false )
{
    $user =& eZUser::currentUser();
    if ( $survey->attribute( 'persistent' ) )
    {
        $result = eZSurveyResult::instance( $surveyID, $user->id() );
    }
    else
    {
        $result = eZSurveyResult::instance( $surveyID );
    }

    $result->setAttribute( 'user_id', $user->id() );
    $result->storeResult();

    if ( $http->hasPostVariable( 'SurveyReceiverID' ) )
    {
        $surveyList = $survey->fetchQuestionList();
        $mailTo = $surveyList[$http->postVariable( 'SurveyReceiverID' )]->answer();

        $tpl_email =& templateInit();

        $tpl_email->setVariable( 'survey', $survey );
        $tpl_email->setVariable( 'survey_questions', $surveyList );

        $templateResult =& $tpl_email->fetch( 'design:survey/mail.tpl' );
        $subject =& $tpl_email->variable( 'subject' );
        $mail = new eZMail();
        $ini =& eZINI::instance();
        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSenderText( $emailSender );
        $mail->setReceiver( $mailTo );
        $mail->setSubject( $subject );
        $mail->setBody( $templateResult );

        $mailResult = eZMailTransport::send( $mail );
    }

    if( $survey->attribute( 'redirect_submit' ) ){
       return $Module->redirectTo( $survey->attribute( 'redirect_submit' ) );
    }else{
       return $Module->redirectTo('survey/result/'.$surveyID);
    }
}

$res =& eZTemplateDesignResource::instance();

$res->setKeys( array( array( 'survey', $surveyID ) ) );

$tpl =& templateInit();

$tpl->setVariable( 'preview', false );

$tpl->setVariable( 'survey', $survey );

$tpl->setVariable( 'survey_validation', $validation );

$ini =& eZINI::instance('ezsurvey.ini');

$path_text = $ini->variable( 'PathTextSettings', 'PathText' );

$path_node_id = $ini->variable('PathNodeIDSettings','PathNodeID');

$node = eZContentObjectTreeNode::fetch( $survey->attribute('node_id'));

$tpl->setVariable('node',$node);

$tpl->setVariable('content_template','design:survey/view.tpl');

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
