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

/*! \file ezsurveyentry.php
*/

include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyquestion.php' );

class eZSurveyEntry extends eZSurveyQuestion
{
    function eZSurveyEntry( $row = false )
    {
        $this->eZSurveyQuestion( $row );
    }

    function processViewActions( &$validation )
    {
        $http =& eZHTTPTool::instance();

        if ( !$http->hasPostVariable( 'SurveyID' ) )
            return;

        if ( $this->attribute( 'mandatory' ) == 1 && strlen( trim ( $http->postVariable( 'SurveyAnswer_'.$this->ID ) ) ) == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = ezi18n( 'survey', 'Please answer the question %number as well!', null,
                                              array( '%number' => $this->questionNumber() ) );
        }
        $this->setAnswer( trim ( $http->postVariable( 'SurveyAnswer_'.$this->ID ) ) );
    }

    function &result()
    {
        $result =& eZSurveyEntry::fetchResult( $this, false, 5 );
        return $result['result'];
    }

    function &fetchResult( $question, $metadata = false, $limit = false, $sortBy = false )
    {
        $db =& eZDB::instance();

        $resultArray = array();

        $query = 'SELECT text as value FROM ezsurveyquestionresult';
        if ( $metadata != false )
        {
            for( $index = 1; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
        }
        $query .= ' where question_id=\'';
        $query .= $question->attribute( 'id' );
        $query .= '\' and length(text)>0';
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

        if ( is_array( $sortBy ) )
        {
            $sortField = 'text';
            if ( isSet( $sortBy[0] ) )
                $sortField = $sortBy[0];

            switch ( $sortField )
            {
                case 'text':
                {
                    $sortingFields = 'ezsurveyquestionresult.text';
                } break;

                default:
                {
                    eZDebug::writeWarning( 'Unknown sort field: ' . $sortField, 'eZSurveyEntry::fetchResult' );
                    continue;
                };
            }

            $sortOrder = true;
            if ( isSet( $sortBy[1] ) )
                $sortOrder = $sortBy[1];
            $sortingFields .= $sortOrder ? " ASC" : " DESC";

            $query .= " ORDER BY $sortingFields";
        }
        else
        {
            $query .= ' ORDER BY ezsurveyquestionresult.id DESC';
        }

        $queryParams = array();
        if ( $limit != false && $limit != 0 )
            $queryParams['limit'] = $limit;

        $rows =& $db->arrayQuery( $query, $queryParams );

        return array( 'result' => $rows );
    }

    function &fetchResultItem( $question, $result_id, $metadata = false )
    {
        $result =& eZPersistentObject::fetchObject( eZSurveyQuestionResult::definition(),
                                                    'text',
                                                    array( 'question_id' => $question->attribute( 'id' ),
                                                           'result_id' => $result_id ),
                                                    false );

        return array( 'result' => $result['text'] );
    }
}

?>
