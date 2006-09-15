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

/*! \file ezsurveyemailentry.php
*/

include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyentry.php' );
include_once( 'lib/ezutils/classes/ezmail.php' );

class eZSurveyEmailEntry extends eZSurveyEntry
{
    function eZSurveyEmailEntry( $row = false )
    {
        if ( !isset( $row['mandatory'] ) )
            $row['mandatory'] = 1;
        $row['type'] = 'EmailEntry';
        $this->eZSurveyEntry( $row );
    }

    function processViewActions( &$validation )
    {
        $http =& eZHTTPTool::instance();

        $answer = trim ( $http->postVariable( 'SurveyAnswer_'.$this->ID ) );
        if ( $this->attribute( 'mandatory' ) == 1 && strlen( $answer ) == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = ezi18n( 'survey', 'Please answer the question ( %question ) as well!', null,
	    array( '%number' => $this->questionNumber(),'%question'=>$this->attribute('text') ) );
        }
        else if ( strlen( $answer ) != 0 && !eZMail::validate( $answer ) )
        {
            $validation['error'] = true;
            $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question ( %question ) is not a valid email address!', null,
                                              array( '%number' => $this->questionNumber(),'%question'=>$this->attribute('text') ) );
        }
        else
            $this->setAnswer( $answer );
    }
}

eZSurveyQuestion::registerQuestionType( 'Email Entry', 'EmailEntry' );

?>
