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
?>
<div id="ezblog-latestpost" class="ezb-mod ezblog-latestpost<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<?php if( $posts ){ ?>
	<ul id="latest-posts" class="entries ezul ezbli">
	<?php foreach( $posts as $post ){ ?>
		<li>
			<?php if( $params->get( 'author_position' , 'bottom') == 'top' ){ ?>
				<?php if( $params->get( 'showauthor' ) ){ ?>
					<div class="post-author-info ezcf ezmts ezpts ezsp">
						<?php if( $params->get( 'showavatar', true ) ){ ?>
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $post->author->id ); ?>" class="post-avatar ezfl ezmrs" alt="<?php echo $post->author->getName(); ?>"><img src="<?php echo $post->author->getAvatar();?>" width="30" title="<?php echo $post->author->getName(); ?>" class="ezavatar" /></a>
						<?php } ?>
						<div class="post-author eztc">
							<?php echo JText::sprintf('MOD_EASYBLOGLATESTPOST_POSTED_BY', '<a href="' . EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $post->author->id ) . '">' . $post->author->getName() . '</a>' , $post->date ); ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>

            <div class="post-head ezcf">
    			<?php if($params->get( 'showfeatureimage' , 1 )) { ?>
    			<div class="post-image ezfl ezmrs">
    				<?php if( $post->featuredImage ) { ?>
          				<?php echo $post->featuredImage;?>
          			<?php } ?>
    			</div>
    			<?php } ?>
                <div class="eztc">
                    <a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $post->id );?>"><?php echo $post->title;?></a>
                </div>
				<?php if( $params->get( 'showcontent' ) ){ ?>
				<div class="post-content ezmts">
				<?php
					if($post->protect)
					{
						echo  $post->content;
					}
					else
					{
						$summary = ($params->get( 'showintro' ) == '0')? strip_tags( $post->intro ) : strip_tags( $post->content );

						if( empty( $post->content) )
						{
							$summary	= strip_tags( $post->intro );
						}

						if( $textcount == 0 )
							echo $summary;
						else
							echo JString::substr( strip_tags( $summary ) , 0 , $textcount );
					}
					?>
				</div>
				<?php } ?>
            </div>

			<div class="post-meta small ezpts">
				<?php $url = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $post->id ); ?>
				<?php if($params->get('showcommentcount', 0)) : ?>
				<a href="<?php echo $url; ?>" class="post-comments"><?php echo JText::sprintf( 'MOD_EASYBLOGLATESTPOST_COUNT', $post->commentCount );?></a>
			    <b>&middot;</b>
				<?php endif; ?>
			    <?php if( $params->get( 'showhits' , true ) ): ?>
				<a href="<?php echo $url;?>"><?php echo JText::sprintf( 'MOD_EASYBLOGLATESTPOST_HITS' , $post->hits );?></a>
				<b>&middot;</b>
				<?php endif; ?>
				<?php if( $params->get( 'showreadmore' , true ) ): ?>
				<a href="<?php echo $url; ?>" class="post-more"><?php echo JText::_('MOD_EASYBLOGLATESTPOST_READMORE'); ?></a>
				<?php endif; ?>
			</div>
			
			<?php if( $params->get( 'showratings', true ) && $post->showRating ): ?>
			<div class="blog-rating small"><?php echo EasyBlogHelper::getHelper( 'ratings' )->getHTML( $post->id , 'entry' , JText::_( 'MOD_EASYBLOGLATESTPOST_RATEBLOG' ) , 'mod-latestpost-' . $post->id );?></div>
			<?php endif; ?>
			
			<?php if( $params->get( 'author_position' , 'bottom') == 'bottom' ){ ?>
				<?php if( $params->get( 'showauthor' ) ){ ?>
					<div class="post-author-info ezcf ezmts ezpts ezsp">
						<?php if( $params->get( 'showavatar', true ) ){ ?>
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $post->author->id ); ?>" class="post-avatar ezfl ezmrs" alt="<?php echo $post->author->getName(); ?>"><img src="<?php echo $post->author->getAvatar();?>" width="30" title="<?php echo $post->author->getName(); ?>" class="ezavatar" /></a>
						<?php } ?>
						<div class="post-author eztc">
							<?php echo JText::sprintf('MOD_EASYBLOGLATESTPOST_POSTED_BY', '<a href="' . EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $post->author->id ) . '">' . $post->author->getName() . '</a>' , $post->date ); ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		</li>
	<?php } ?>
	</ul>
<?php } else { ?>
	<?php echo JText::_('MOD_EASYBLOGLATESTPOST_NO_POST'); ?>
<?php } ?>
</div>

