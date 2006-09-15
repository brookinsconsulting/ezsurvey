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

/*! \file ezsurveymultiplechoice.php
*/

include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyquestion.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyquestionresult.php' );
include_once( 'lib/ezxml/classes/ezxml.php' );

class eZSurveyMultipleChoice extends eZSurveyQuestion
{
    function eZSurveyMultipleChoice( $row = false )
    {
        $row['type'] = 'MultipleChoice';
        $this->eZSurveyQuestion( $row );
        $this->decodeXMLOptions();
    }

    function afterAdding()
    {
        $this->addOption( '', false, 0 );
        $this->encodeXMLOptions();
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
        $newOrder1 =& $http->postVariable( 'SurveyMC_'.$this->ID.'_'.$oldOrder1.'_TabOrder' );
        $newOrder2 =& $http->postVariable( 'SurveyMC_'.$this->ID.'_'.$oldOrder2.'_TabOrder' );

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
                    $optionValue = $option->elementsByName( "value" );
                    $optionValue = $optionValue[0]->textContent();
                    $optionChecked = $option->elementsByName( "checked" );
                    $optionChecked = $optionChecked[0]->textContent();
                    $this->addOption( $optionLabel, $optionValue, $optionChecked );
                }
            }
        }
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

            $optionValue =& $doc->createElementNode( "value" );
            $optionValue->appendChild( $doc->createTextNode( $optionArray['value'] ) );
            $option->appendChild( $optionValue );

            $optionChecked =& $doc->createElementNode( "checked" );
            $optionChecked->appendChild( $doc->createTextNode( $optionArray['checked'] ) );
            $option->appendChild( $optionChecked );

            $root->appendChild( $option );

            unset( $option );
            unset( $optionLabel );
            unset( $optionValue );
            unset( $optionChecked );
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

        if ( !$http->hasPostVariable( 'SurveyAnswer_'.$this->ID ) &&
             $this->attribute( 'num' ) != 3 &&          // 3 - checkboxes in a row
             $this->attribute( 'num' ) != 4 )           // 4 - checkboxes in a column
        {
            $validation['error'] = true;
            $validation['errors'][] = ezi18n( 'survey', 'Please answer the question ( %question ) as well!', null,
	    array( '%number' => $this->questionNumber(),'%question'=>$this->attribute('text') ) );
        }
        else
        {
            $answer =& $http->postVariable( 'SurveyAnswer_'.$this->ID );
            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                if ( is_array( $answer ) )
                {
                    if ( in_array( $option['value'], $answer ) )
                        $option['toggled'] = 1;
                    else
                        $option['toggled'] = 0;
                }
                else
                {
                    if ( $option['value'] == $answer )
                        $option['toggled'] = 1;
                    else
                        $option['toggled'] = 0;
                }
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
            $tagged = ( $http->hasPostVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Selected' ) )? 1: 0;
            if ( $tagged )
            {
                $option['tagged'] = $tagged;
            }
        }

        if ( $http->hasPostVariable( 'SurveyMC_'.$this->ID.'_RemoveSelected' ) )
        {
            $this->removeTaggedOptions();
        }

        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionID = $option['id'];
            if ( $http->hasPostVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Label' ) &&
                 $http->postVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Label' ) != $option['label'] )
            {
                $option['label'] = $http->postVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Label' );
                $this->setHasDirtyData( true );
            }

            if ( $http->hasPostVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Value' ) )
            {
                $option['value'] = trim( $http->postVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Value' ) );
                if ( strlen( $option['value'] ) == 0 )
                {
                    $validation['error'] = true;
                    $validation['errors'][] = ezi18n( 'survey', 'You must enter the value for an option in the question with id %question ( %name ) !', null,
		    array( '%question' => $this->ID,'%name'=>$this->attribute('text') ) );
                }
                if ( $http->postVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Value' ) != $option['value'] )
                    $this->setHasDirtyData( true );
            }

            $checked = ( $http->hasPostVariable( 'SurveyMC_'.$this->ID.'_'.$optionID.'_Checked' ) )? 1: 0;
            if ( $checked != $option['checked'] )
            {
                $option['checked'] = $checked;
                $this->setHasDirtyData( true );
            }
        }

        usort( $this->Options, array( $this, 'tabOrderCompare' ) );

        $this->reorderOptions();

        $optionValues = array();
        $optionCount = 0;
        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionCount++;
            if ( in_array( $option['value'], $optionValues ) )
            {
                $validation['error'] = true;
                $validation['errors'][] = ezi18n( 'survey', 'Options in the question with id %question ( %name ) must have unique values!', null,
		array( '%question' => $this->ID,'%name'=>$this->attribute('name') ) );
                break;
            }
            $optionValues[] = $option['value'];
        }
        if ( $optionCount == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = ezi18n( 'survey', 'You must enter at least one option in the question with id %question ( %name ) !', null,
	    array( '%question' => $this->ID,'%name'=>$this->attribute('text') ) );
        }
        if ( $http->hasPostVariable( 'SurveyMC_'.$this->ID.'_NewOption' ) )
        {
            $this->addOption( '', false, 0 );
            $this->setHasDirtyData( true );
        }
        $this->encodeXMLOptions();
    }

    function &result()
    {
        $result =& eZSurveyMultipleChoice::fetchResult( $this );
        return $result['result'];
    }

    // from fetching from template
    function &fetchResult( $question, $metadata = false )
    {
        $db =& eZDB::instance();

        $resultArray = array();
        foreach ( $question->Options as $option )
        {
            $resultArray[$option['value']] = array( 'label' => $option['label'],
                                                    'value' => $option['value'],
                                                    'count' => 0,
                                                    'percentage' => 0 );
        }
        if ( $metadata == false )
        {
            $query = 'SELECT count(distinct id) as count from ezsurveyresult where survey_id=\'';
            $query .= $question->attribute( 'survey_id' );
            $query .= '\'';
        }
        else
        {
            $query = 'SELECT count(distinct m1.result_id) as count from ezsurveyresult, ezsurveymetadata as m1';
            for( $index=2; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
            $query .= ' where survey_id=\'';
            $query .= $question->attribute( 'survey_id' );
            $query .= '\'';
            $index = 0;
            foreach ( array_keys( $metadata ) as $key )
            {
                $index++;
                if ( $index == 1 )
                    $query .= ' and ezsurveyresult.id=m1.result_id';
                else
                {
                    $query .= ' and m';
                    $query .= ( $index - 1 );
                    $query .= '.result_id=m';
                    $query .= $index;
                    $query .= '.result_id';
                }
                $query .= ' and m';
                $query .= $index;
                $query .= '.attr_name=\'';
                $query .= $key;
                $query .= '\' and m';
                $query .= $index;
                $query .= '.attr_value=\'';
                $query .= $metadata[$key];
                $query .= '\'';
            }
        }
        $rows =& $db->arrayQuery( $query );
        $count= $rows[0]['count'];
        if ( $count == 0 )
        {
            return array( 'result' => $resultArray );
        }

        $query = 'SELECT text,count(text) as count from ezsurveyquestionresult';
        if ( $metadata != false )
        {
            for( $index=1; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
        }
        $query .= ' where question_id=\'';
        $query .= $question->attribute( 'id' );
        $query .= '\'';
        $index = 0;
        if ( $metadata != false )
        {
            foreach ( array_keys( $metadata ) as $key )
            {
                $index++;
                if ( $index == 1 )
                    $query .= ' and ezsurveyquestionresult.result_id=m1.result_id';
                else
                {
                    $query .= ' and m';
                    $query .= ( $index - 1 );
                    $query .= '.result_id=m';
                    $query .= $index;
                    $query .= '.result_id';
                }
                $query .= ' and m';
                $query .= $index;
                $query .= '.attr_name=\'';
                $query .= $key;
                $query .= '\' and m';
                $query .= $index;
                $query .= '.attr_value=\'';
                $query .= $metadata[$key];
                $query .= '\'';
            }
        }
        $query .= ' group by text';
        $rows =& $db->arrayQuery( $query );
        foreach ( $rows as $row )
        {
            $percentage = (int) ( ( 100 * $row['count'] ) / $count );
            if ( $percentage > 100 )
                $percentage = 100;
            $resultArray[$row['text']] = array( 'label' => $resultArray[$row['text']]['label'],
                                                'value' => $row['text'],
                                                'count' => $row['count'],
                                                'percentage' => $percentage );
        }
        return array( 'result' => $resultArray );
    }

    function &fetchResultItem( $question, $result_id, $metadata = false )
    {
        $labelArray = array();
        foreach ( $question->Options as $option )
        {
            $labelArray[$option['value']] = $option['label'];
        }

        $result =& eZPersistentObject::fetchObjectList(
            eZSurveyQuestionResult::definition(),
            'text',
            array( 'question_id' => $question->attribute( 'id' ),
                   'result_id' => $result_id ),
            array(),
            null,
            false );

        $resultArray = array();
        foreach ( array_keys( $result ) as $key )
        {
            $resultArray[] = array( 'value' => $result[$key]['text'],
                                    'label' => $labelArray[$result[$key]['text']] );
        }
        return array( 'result' => $resultArray );
    }

    function isSingleQuestion()
    {
        $type = $this->attribute( 'num' );
        return ( $type != 3 && $type != 4 )? true: false;
    }

    var $Options;
    var $OptionID=0;
}

eZSurveyQuestion::registerQuestionType( ezi18n( 'survey', 'Single/Multiple Choice' ),
                                        'MultipleChoice' );

?>
