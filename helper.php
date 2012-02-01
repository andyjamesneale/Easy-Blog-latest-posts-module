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

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'models'.DS.'tags.php');
require_once(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'helper.php');
require_once(EBLOG_CLASSES . DS . 'simpleimage.php' );
jimport('joomla.system.file');
jimport('joomla.system.folder');

class modEasyBlogLatestPostHelper
{
	function getLatestPost(&$params)
	{
		$db 			= &JFactory::getDBO();
		$config			= EasyBlogHelper::getConfig();
		$count			= (int) $params->get( 'count' , 0 );

		if( !class_exists( 'EasyBlogModelBlog' ) )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'blog' , EBLOG_ROOT . DS . 'models' );
		}

		$model = JModel::getInstance( 'Blog' , 'EasyBlogModel' );


		if( $params->get( 'usefeatured' ) )
		{
			$posts = $model->getFeaturedBlog( array() , $count );
		}
		else
		{
			$categories	= $params->get( 'catid' );
			$categories = trim( $categories );
			$type = !empty( $categories ) ? 'category' : '';

			if( !empty( $categories ) )
			{
				$categories = explode( ',' , $categories );
			}

			$cid		= $categories;

			$posts		= $model->getBlogsBy( $type , $cid , 'latest' , $count , EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, true, array(), $categories );
		}

		if(count($posts) > 0)
		{
			for($i = 0; $i < count($posts); $i++ )
			{
				$row =& $posts[$i];

				// strip off media
				$row->intro		= EasyBlogHelper::removeGallery( $row->intro );
				$row->content	= EasyBlogHelper::removeGallery( $row->content );
				$row->intro		= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->intro );
				$row->content	= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->content );

				$row->commentCount 	= EasyBlogHelper::getCommentCount($row->id);

				JTable::addIncludePath( EBLOG_TABLES );
				$author 			=& EasyBlogHelper::getTable( 'Profile', 'Table' );
				$row->author		= $author->load( $row->created_by );
				$row->date			= EasyBlogDateHelper::toFormat( JFactory::getDate( $row->created ) , $config->get('layout_dateformat', '%A, %d %B %Y') );

				$requireVerification = false;
				if($config->get('main_password_protect', true) && !empty($row->blogpassword))
				{
					$row->title	= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $row->title);
					$requireVerification = true;
				}

				if($requireVerification && !EasyBlogHelper::verifyBlogPassword($row->blogpassword, $row->id))
				{
					$theme = new CodeThemes();
					$theme->set('id', $row->id);
					$theme->set('return', base64_encode(EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id)));
					$row->intro			= $theme->fetch( 'blog.protected.php' );
					$row->content		= $row->intro;
					$row->showRating	= false;
					$row->protect		= true;
				}
				else
				{
					$row->showRating	= true;
					$row->protect		= false;
				}
			}
		}

		return $posts;
	}

	function getFeaturedImage( $content )
	{
		$pattern = '#<img class="featured"[^>]*>#i';

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return modEasyBlogLatestPostHelper::getResizedImage($matches[0]);
		}

		// If featured image is not supplied, try to use the first image as the featured post.
		$pattern				= '#<img[^>]*>#i';

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return modEasyBlogLatestPostHelper::getResizedImage($matches[0]);
		}

		// If all else fail, try to use the default image
		return false;
	}

	function getResizedImage( $img )
	{
	    $img		= JString::str_ireplace( 'img ' , 'img ' , $img );
	    return $img;
	}

	function getThumbnailImage($img)
	{
		$srcpattern = '/src=".*?"/';

		preg_match( $srcpattern , $img , $src );

		if(isset($src[0]))
		{
			$imagepath	= trim(str_ireplace('src=', '', $src[0]) , '"');
			$segment 	= explode('/', $imagepath);
			$file 		= end($segment);
			$thumbnailpath = str_ireplace($file, 'thumb_'.$file, implode('/', $segment));

			if(!JFile::exists($thumbnailpath))
			{
				$image = new SimpleImage();
				$image->load($imagepath);
				$image->resize(64, 64);
				$image->save($thumbnailpath);
			}

			$newSrc = 'src="'.$thumbnailpath.'"';
		}
		else
		{
			return false;
		}

		$oldAttributes = array('src'=>$srcpattern, 'width'=>'/width=".*?"/', 'height'=>'/height=".*?"/');
		$newAttributes = array('src'=>$newSrc,'width'=>'', 'height'=>'');

		return preg_replace($oldAttributes, $newAttributes, $img);
	}
}
