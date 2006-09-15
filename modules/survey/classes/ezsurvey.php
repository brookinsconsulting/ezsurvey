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

/*! \file ezsurvey.php
*/

include_once( 'kernel/classes/ezpersistentobject.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyquestion.php' );
include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyquestions.php' );
include_once( 'kernel/classes/ezcontentobject.php' );
include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
include_once( 'kernel/classes/ezcontentbrowse.php' );
include_once( 'kernel/classes/ezcontentbrowsebookmark.php' );
include_once( 'kernel/classes/ezcontentclass.php' );
include_once( 'lib/ezdb/classes/ezdb.php');
include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'lib/ezutils/classes/ezini.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'kernel/content/ezcontentoperationcollection.php');


class eZSurvey extends eZPersistentObject
{
    function eZSurvey( $row = array() )
    {
        if ( !isset( $row['valid_from' ] ) )
            $row['valid_from'] = -time();
        if ( !isset( $row['valid_to' ] ) )
            $row['valid_to'] = -time();
        $this->eZPersistentObject( $row );
        $this->QuestionList = null;
    }

    function &definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'title' => array( 'name' => 'Title',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => true ),
                                         'enabled' => array( 'name' => 'Enabled',
                                                             'datatype' => 'integer',
                                                             'default' => '1',
                                                             'required' => true ),
                                         'published' => array( 'name' => 'Published',
                                                               'datatype' => 'integer',
                                                               'default' => '0',
                                                               'required' => true ),
                                         'persistent' => array( 'name' => 'Persistent',
                                                                'datatype' => 'integer',
                                                                'default' => '0',
                                                                'required' => true ),
                                         'valid_from' => array( 'name' => 'ValidFrom',
                                                                'datatype' => 'integer',
                                                                'default' => '0',
                                                                'required' => true ),
                                         'valid_to' => array( 'name' => 'ValidTo',
                                                              'datatype' => 'integer',
                                                              'default' => '0',
                                                              'required' => true ),
                                         'redirect_cancel' => array( 'name' => 'RedirectCancel',
                                                              'datatype' => 'string',
                                                              'default' => '/content/view/full/2',
                                                              'required' => true ),
                                         'redirect_submit' => array( 'name' => 'RedirectSubmit',
                                                              'datatype' => 'string',
                                                              'default' => '/content/view/full/2',
                                                              'required' => true ),
                                         'node_id' => array( 'name' => 'NodeID',
                                                              'datatype' => 'integer',
                                                              'default' => '0',
                                                              'required' => false )
						      ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array( 'question_results' => 'fetchQuestionResultList',
                                                      'result_count' => 'resultCount',
                                                      'questions' => 'fetchQuestionList',
                                                      'question_types' => 'questionTypes',
                                                      'valid_from_array' => 'validFromArray',
                                                      'valid_to_array' => 'validToArray',
                                                      'valid' => 'valid',
                                                      'can_edit_results' => 'canEditResults' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurvey',
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'ezsurvey' );
    }

    function &clone()
    {
        $row = array( 'id' => null,
                      'title' => $this->Title.' (Copy)',
                      'valid_from' => $this->ValidFrom,
                      'valid_to' => $this->ValidTo,
                      'enabled' => 1,
                      'published' => 0 );
        $cloned = new eZSurvey( $row );
        $cloned->store();
        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }
        foreach( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->clone( $cloned->attribute( 'id' ) );
        }
        return $cloned;
    }

    /*!
     Check current user can edit survey results.

     \return true if user is allowed to edit survey results.
    */
    function canEditResults()
    {
        $user =& eZUser::instance();
        $accessList = 1;

        $accessResult = $user->hasAccessTo( 'survey', 'administration' );

        return $accessResult['accessWord'] == 'yes';
    }

    /*!
     Get number of results for current survey
    */
    function resultCount()
    {
        $db =& eZDB::instance();
        $sql = 'SELECT count(*) as count from ezsurveyresult WHERE survey_id = \'' . $this->attribute( 'id' ) . '\'';
        $rows = $db->arrayQuery( $sql );
        return $rows[0]['count'];
    }

    function id()
    {
        return $this->ID;
    }

    /*!
      Fetch eZSurvey object

      \param eZSurvey ID
      \param as Object ( optional, default true )

      \return eZSurvey
    */
    function &fetch( $id , $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZSurvey::definition(),
                                                null,
                                                array( 'id' => $id ),
                                                $asObject );
    }

    /*!
     \static

      Fetch survey object. Return false if survey is not published, not enabled or not valid.

      \param Survey id

      \return Survey object
    */
    function &fetchSurvey( $id )
    {
        $survey =& eZSurvey::fetch( $id );
        //if ( !$survey || !$survey->published() || !$survey->enabled() || !$survey->valid() )
        $current_site_access = $GLOBALS['eZCurrentAccess'];

        $error = false;

        if($current_site_access['name']=='admin'){

	   $error = !$survey;

        }else{

           $error = ( !$survey || !$survey->published() || !$survey->enabled() || !$survey->valid() );

        }

        if ( $error)
            $survey = false;

        return array( 'result' => $survey );
    }

    function &fetchList()
    {
        $surveys=& eZSurvey::fetchSurveyList();
        return array( 'result' => $surveys );
    }

    /*!
     Get previous results for current survey.

     \param user object.

     \return array of question results. false if persistent is set to 0, no previous results exists or anonymous user.
     */
    function &fetchQuestionResultList( $user = false )
    {
        if ( !$this->attribute( 'persistent' ) )
        {
            return 0;
        }

        if ( !$user )
        {
            $user =& eZUser::instance();
        }

        if ( !$user->attribute( 'is_logged_in' ) )
        {
            return 0;
        }

        include_once( 'extension/ezsurvey/modules/survey/classes/ezsurveyresult.php' );

        $result =& eZPersistentObject::fetchObject( eZSurveyResult::definition(),
                                                    null,
                                                    array( 'survey_id' => $this->ID,
                                                           'user_id' => $user->attribute( 'contentobject_id' ) ) );

        if ( !$result )
        {
            return 0;
        }

        return $result->fetchQuestionResultList();
    }

    /*!
     Fetch list of questions objects for the current survey.

     \return Array of Question objects.
    */
    function &fetchQuestionList()
    {
        if ( $this->QuestionList === null )
        {
            $rows = eZPersistentObject::fetchObjectList( eZSurveyQuestion::definition(),
                                                         null,
                                                         array( 'survey_id' => $this->ID ),
                                                         array( 'tab_order' => 'asc' ),
                                                         null,
                                                         false );
            $objects = array();
            $this->QuestionList = array();
            $questionIterator = 1;
            foreach ( $rows as $row )
            {
                $classname = implode( '', array( 'eZSurvey', $row['type'] ) );
                $newObject = new $classname( $row );
                $newObject->questionNumberIterate( $questionIterator );
                $this->QuestionList[$newObject->attribute( 'id' )] = $newObject;
            }
        }
        return $this->QuestionList;
    }

    function &fetchSurveyList()
    {
        $rows = eZPersistentObject::fetchObjectList( eZSurvey::definition(),
                                                     null,
                                                     null,
                                                     array( 'id' => 'desc' ),
                                                     null,
                                                     true );
        return $rows;
    }

    function enabled()
    {
        return ( $this->Enabled == '1' )? true: false;
    }

    function published()
    {
        return ( $this->Published == '1' )? true: false;
    }

    /*!
     Check if survey is in the valid timeframe

     \return true if the survey is in the valid time interval
    */
    function valid()
    {
        return ( $this->ValidFrom <= 0 || $this->ValidFrom <= time() ) &&
               ( $this->ValidTo <= 0 || $this->ValidTo >= time() );
    }

    function processViewActions( &$validation )
    {
        $validation['error'] = false;
        $validation['warning'] = false;
        $validation['errors'] = array();
        $validation['warnings'] = array();

        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        $http =& eZHTTPTool::instance();

        if ( !$http->hasPostVariable( 'SurveyID' ) )
            return;

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->processViewActions( $validation );
        }
    }

    function reOrder()
    {
        $iterator = 1;
        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            if ( $question->attribute( 'tab_order' ) != $iterator )
                $question->setAttribute( 'tab_order', $iterator );
            $iterator++;
        }
    }

    /*!
     Processes the editing input of the survey, called by the module view.

     Return the result in the input parameter
    */
    function processEditActions( &$validation )
    {
        $validation['error'] = false;
        $validation['warning'] = false;
        $validation['errors'] = array();
        $validation['warnings'] = array();

        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        $http =& eZHTTPTool::instance();

        if ( !$http->hasPostVariable( 'SurveyID' ) )
            return;

        if ( $http->postVariable( 'SurveyTitle' ) != $this->Title )
            $this->setAttribute( 'title', $http->postVariable( 'SurveyTitle' ) );

        $enabled = ( $http->hasPostVariable( 'SurveyEnabled' ) )? 1: 0;

        if ( $enabled != $this->Enabled )
            $this->setAttribute( 'enabled', $enabled );

        $validFrom = mktime(
            $http->postVariable( 'SurveyValidFromHour' ),
            $http->postVariable( 'SurveyValidFromMinute' ),
            0,
            $http->postVariable( 'SurveyValidFromMonth' ),
            $http->postVariable( 'SurveyValidFromDay' ),
            $http->postVariable( 'SurveyValidFromYear' ) );
        if ( $http->hasPostVariable( 'SurveyValidFromNoLimit' ) )
            $validFrom = -$validFrom;
        if ( $this->ValidFrom != $validFrom )
            $this->setAttribute( 'valid_from', $validFrom );

        if ( $http->hasPostVariable( 'SurveyPersistent' ) )
            $this->setAttribute( 'persistent', 1 );
        else
            $this->setAttribute( 'persistent', 0 );

        $validTo = mktime(
            $http->postVariable( 'SurveyValidToHour' ),
            $http->postVariable( 'SurveyValidToMinute' ),
            0,
            $http->postVariable( 'SurveyValidToMonth' ),
            $http->postVariable( 'SurveyValidToDay' ),
            $http->postVariable( 'SurveyValidToYear' ) );
        if ( $http->hasPostVariable( 'SurveyValidToNoLimit' ) )
            $validTo = -$validTo;
        if ( $this->ValidTo != $validTo )
            $this->setAttribute( 'valid_to', $validTo );

        if ( $this->RedirectCancel != $http->postVariable( 'SurveyRedirectCancel' ) )
            $this->setAttribute( 'redirect_cancel', $http->postVariable( 'SurveyRedirectCancel' ) );
        if ( $this->RedirectSubmit != $http->postVariable( 'SurveyRedirectSubmit' ) )
            $this->setAttribute( 'redirect_submit', $http->postVariable( 'SurveyRedirectSubmit' ) );

        usort( $this->QuestionList, array( 'eZSurveyQuestion', 'tabOrderCompare' ) );

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            if ( $http->hasPostVariable( 'SurveyRemoveSelected' ) )
            {
                if ( $http->hasPostVariable( 'SurveyQuestion_'.$question->attribute( 'id' ).'_Selected' ) )
                {
                    $question->remove();
                    unset( $this->QuestionList[$key] );
                }
            }

            if ( $http->hasPostVariable( 'SurveyQuestionVisible_'.$question->attribute( 'id' ) ) )
            {
                $question->setAttribute( 'visible', 1 );
            }
            else
            {
                $question->setAttribute( 'visible', 0 );
            }
        }

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->processEditActions( $validation );
        }

        if ( $http->hasPostVariable( 'SurveyNewQuestion' ) )
        {
            $list = eZSurveyQuestion::listQuestionTypes();
            $type = $http->postVariable( 'SurveyNewQuestionType' );
            if ( isset( $list[$type] ) && ( $list[$type]['max_one_instance']==false || $list[$type]['count'] == 0 ) )
            {
                $classname = implode( '', array( 'eZSurvey', $type ) );
                $newObject = new $classname( array( 'survey_id' => $this->ID ) );
                $newObject->afterAdding();
                $this->QuestionList[$newObject->attribute( 'id' )] =& $newObject;
            }
        }

        $this->reOrder();
    }

    function sync( $fieldFilters = null )
    {
        if ( $this->QuestionList === false )
        {
            $this->fetchQuestionList();
        }
        eZPersistentObject::sync( $fieldFilters );
        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->sync();
        }
    }

    function storeAll()
    {
        if ( $this->QuestionList === false )
        {
            $this->fetchQuestionList();
        }
        $this->store();
        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->store();
        }
    }

    function storeResult( $resultID )
    {
        if ( !$this->QuestionList )
        {
            $this->fetchQuestionList();
        }

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->storeResult( $resultID );
        }
    }

    // Removes a survey, it's question and all collected data
    function remove()
    {
        $db = eZDB::instance();
        $db->begin();

	$node_id = $this->NodeID;

        $rows = $db->arrayQuery( "select id from ezsurveyresult where survey_id=".$this->ID );
        $results = false;
        foreach( $rows as $row )
        {
            if ( $results == false )
            {
                $resultIDString = '('.$row['id'];
                $results = true;
            }
            else
                $resultIDString .= ', '.$row['id'];
        }
        if ( $results )
            $resultIDString .= ')';

        $db->query( "delete from ezsurvey where id=".$this->ID );
        $db->query( "delete from ezsurveyquestion where survey_id=".$this->ID );
        if ( $results )
        {
            $db->query( "delete from ezsurveyresult where survey_id=".$this->ID );
            $db->query( "delete from ezsurveyquestionresult where result_id in ".$resultIDString );
            $db->query( "delete from ezsurveymetadata where result_id in ".$resultIDString );
        }

  	$object = eZContentObject::fetchByNodeID($node_id);

	if($object){

	   $object_id = $object->attribute('id');

	   $object->removeReverseRelations($object_id);

	   eZContentObjectTreeNode::removeSubtrees(array($node_id),false);

	}

        $db->commit();
    }

    function &questionTypes()
    {
        $list = eZSurveyQuestion::listQuestionTypes();
        foreach( array_keys( $list ) as $index )
        {
            if ( $list[$index]['max_one_instance'] == true )
            {
                if ( $list[$index]['count'] > 0 )
                    unset( $list[$index] );
            }
        }
        return $list;
    }

    // private
    function &dateTimeArray( $tstamp )
    {
        $noLimit = false;
        if ( $tstamp <= 0 )
        {
            $noLimit = true;
            $tstamp = -$tstamp;
        }
        return array(
          "no_limit" => $noLimit,
          "year" => date( "Y", $tstamp ),
          "month" => date( "m", $tstamp ),
          "day" => date( "d", $tstamp ),
          "hour" => date( "H", $tstamp ),
          "minute" => date( "i", $tstamp )
        );
    }

    function &validFromArray()
    {
        return $this->dateTimeArray( $this->ValidFrom );
    }

    function &validToArray()
    {
        return $this->dateTimeArray( $this->ValidTo );
    }

    function store(){

	     if($this->NodeID==0){

	        parent::store();

                $identifier = 'survey';

                $class =& eZContentClass::fetchByIdentifier($identifier);

                $class_id = $class->attribute('id');

                $ini =& eZINI::instance('ezsurvey.ini');

                $path_node_id = $ini->variable('PathNodeIDSettings','PathNodeID');

                $p_node_id = $path_node_id[count($path_node_id)-1];

                $p_node =& eZContentObjectTreeNode::fetch($p_node_id);

                $p_object =& $p_node->attribute('object');

                $user =& eZUser::currentUser();

                $user_id =& $user->attribute('contentobject_id');

                $section_id = $p_object->attribute('section_id');

                $db =& ezDB::instance();

                $db->begin();

                $object =& $class->instantiate($user_id,$section_id);

                $attributes  =& $object->contentObjectAttributes(
			                 true,
                                         $object->attribute('current_version'),
				         null,
				         false);
    
                foreach ( array_keys( $attributes ) as $key ){

                          $attribute =& $attributes[$key];

	                  if($attribute->attribute('contentclass_attribute_identifier')=='survey_number'){

			     $attribute->setAttribute('data_int',$this->ID);

		             $attribute->store();

	                  }

	                  if($attribute->attribute('contentclass_attribute_identifier')=='survey_name'){

			     $survey_name = $this->Title==''?'Survey no. '.$this->ID:$this->Title;

			     $attribute->setAttribute('data_text',$survey_name);

		             $attribute->store();
	                  }

                }

		$object->store();

                $node_assign =& eZNodeAssignment::create(
			         array('contentobject_id' => $object->attribute('id'),
                                       'contentobject_version' => $object->attribute( 'current_version' ),
				       'parent_node' => $p_node->attribute( 'node_id' ),
				       'is_main' => 1)
		                 );


                $node_assign->store();

                $db->commit();

                eZContentOperationCollection::publishNode(
			                      $node_assign->attribute('parent_node'), 
                                              $object->attribute('id'),
					      $object->attribute('current_version'),
					      null);

                $this->NodeID = $object->attribute('main_node_id');

		parent::store();

	     }else{

		$node =& eZContentObjectTreeNode::fetch($this->NodeID);

                $object =& $node->attribute('object');

                $attributes  =& $object->contentObjectAttributes(
			                 true,
                                         $object->attribute('current_version'),
				         null,
				         false);

                $db =& ezDB::instance();

                $db->begin();

                foreach ( array_keys( $attributes ) as $key ){

                          $attribute =& $attributes[$key];

	                  if($attribute->attribute('contentclass_attribute_identifier')=='survey_number'){

			     $attribute->setAttribute('data_int',$this->ID);

		             $attribute->store();

	                  }


	                  if($attribute->attribute('contentclass_attribute_identifier')=='survey_name'){

				  $survey_name = $this->Title==''?'Survey no. '.$this->ID:$this->Title;

				  $attribute->setAttribute('data_text',$survey_name);

		                  $attribute->store();
	                  }

                }

                $db->commit();

                eZContentOperationCollection::publishNode($node->attribute('parent_node_id'), 
                                              $object->attribute('id'),
					      $object->attribute('current_version'),
					      $this->NodeID);

                parent::store();

	   }

    }

    var $ID;
    var $Title;
    var $Enabled;
    var $Published;
    var $QuestionList;
    var $ValidFrom;
    var $ValidTo;
    var $RedirectCancel;
    var $RedirectSubmit;
    var $NodeID = 0;

}

?>
