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

/*! \file ezsurveyresult.php
*/

include_once( 'kernel/classes/ezpersistentobject.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurvey.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveymetadata.php' );

class eZSurveyResult extends eZPersistentObject
{
    function eZSurveyResult( $row, $metaData = null )
    {
        $this->eZPersistentObject( $row );
        if ( $metaData == false )
        {
            $this->MetaData = null;
        }
        else
        {
            $this->MetaData = $metaData;
        }
    }

    function &definition()
    {
        return array( 'fields' => array ( 'id' => array( 'name' => 'ID',
                                                         'datatype' => 'integer',
                                                         'default' => 0,
                                                         'required' => true ),
                                          'survey_id' => array( 'name' => 'SurveyID',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                          'user_id' => array( 'name' => 'UserID',
                                                              'datatype' => 'integer',
                                                              'default' => EZ_USER_ANONYMOUS_ID,
                                                              'required' => false ),
                                          'tstamp' => array( 'name' => 'TStamp',
                                                             'datatype' => 'integer',
                                                           'default' => 0,
                                                           'required' => false ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array( 'question_results' => 'fetchQuestionResultList' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurveyResult',
                      'sort' => array( 'id', 'asc' ),
                      'name' => 'ezsurveyresult' );
    }

    function instance( $surveyID, $userID = false )
    {
        if ( $userID )
        {
            $surveyResult =& eZPersistentObject::fetchObject( eZSurveyResult::definition(),
                                                              null,
                                                              array( 'survey_id' => $surveyID,
                                                                     'user_id' => $userID ) );
            if ( $surveyResult )
            {
                return $surveyResult;
            }
        }

        return new eZSurveyResult( array( 'survey_id' => $surveyID ) );
    }

    function storeResult()
    {
        $this->setAttribute( 'tstamp', time() );
        $this->store();
        $object = new eZSurveyMetaData( array( 'result_id' => $this->ID ) );
        if ( $this->MetaData !== null )
        {
            foreach( array_keys( $this->MetaData ) as $key )
            {
                $object->setAttribute( 'attr_name', $key );
                $object->setAttribute( 'attr_value', $this->MetaData[$key] );
                $object->store();
                $object->setAttribute( 'id', null );
            }
        }
        else
        {
            // to have stored result_id in ezsuveymetadata table too
            $object->setAttribute( 'attr_name', '' );
            $object->setAttribute( 'attr_value', '' );
            $object->store();
        }

        $survey =& eZSurvey::fetch( $this->attribute( 'survey_id' ) );
        $survey->storeResult( $this->ID );
    }

    /*!
     \static
     Fetch Survey Result object

     \param survey result id

     \return survey result object
    */
    function &fetch( $resultID )
    {
        return eZPersistentObject::fetchObject( eZSurveyResult::definition(),
                                                null,
                                                array( 'id' => $resultID ) );
    }

    // returns true if user with $user_id id (or current if false) has posted
    // survey with $survey_id id, false otherwise.
    function &fetchAlreadyPosted( $survey_id, $user_id = false )
    {
        if ( $user_id === false )
        {
            $user_id = eZUser::currentUserID();
        }
        return array( 'result' => ( eZPersistentObject::fetchObject( eZSurveyResult::definition(),
                                                                     null,
                                                                     array( 'survey_id' => $survey_id,
                                                                            'user_id' => $user_id ) ) )? true: false );
    }

    /*!
     Get previous results for current survey.

     \return array of question results. false if persistent is set to 0, no previous results exists or anonymous user.
     */
    function &fetchQuestionResultList( $asObject = false)
    {
        $rows = eZPersistentObject::fetchObjectList( eZSurveyQuestionResult::definition(),
                                                     null,
                                                     array( 'result_id' => $this->attribute( 'id' ) ),
                                                     false,
                                                     null,
                                                     $asObject );
        if ( $asObject )
        {
            return $rows;
        }

        $resultRows = array();
        foreach( $rows as $row )
        {
            if ( !isset ( $resultRows[(string)$row['question_id']] ) )
            {
                $resultRows[(string)$row['question_id']] = array();
            }
            $resultRows[(string)$row['question_id']][$row['text']] = $row['text'];
            $resultRows[(string)$row['question_id']]['text'] = $row['text'];
        }

        return $resultRows;
    }

    /*!
     \static
     Fetch Results for a specified query

     \param SurveyID
     \param Offset, default 0
     \param limit, default 15

     \return Array containing result objects.
    */
    function &fetchResultArray( $surveyID, $offset = 0, $limit = 15 )
    {
        return eZPersistentObject::fetchObjectList( eZSurveyResult::definition(),
                                                    null,
                                                    array( 'survey_id' => $surveyID ),
                                                    array( 'tstamp' => true ),
                                                    array( 'length' => $limit,
                                                           'offset' => $offset ) );
    }

    // static
    // if $offset is false, will return number of items
    // otherwise array, see close the function end.
    function &fetchResult( $surveyID, $offset = false, $metadata = false )
    {
        $db =& eZDB::instance();
        if ( $offset !== false )
            $offset = (int) $offset;

        if ( $metadata == false )
        {
            $query = ' FROM ezsurveyresult WHERE survey_id=\'';
            $query .= $surveyID;
            $query .= '\'';
        }
        else
        {
            $query = ' FROM ezsurveyresult, ezsurveymetadata as m1';
            for( $index=2; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
            $query .= ' where survey_id=\'';
            $query .= $surveyID;
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
        $countRows =& $db->arrayQuery( 'SELECT count(distinct ezsurveyresult.id) as count'.$query );
        $count= $countRows[0]['count'];

        if ( $offset === false )
            return $count;

        $resultIDRows =& $db->arrayQuery( 'SELECT distinct ezsurveyresult.id as result_id, ezsurveyresult.user_id as user_id'.$query, array ( 'limit' => 1,
                                                                                      'offset' => $offset ) );
        $resultID = false;
        $userID = false;
        // the key for $resultIDRows is not known:
        foreach ( array_keys( $resultIDRows ) as $key )
        {
            $resultID = $resultIDRows[$key]['result_id'];
            $userID = $resultIDRows[$key]['user_id'];
        }
        return array( 'count' => $count, 'result_id' => $resultID, 'user_id' => $userID );
    }

    // static
    // exports surveys
    // TODO: export metadata as well...
    function exportCSV( $surveyID )
    {
        function printLine( $list, $array )
        {
            foreach( $list as $key )
            {
                echo '"'.str_replace( '"', "'", $array[$key] ).'";';
            }
            echo "\n";
        }

        $survey = eZSurvey::fetch( $surveyID );
        if ( !$survey || !$survey->published() || !$survey->enabled() || !$survey->valid() )
            return false;

        $questionList = $survey->fetchQuestionList();

        $questions = array();
        $indexList = array();
        foreach( array_keys( $questionList ) as $key )
        {
            if ( $questionList[$key]->canAnswer() )
            {
                $indexList[] = $key;
                $questions[$key] = $questionList[$key]->attribute( 'text' );
            }
        }
        printLine( $indexList, $questions );

        $db =& eZDB::instance();
        $rows = $db->arrayQuery( "SELECT ezsurveyquestionresult.result_id as id, question_id, text
                                  FROM ezsurveyquestionresult, ezsurveyresult
                                  WHERE ezsurveyresult.id=ezsurveyquestionresult.result_id AND ezsurveyresult.survey_id=$surveyID
                                  ORDER BY tstamp ASC, ezsurveyquestionresult.result_id ASC" );
        $oldID = false;
        $answers = array();
        foreach( array_keys( $rows ) as $key )
        {
            $row =& $rows[$key];
            if ( $oldID != $row['id'] )
            {
                if ( $oldID !== false )
                {
                    printLine( $indexList, $answers );
                    unset( $answers );
                    $answers = array();
                }
                $oldID = $row['id'];
            }
            if ( isset( $answers[$row['question_id']] ) )
                $answers[$row['question_id']] .= ";".$row['text'];  // esp. for multiple check boxes
            else
                $answers[$row['question_id']] = $row['text'];
        }
        if ( $oldID !== false )
        {
           printLine( $indexList, $answers );
        }

        return true;
    }

    /*!
     \reimp
    */
    function remove()
    {
        foreach( $this->fetchQuestionResultList( true ) as $questionResult )
        {
            $questionResult->remove();
        }
        eZPersistentObject::remove();
    }

    var $ID;
    var $SurveyID;
    var $UserID;
    var $TStamp;
    var $Survey = false;
    var $MetaData;
}

?>
