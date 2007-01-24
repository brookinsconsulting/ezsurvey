<?php
include_once( 'lib/ezutils/classes/ezcli.php' );
include_once( 'kernel/classes/ezscript.php' );
$cli =& eZCLI::instance();
$script =& eZScript::instance( array( 'description' => ( "Root node setup script\n" .
                                                         "creates a root node\n" .
                                                         "\n" .
                                                         "./extension/survey/bin/createrootnode.php -ssiteaccessname" ),
                                      'use-session' => true,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[admin-user:]",
                                "",
                                array( 'admin-user' => 'Admin user login name' ) );

$script->initialize();

include_once( 'kernel/classes/ezcontentobject.php' );
include_once( 'kernel/classes/ezsection.php' );

// login as admin
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
$user = eZUser::fetchByName( $options['admin-user'] );

if ( is_object( $user ) )
{
	if ( $user->loginCurrent() )
	   $cli->output( "Logged in as 'admin'" );
}
else
{
	$cli->error( 'No admin.' );
    $script->shutdown( 1 );
}
$section = new eZSection( array( 'name' => 'Survey', 'navigation_part_identifier' => 'ezsurveynavigationpart' ) );
$section->store();
$userClassID = 1;
$userCreatorID = 14;
$defaultSectionID = $section->ID;

$class = eZContentClass::fetch( $userClassID );
$contentObject = $class->instantiate( $userCreatorID, $defaultSectionID );


$contentObject->store();

$contentObjectID = $contentObject->attribute( 'id' );
$userID = $contentObjectID;
$nodeAssignment = eZNodeAssignment::create( array( 'contentobject_id' => $contentObjectID,
'contentobject_version' => 1,
'parent_node' => 1,
'is_main' => 1 ) );
$nodeAssignment->store();
$version =& $contentObject->version( 1 );
$version->setAttribute( 'modified', time() );
$version->setAttribute( 'status', EZ_VERSION_STATUS_DRAFT );
$version->store();

$contentObjectID = $contentObject->attribute( 'id' );
$contentObjectAttributes = $version->contentObjectAttributes();

$contentObjectAttributes[0]->setAttribute( 'data_text', 'Surveys' );
$contentObjectAttributes[0]->store();

include_once( 'lib/ezutils/classes/ezoperationhandler.php' );
$operationResult = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $contentObjectID,
'version' => 1 ) );
$publishedobj = eZContentObject::fetch( $contentObjectID );

$cli->output( "Node created with ObjectID #$contentObjectID and NodeID #".$publishedobj->attribute( 'main_node_id' ) );

return $script->shutdown();
?>