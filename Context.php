<?php

namespace wpscholar\WordPress;

/**
 * Context based on WordPress' built-in template hierarchy.
 *
 * See the following link for additional info on the WordPress template hierarchy.
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * Class Context
 * @package wpscholar\WordPress
 */
class Context {

	/**
	 * Fetches the template context based on the current page.
	 *
	 * @return array
	 */
	public static function getContext() {
		$context = [];
		if ( is_front_page() ) {
			$context = self::getFrontPageContext();
		} elseif ( is_home() ) {
			$context = self::getHomeContext();
		} elseif ( is_singular() ) {
			$context = self::getSingularContext();
		} elseif ( is_archive() ) {
			$context = self::getArchiveContext();
		} elseif ( is_search() ) {
			$context = self::getSearchContext();
		} elseif ( is_404() ) {
			$context = self::get404Context();
		}

		return $context;
	}

	/**
	 * Context for 404 error pages.
	 *
	 * @return array
	 */
	protected static function get404Context() {
		return [ '404' ];
	}

	/**
	 * Context for archive pages, detects the type of archive page.
	 *
	 * @return array
	 */
	protected static function getArchiveContext() {
		$context = [];

		if ( is_tax() ) {
			$context = self::getTaxonomyArchiveContext();
		} elseif ( is_category() ) {
			$context = self::getCategoryArchiveContext();
		} elseif ( is_tag() ) {
			$context = self::getTagArchiveContext();
		} elseif ( is_author() ) {
			$context = self::getAuthorArchiveContext();
		} elseif ( is_date() ) {
			$context = self::getDateArchiveContext();
		} elseif ( is_post_type_archive() ) {
			$context = self::getPostTypeArchiveContext();
		}

		$context[] = 'archive';

		return $context;
	}

	/**
	 * Context for media library attachment pages.
	 *
	 * @return array
	 */
	protected static function getAttachmentContext() {
		$context = [];
		$attachment = get_post();
		if ( ! empty( $attachment->post_mime_type ) ) {
			$type = explode( '/', $attachment->post_mime_type );
			if ( isset( $type[1] ) ) {
				$context[] = "{$type[0]}-{$type[1]}";
				$context[] = $type[1];
			}
			$context[] = $type[0];
		}
		$context[] = 'attachment';
		$context[] = 'single-attachment-' . sanitize_html_class( $attachment->post_name );
		$context[] = 'single-attachment';
		$context[] = 'single';

		return $context;
	}

	/**
	 * Context for author archive pages.
	 *
	 * @return array
	 */
	protected static function getAuthorArchiveContext() {
		/* @var \WP_User $user */
		$user = get_queried_object();

		return [
			'author-' . sanitize_html_class( $user->user_nicename ),
			"author-{$user->ID}",
			'author',
		];
	}

	/**
	 * Context for category taxonomy and term archives.
	 *
	 * @return array
	 */
	protected static function getCategoryArchiveContext() {
		/* @var \WP_Term $term */
		$term = get_queried_object();

		return [
			'category-' . sanitize_html_class( $term->slug ),
			"category-{$term->term_id}",
			'category',
		];
	}

	/**
	 * Context for date-based archives.
	 *
	 * @return array
	 */
	protected static function getDateArchiveContext() {
		return [ 'date' ];
	}

	/**
	 * Context for the site front-page. Can be page for posts if the front-page is the page for posts.
	 *
	 * @return array
	 */
	protected static function getFrontPageContext() {
		$context = [ 'front-page' ];
		if ( is_home() ) {
			array_push( $context, ...self::getHomeContext() );
		} else if ( is_page() ) {
			array_push( $context, ...self::getPageContext() );
		}

		return $context;
	}

	/**
	 * Context for the page for posts (home, in WordPress template speak).
	 *
	 * @return array
	 */
	protected static function getHomeContext() {
		return [ 'home' ];
	}

	/**
	 * Context for single pages.
	 *
	 * @return array
	 */
	protected static function getPageContext() {
		/* @var \WP_Post $page */
		$page = get_queried_object();
		$context = [];
		// Check if a custom template is in use.
		$template = get_page_template_slug( $page );
		if ( $template ) {
			$context[] = str_replace( '.php', '', $template );
		}
		$context[] = 'page-' . sanitize_html_class( $page->post_name );
		$context[] = "page-{$page->ID}";
		$context[] = 'page';

		return $context;
	}

	/**
	 * Context for custom post type archives.
	 *
	 * @return array
	 */
	protected static function getPostTypeArchiveContext() {
		$postType = get_query_var( 'post_type' );
		$context = [];
		if ( is_array( $postType ) ) {
			// Have some sort of context for archives where multiple post types are loaded.
			$context[] = 'archive-multi-post-type';
		} else {
			$context[] = "archive-{$postType}";
		}

		return $context;
	}

	/**
	 * Context for search result archives.
	 *
	 * @return array
	 */
	protected static function getSearchContext() {
		return [ 'search' ];
	}

	/**
	 * Context for single posts and custom post types.
	 *
	 * @return array
	 */
	protected static function getSingleContext() {
		$post = get_post();
		$context = [];
		// Check if a custom template is in use.
		$template = get_page_template_slug( $post );
		if ( $template ) {
			$context[] = str_replace( '.php', '', $template );
		}
		$context[] = "single-{$post->post_type}-" . sanitize_html_class( $post->post_name );
		$context[] = "single-{$post->post_type}";
		$context[] = 'single';

		return $context;
	}

	/**
	 * Context for singular pages; includes attachments, pages, posts, and custom post types.
	 *
	 * @return array
	 */
	protected static function getSingularContext() {
		$context = [];
		if ( is_attachment() ) {
			$context = self::getAttachmentContext();
		} elseif ( is_page() ) {
			$context = self::getPageContext();
		} elseif ( is_single() ) {
			$context = self::getSingleContext();
		}

		$context[] = 'singular';

		return $context;
	}

	/**
	 * Context for tag taxonomy and term archives.
	 *
	 * @return array
	 */
	protected static function getTagArchiveContext() {
		/* @var \WP_Term $term */
		$term = get_queried_object();

		return [
			'tag-' . sanitize_html_class( $term->slug ),
			"tag-{$term->term_id}",
			'tag',
		];
	}

	/**
	 * Context for custom taxonomy and custom taxonomy term archives.
	 *
	 * @return array
	 */
	protected static function getTaxonomyArchiveContext() {
		/* @var \WP_Term $term */
		$term = get_queried_object();

		return [
			"taxonomy-{$term->taxonomy}-" . sanitize_html_class( $term->slug ),
			"taxonomy-{$term->taxonomy}",
			'taxonomy',
		];
	}

}
