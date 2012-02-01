<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('ModEasyBlogLatestPostImgUriPath')) {
    define('ModEasyBlogLatestPostImgUriPath', JURI::root() . 'modules/mod_easybloglatestpost/assets/');
}

$path	= JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'helpers' . DS . 'helper.php';

jimport( 'joomla.filesystem.file' );

if( !JFile::exists( $path ) )
{
	return;
}

require_once( $path );
require_once( dirname(__FILE__).DS.'helper.php' );

JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

JTable::addIncludePath( EBLOG_TABLES );
EasyBlogHelper::loadModuleCss();

$config         	=& EasyblogHelper::getConfig();
$posts				= modEasyBlogLatestPostHelper::getLatestPost($params);
$easyblogInstalled 	= true;
$textcount 			= $params->get( 'textcount' , 200 );

$language			= JFactory::getLanguage();
$language->load( 'com_easyblog' , JPATH_ROOT );

$document			=& JFactory::getDocument();
$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/css/module.css' );

if($params->get( 'showfeatureimage' , 1 ))
{
	for( $i = 0; $i < count( $posts ); $i++ )
	{
		$posts[$i]->featuredImage = modEasyBlogLatestPostHelper::getFeaturedImage( $posts[ $i ]->intro . $posts[$i]->content );
	}
}

require( JModuleHelper::getLayoutPath( 'mod_easybloglatestpost' ) );