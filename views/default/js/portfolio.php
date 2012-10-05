<?php
/**
 * Tag Dashboards JS Portfolio Library
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */ 
?>
//<script>
elgg.provide('elgg.portfolio');

// Init function
elgg.portfolio.init = function () {
	// Click handler for portfolio add 
	$(document).delegate('.portfolio-add', 'click', elgg.portfolio.addClick);

	// Click handler for porftolio recommend
	$(document).delegate('.portfolio-recommend', 'click', elgg.portfolio.recommendClick);

	// Click handler for profile add to portfolio
	$(document).delegate('.portfolio-add-profile', 'click', elgg.portfolio.profileAddClick);

	// Click handler for profile add to portfolio
	$(document).delegate('.portfolio-ignore-profile', 'click', elgg.portfolio.profileIgnoreClick);

	// Click handler for yearbook tag 
	$(document).delegate('.yearbook-tag', 'click', elgg.portfolio.yearbookClick);	
	
	// Click handler for emag tag (Note: sticking this here for now..)
	$(document).delegate('.emag-tag', 'click', elgg.portfolio.emagClick);

	// Ajax load the portfolio content
	elgg.portfolio.loadContent();
}

// Click handler for portfolio add 
elgg.portfolio.addClick = function(event) {
	if (!$(this).hasClass('disabled')) {
		// href will be #{guid}
		var entity_guid = $(this).attr('href').substring(1);

		$(this).addClass('disabled');

		$_this = $(this);

		elgg.action('portfolio/add', {
			data: {
				guid: entity_guid,
			},
			success: function(data) {
				if (data.status != -1) {
					// Try to add tag to the entity info block
					var $tag_item = $(document.createElement('li'));
					var $tag = $(document.createElement('a'));
					$tag.attr('href', elgg.get_site_url() + 'tagdashboards/add/#' + 'portfolio');
					$tag.attr('rel', 'tag');
					$tag.text('portfolio');
					$tag.appendTo($tag_item);

					var $elgg_body = $_this.closest('.elgg-body');

					var $tags = $elgg_body.find('ul.elgg-tags');

					// If we have a tag container, add the tag
					if ($tags.length != 0) {
						$tag_item.css('display', 'none');
						$tag_item.appendTo($tags);
						$tag_item.fadeIn();
					} else {
						// No tag container make a new tag div and try to add it 
						// after the elgg-subtext div 
						var $tag_div = $(document.createElement('div'));
						$tag_div.css('display', 'none');

						var $icon_span = $(document.createElement('span'));
						$icon_span.attr('class', 'elgg-icon elgg-icon-tag');
						$icon_span.appendTo($tag_div);

						var $tag_ul = $(document.createElement('ul'));
						$tag_ul.attr('class', 'elgg-tags');
						$tag_ul.appendTo($tag_div);

						$tag_item.appendTo($tag_ul);

						$elgg_body.find('div.elgg-subtext').after($tag_div);
						$tag_div.fadeIn();
					}

					// Remove link from actions menu
					elgg.portfolio.removeFromMenu($_this.attr('id'));
				} else {
					// Error
					$_this.removeClass('disabled');
				}
			}
		});
	}
	event.preventDefault();
}

// Click handler for portfolio recommend
elgg.portfolio.recommendClick = function(event) {
	if (!$(this).hasClass('disabled')) {
		// href will be #{guid}
		var entity_guid = $(this).attr('href').substring(1);
	
		$(this).addClass('disabled');

		$_this = $(this);

		elgg.action('portfolio/recommend', {
			data: {
				guid: entity_guid,
			},
			success: function(data) {
				if (data.status != -1) {
					// Remove link from actions menu
					elgg.portfolio.removeFromMenu($_this.attr('id'));
				} else {
					// Error
					$_this.removeClass('disabled');
				}
			}
		});
	}
	event.preventDefault();
}

// Click handler for profile portfolio add 
elgg.portfolio.profileAddClick = function(event) {
	if (!$(this).hasClass('disabled')) {
		// href will be #{guid}
		var entity_guid = $(this).attr('href').substring(1);

		$(this).addClass('disabled');

		$_this = $(this);

		elgg.action('portfolio/add', {
			data: {
				guid: entity_guid,
				remove_recommended: 1, // Remove the recommended metadata as well
			},
			success: function(data) {
				if (data.status != -1) {
					// Refresh module
					elgg.modules.genericmodule.init();
					
					// Re-load content
					elgg.portfolio.loadContent();

					// Update count for recommended button
					if (data.output.count !== null) {
						$('#portfolio-recommended-count').html(data.output.count);
					}
				} else {
					// Error
					$_this.removeClass('disabled');
				}
			}
		});
	}
	event.preventDefault();
}

