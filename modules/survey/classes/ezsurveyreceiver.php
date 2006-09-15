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

/*! \file ezsurveyreceiver.php
*/

include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyquestion.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyquestionresult.php' );
include_once( 'lib/ezxml/classes/ezxml.php' );
include_once( 'lib/ezutils/classes/ezmail.php' );

class eZSurveyReceiver extends eZSurveyQuestion
{
    function eZSurveyReceiver( $row = false )
    {
        $row['type'] = 'Receiver';
        $this->eZSurveyQuestion( $row );
        $this->decodeXMLOptions();
    }

    function addOption( $label, $value, $checked )
    {
        ++$this->OptionID;
        $this->Options[] = array( 'id' => $this->OptionID,
                                  'label' => $label,
                                  'value' => $value,
                                  'checked' => $checked,
                                  'toggled' => $checked,
                                  'tagged' => 0 );
    }

    function removeTaggedOptions()
    {
        $iterator = 1;
        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            if ( $option['tagged'] == 1 )
            {
                unset( $this->Options[$key] );
                $this->setHasDirtyData( true );
            }
            else
                $option['id'] = $iterator++;
        }
    }

    function reorderOptions()
    {
        $iterator = 1;
        foreach ( array_keys ( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $option['id'] = $iterator++;
        }
    }

    function tabOrderCompare( &$option1, &$option2 )
    {
        $http =& eZHTTPTool::instance();

        $oldOrder1 =& $option1['id'];
        $oldOrder2 =& $option2['id'];
        $newOrder1 =& $http->postVariable( 'SurveyReceiver_'.$this->ID.'_'.$oldOrder1.'_TabOrder' );
        $newOrder2 =& $http->postVariable( 'SurveyReceiver_'.$this->ID.'_'.$oldOrder2.'_TabOrder' );

        if ( $newOrder1 < $newOrder2 )
            return -1;
        else if ( $newOrder1 > $newOrder2 )
            return 1;
        else
        {
            if ( $oldOrder1 > $oldOrder2 )
                return -1;
            else if ( $oldOrder1 < $oldOrder2 )
                return 1;
            else
                return 0;
        }
    }

    function decodeXMLOptions()
    {
        $this->Options = array();
        if ( $this->Text2 != '' )
        {
            $xml = new eZXML();
            $dom =& $xml->domTree( $this->Text2 );
            $optionArray =& $dom->elementsByName( "option" );
            if ( $optionArray )
            {
                foreach ( $optionArray as $option )
                {
                    $optionLabel = $option->elementsByName( "label" );
                    $optionLabel = $optionLabel[0]->textContent();
                    $optionValue = $option->elementsByName( "email" );
                    $optionValue = $optionValue[0]->textContent();
                    $optionChecked = $option->elementsByName( "checked" );
                    $optionChecked = $optionChecked[0]->textContent();
                    $this->addOption( $optionLabel, $optionValue, $optionChecked );
                }
            }
            else
                $this->addOption( '', '', 0 );
        }
        else
            $this->addOption( '', '', 0 );
    }

    function encodeXMLOptions()
    {
        $doc = new eZDOMDocument();
        $root =& $doc->createElementNode( "options" );
        $doc->setRoot( $root );
        foreach ( $this->Options as $optionArray )
        {
            $option =& $doc->createElementNode( "option" );
            $optionLabel =& $doc->createElementNode( "label" );
            $optionLabel->appendChild( $doc->createTextNode( $optionArray['label'] ) );
            $option->appendChild( $optionLabel );

            $optionValue =& $doc->createElementNode( "email" );
            $optionValue->appendChild( $doc->createTextNode( $optionArray['value'] ) );
            $option->appendChild( $optionValue );

            $optionChecked =& $doc->createElementNode( "checked" );
            $optionChecked->appendChild( $doc->createTextNode( $optionArray['checked'] ) );
            $option->appendChild( $optionChecked );

            $root->appendChild( $option );
        }
        $this->Text2 =& $doc->toString();
    }

    function hasAttribute( $attr_name )
    {
        if ( $attr_name == 'options' )
            return true;
        return eZSurveyQuestion::hasAttribute( $attr_name );
    }

    function &attribute( $attr_name )
    {
        if ( $attr_name == 'options' )
            return $this->Options;
        return eZSurveyQuestion::attribute( $attr_name );
    }

    function processViewActions( &$validation )
    {
        $http =& eZHTTPTool::instance();

        if ( !$http->hasPostVariable( 'SurveyID' ) )
            return;

        if ( $http->hasPostVariable( 'SurveyAnswer_'.$this->ID ) )
        {
            $answer =& $http->postVariable( 'SurveyAnswer_'.$this->ID );
            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                if ( $option['id'] == $answer )
                {
                    $option['toggled'] = 1;
                    $this->setAnswer( $option['value'] );
                }
                else
                    $option['toggled'] = 0;
            }
        }
    }

    function processEditActions( &$validation )
    {
        $http =& eZHTTPTool::instance();

        eZSurveyQuestion::processEditActions( $validation );
        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionID = $option['id'];
            if ( $http->hasPostVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Label' ) &&
                 $http->postVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Label' ) != $option['label'] )
            {
                $option['label'] = $http->postVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Label' );
                $this->setHasDirtyData( true );
            }

            if ( $http->hasPostVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Value' ) &&
                 $http->postVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Value' ) != $option['value'] )
            {
                $option['value'] = $http->postVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Value' );
                $this->setHasDirtyData( true );
            }

            $checked = ( $http->hasPostVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Checked' ) )? 1: 0;
            if ( $checked != $option['checked'] )
            {
                $option['checked'] = $checked;
                $this->setHasDirtyData( true );
            }
            
            $tagged = ( $http->hasPostVariable( 'SurveyReceiver_'.$this->ID.'_'.$optionID.'_Selected' ) )? 1: 0;
            if ( $tagged )
            {
                $option['tagged'] = $tagged;
            }
        }

        usort( $this->Options, array( $this, 'tabOrderCompare' ) );

        if ( $http->hasPostVariable( 'SurveyReceiver_'.$this->ID.'_RemoveSelected' ) )
            $this->removeTaggedOptions();
        else
            $this->reorderOptions();

        $optionValues = array();
        $optionCount = 0;
        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            
            if ( !eZMail::validate( $option['value'] ) )
            {
                $validation['error'] = true;
                $validation['errors'][] = ezi18n( 'survey', "Entered text '%text' in the question with id %number is not an email address!", null,
                                                  array( '%number' => $this->ID,
                                                         '%text' => $option['value'] ) );
                break;
            }
            
            if ( in_array( $option['value'], $optionValues ) )
            {
                $validation['error'] = true;
                $validation['errors'][] = ezi18n( 'survey', 'Email addresses in the question with id %question must have unique values!', null,
                                                  array( '%number' => $this->ID ) );
                break;
            }
            $optionValues[] = $option['value'];
        }
        if ( $http->hasPostVariable( 'SurveyReceiver_'.$this->ID.'_NewOption' ) )
        {
            $this->addOption( '', '', 0 );
            $this->setHasDirtyData( true );
        }
        $this->encodeXMLOptions();
    }

    function questionNumberIterate( &$iterator )
    {
        if ( count( $this->Options ) > 1 )
            $this->QuestionNumber=$iterator++;
    }

    function &answer()
    {
        if ( count( $this->Options ) > 1 )
            return parent::answer();
        $arrayKeys = array_keys( $this->Options );
        return $this->Options[$arrayKeys[0]]['value'];
    }

    var $Options;
    var $OptionID=0;
}

eZSurveyQuestion::registerQuestionType( 'Form Receiver', 'Receiver', true );

?>