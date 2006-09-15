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

/*! \file module.php
*/

$Module = array( 'name' => 'Survey' );

$ViewList = array();
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'administration' ),
    'params' => array ( ) );
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ) );
$ViewList['copy'] = array(
    'script' => 'copy.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ) );
$ViewList['preview'] = array(
    'script' => 'preview.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ) );
$ViewList['view'] = array(
    'script' => 'view.php',
    'functions' => array( 'filling' ),
    'params' => array( 'SurveyID' ) );
$ViewList['result'] = array(
    'script' => 'result.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ) );
$ViewList['rview'] = array(
    'script' => 'rview.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );
$ViewList['result_list'] = array(
    'script' => 'result_list.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );
$ViewList['result_edit'] = array(
    'script' => 'result_edit.php',
    'functions' => array( 'administration' ),
    'params' => array( 'ResultID' ) );
$ViewList['remove'] = array(
    'script' => 'remove.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ) );
$ViewList['export'] = array(
    'script' => 'export.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ) );

$FunctionList['administration'] = array( );
$FunctionList['filling'] = array( );

?>