// Click handler for profile portfolio ignore 
elgg.portfolio.profileIgnoreClick = function(event) {
	if (!$(this).hasClass('disabled')) {
		// href will be #{guid}
		var entity_guid = $(this).attr('href').substring(1);

		$(this).addClass('disabled');

		$_this = $(this);

		elgg.action('portfolio/recommend', {
			data: {
				guid: entity_guid,
				remove: 1, // Remove the recommended metadata
			},
			success: function(data) {
				if (data.status != -1) {
					// Refresh recommended module
					elgg.modules.genericmodule.init();
					
					// Update count for recommended button
					if (data.output.count !== null) {
						$('#portfolio-recommended-count').html(data.output.count);
					}
				} else {
					// Error
					$_this.removeClass('disabled');
				}
			}
		});
	}
	event.preventDefault();
}

// Click handler for yearbook tag 
elgg.portfolio.yearbookClick = function(event) {	
	if (!$(this).hasClass('disabled')) {
		// href will be #{guid}
		var entity_guid = $(this).attr('href').substring(1);
		elgg.portfolio.tagEntity($(this), entity_guid, 'yearbook');
	}
	event.preventDefault();
}

// Click handler for yearbook tag 
elgg.portfolio.emagClick = function(event) {	
	if (!$(this).hasClass('disabled')) {
		// href will be #{guid}
		var entity_guid = $(this).attr('href').substring(1);
		elgg.portfolio.tagEntity($(this), entity_guid, 'emagazine');
	}
	event.preventDefault();
}

// Helper function to tag an entity with given tag
elgg.portfolio.tagEntity = function($sender, guid, tag) {
	$sender.addClass('disabled');
	
	elgg.action('tagdashboards/tag', {
		data: {
			guid: guid,
			tag: tag,
			ignore_access: true,
		},
		success: function(data) {
			if (data.status != -1) {
				// Try to add tag to the entity info block
				var $tag_item = $(document.createElement('li'));
				var $tag = $(document.createElement('a'));
				$tag.attr('href', elgg.get_site_url() + 'tagdashboards/add/#' + tag);
				$tag.attr('rel', 'tag');
				$tag.text(tag);
				$tag.appendTo($tag_item);
				
				$entity_anchor = $(document).find('#entity-anchor-' + guid);

				var $elgg_body = $entity_anchor.closest('.elgg-body');

				var $tags = $elgg_body.find('ul.elgg-tags');

				// If we have a tag container, add the tag
				if ($tags.length != 0) {
					$tag_item.css('display', 'none');
					$tag_item.appendTo($tags);
					$tag_item.fadeIn();
				} else {
					// No tag container make a new tag div and try to add it 
					// after the elgg-subtext div 
					var $tag_div = $(document.createElement('div'));
					$tag_div.css('display', 'none');

					var $icon_span = $(document.createElement('span'));
					$icon_span.attr('class', 'elgg-icon elgg-icon-tag');
					$icon_span.appendTo($tag_div);

					var $tag_ul = $(document.createElement('ul'));
					$tag_ul.attr('class', 'elgg-tags');
					$tag_ul.appendTo($tag_div);

					$tag_item.appendTo($tag_ul);

					$elgg_body.find('div.elgg-subtext').after($tag_div);
					$tag_div.fadeIn();
				}
			
				// Remove link from actions menu
				elgg.portfolio.removeFromMenu($sender.attr('id'));
			} else {
				// Error
				$sender.removeClass('disabled');
			}
		}
	});
}

/**	
 * Load portfolio content 
 */
elgg.portfolio.loadContent = function() {
	var $container = $('#tagdashboards-portfolio-container');

	// Don't load unless we have the container
	if ($container.length) {
		var user_guid = $('input#portfolio-user').val();
		var url = elgg.get_site_url() + 'ajax/view/tagdashboards/portfolio/content'


		elgg.get(url, {
			data: {
				user_guid: user_guid
			},
			success: function(data){
				$container.html(data);
			}
		});
	}
}

/**
 * Helper function to remove a link from the entity menu
 *
 * @param {String} id Link id to remove
 *
 * @return {void}
 */
elgg.portfolio.removeFromMenu = function(id) {
	var $link = $('#' + id);
	var width = $link.parent().outerWidth(true);
	$menu = $link.closest('.tgstheme-entity-menu-actions');
	$menu.width($menu.width() - width + 2); // +2 Extra pixels, to fix weirdness
	$menu.css('left', $menu.position().left + width - 2);
	$link.parent().remove();
}

/**
 * Repositions the recommended popup
 *
 * @param {String} hook    'getOptions'
 * @param {String} type    'ui.popup'
 * @param {Object} params  An array of info about the target and source.
 * @param {Object} options Options to pass to
 *
 * @return {Object}
 */
elgg.portfolio.recommendedHandler = function(hook, type, params, options) {
	if (params.target.attr('id') == 'tagdashboards-recommended-dropdown') {
		options.my = 'right top';
		options.at = 'right bottom';
		return options;
	}
	return null;
};

elgg.register_hook_handler('getOptions', 'ui.popup', elgg.portfolio.recommendedHandler);
elgg.register_hook_handler('init', 'system', elgg.portfolio.init);