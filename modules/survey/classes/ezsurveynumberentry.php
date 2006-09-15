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

/*! \file ezsurveytextentry.php
*/

include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyentry.php' );
include_once( 'lib/ezlocale/classes/ezlocale.php' );

include_once( 'lib/ezutils/classes/ezfloatvalidator.php' );
include_once( 'lib/ezutils/classes/ezintegervalidator.php' );

class eZSurveyNumberEntry extends eZSurveyEntry
{
    function eZSurveyNumberEntry( $row = false )
    {
        if ( !isset( $row['mandatory'] ) )
            $row['mandatory'] = 1;
        $row['type'] = 'NumberEntry';
        $this->eZSurveyEntry( $row );
    }

    function processViewActions( &$validation )
    {
        $http =& eZHTTPTool::instance();
        $locale =& eZLocale::instance();

        $answer = trim( $http->postVariable( 'SurveyAnswer_'.$this->ID ) );

        if ( $this->attribute( 'mandatory' ) == 1 && strlen( $answer ) == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = ezi18n( 'survey', 'Please answer the question ( %name ) as well!', null,
	    array( '%number' => $this->questionNumber(),'%name'=>$this->attribute('text') ) );
            return;
        }

        if ( strlen( $answer ) == 0 )
        {
            $this->setAnswer( '' );
            return;
        }

        $answer =& $locale->internalNumber( $answer );
        $min = $this->attribute( 'text2' );
        if ( strlen( $min ) == 0 )
            $min = false;
        $max = $this->attribute( 'text3' );
        if ( strlen( $max ) == 0 )
            $max = false;
        if ( $this->attribute( 'num' ) )
        {
            // due to bug in eZIntegerValidator: 6.00 is not integer for it...
            if ( is_numeric( $answer ) && (int) $answer == $answer )
                $answer = (int) $answer;
            $reqInteger = true;
            $validator = new eZIntegerValidator( $min, $max );
            if ( $min !== false )
                $minText = $min;
            if ( $max !== false )
                $maxText = $max;
        }
        else
        {
            $reqInteger = false;
            $validator = new eZFloatValidator( $min, $max );
            if ( $min !== false )
                $minText = $locale->formatNumber( $min );
            if ( $max !== false )
                $maxText = $locale->formatNumber( $max );
        }
        switch ( $validator->validate( $answer ) )
        {
            case EZ_INPUT_VALIDATOR_STATE_ACCEPTED:
            {
                $this->setAnswer( $answer );
            } break;

            case EZ_INPUT_VALIDATOR_STATE_INTERMEDIATE:
            {
                $validation['error'] = true;
                if ( $min == false && $max == false )
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question ( %name ) is not an integer number!', null,
			array( '%number' => $this->questionNumber(),'%name'=>$this->attribute('text') ) );
                    }
                    else
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question ( %name ) is not a number!', null,
			array( '%number' => $this->questionNumber(),'%name'=>$this->attribute('text') ) );
                    }
                }
                else if ( $min == false )
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered number in the question ( %name ) is not integer or is not lower than or equal to %max!', null,
                                                          array( '%number' => $this->questionNumber(),
                                                                 '%max' => $maxText,
							         '%name'=>$this->attribute('text') ) );
                    }
                    else
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered number in the question ( %name ) must be lower than or equal to %max!', null,
                                                          array( '%number' => $this->questionNumber(),
                                                                 '%max' => $maxText,
							         '%name'=>$this->attribute('text') ) );
                    }
                }
                else if ( $max == false )
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered number in the question ( %name ) is not integer or is not greater than or equal to %min!', null,
                                                          array( '%number' => $this->questionNumber(),
                                                                 '%min' => $minText,
							         '%name'=> $this->attribute('text') ) );
                    }
                    else
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered number in the question ( %name )  must be greater than or equal to %min!', null,
                                                          array( '%number' => $this->questionNumber(),
                                                                 '%min' => $minText,
							         '%name'=>$this->attribute('text') ) );
                    }
                }
                else
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered number in the question ( %name ) is not integer or is not between %min and %max!', null,
                                                          array( '%number' => $this->questionNumber(),
                                                                 '%min' => $minText,
                                                                 '%max' => $maxText,
							         '%name'=>$this->attribute('text') ) );
                    }
                    else
                    {
                        $validation['errors'][] = ezi18n( 'survey', 'Entered number in the question ( %name ) must be between %min and %max!', null,
                                                          array( '%number' => $this->questionNumber(),
                                                                 '%min' => $minText,
                                                                 '%max' => $maxText,
							         '%name'=>$this->attribute('text') ) );
                    }
                }
            } break;

            default:
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question ( %name ) is not an integer number!', null,
		    array( '%number' => $this->questionNumber(),'%name'=>$this->attribute('text') ) );
                }
                else
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question ( %name ) is not a number!', null,
		    array( '%number' => $this->questionNumber(),'%name'=>$this->attribute('text') ) );
                }
            } break;
        }
    }

    function processEditActions( &$validation )
    {
        parent::processEditActions( $validation );
        $http =& eZHTTPTool::instance();
        $locale =& eZLocale::instance();

        if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Num_Hidden' ) )
        {
            if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Num' ) )
                $newNum = 1;
            else
                $newNum = 0;
            if ( $this->attribute( 'num' ) != $newNum )
                $this->setAttribute( 'num', $newNum );
        }

        if ( $this->attribute( 'num' ) )
        {
            $reqInteger = true;
            $validator = new eZIntegerValidator;
        }
        else
        {
            $reqInteger = false;
            $validator = new eZFloatValidator;
        }

        $this->setAttribute( 'text2', trim( $this->attribute( 'text2' ) ) );
        $this->setAttribute( 'text3', trim( $this->attribute( 'text3' ) ) );
        $this->setAttribute( 'default_value', trim( $this->attribute( 'default_value' ) ) );

        if ( strlen( $this->attribute( 'text2' ) ) > 0 )
        {
            $data = $this->attribute( 'text2' );
            $data = trim( $data );
            $data =& $locale->internalNumber( $data );
            if ( $reqInteger && is_numeric( $data ) && (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) == EZ_INPUT_VALIDATOR_STATE_ACCEPTED )
            {
                $this->setAttribute( 'text2', $data );
            }
            else
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question with id ( %name ) is not an integer number!', null,
		    array( '%number' => $this->ID,'%name'=>$this->attribute('text') ) );
                }
                else
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question with id ( %name ) is not an number!', null,
		    array( '%number' => $this->ID, '%name'=>$this->attribute('text') ) );
                }
            }
        }

        if ( strlen( $this->attribute( 'text3' ) ) > 0 )
        {
            $data = $this->attribute( 'text3' );
            $data = trim( $data );
            $data =& $locale->internalNumber( $data );
            if ( $reqInteger && is_numeric( $data ) && (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) == EZ_INPUT_VALIDATOR_STATE_ACCEPTED )
            {
                $this->setAttribute( 'text3', $data );
            }
            else
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question with id ( %name ) is not an integer number!', null,
		    array( '%number' => $this->ID,'%name'=>$this->attribute('text') ) );
                }
                else
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question with id ( %name ) is not an number!', null,
		    array( '%number' => $this->ID,'%name'=>$this->attribute('text') ) );
                }
            }
        }

        if ( strlen( $this->attribute( 'default_value' ) ) > 0 )
        {
            $data = $this->attribute( 'default_value' );
            $data = trim( $data );
            $data =& $locale->internalNumber( $data );
            if ( $reqInteger && is_numeric( $data ) && (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) == EZ_INPUT_VALIDATOR_STATE_ACCEPTED )
            {
                $this->setAttribute( 'default_value', $data );
            }
            else
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question with id ( %name ) is not an integer number!', null,
		    array( '%number' => $this->ID,'%name'=>$this->attribute('text') ) );
                }
                else
                {
                    $validation['errors'][] = ezi18n( 'survey', 'Entered text in the question with id ( %name ) is not an number!', null,
		    array( '%number' => $this->ID,'%name'=>$this->attribute('text') ) );
                }
            }
        }
    }
}

eZSurveyQuestion::registerQuestionType( 'Number Entry', 'NumberEntry' );

?>
