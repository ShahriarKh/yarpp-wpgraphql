<?php

/**
 * Plugin Name: YARPP WPGraphQL (forked)
 * Plugin URI: https://github.com/matepaiva/yarpp-wpgraphql
 * Version: 0.0.1
 * Author: Matheus Paiva (forked by Shahriar)
 * Author URI: https://github.com/matepaiva/
 * Description: Creates a relatedPosts field in Post type with wp-graphql. You must have installed wp-graphql and YARPP.
 * License: GPLv2 or later
 */


add_action('graphql_register_types', function () {
  global $yarpp;
  if ($yarpp) {
    \register_graphql_connection([
      'fromType' => 'course', /* was Post */
      'fromFieldName' => 'relatedPosts2',
      'toType' => 'post', /* was Post */
      'connectionTypeName' => 'CouresRelatedPosts',
      'connectionArgs' => [
        'limit' => [
          'name' => 'limit',
          'type' => 'Int',
          'description' => 'Override\'s YARPP setting\'s "Maximum number of related posts." The maximum number is 20.'
        ]
      ],
      'resolve' => function ($post, $args, $context, $info) {
        global $yarpp;
        $limit = isset($args['where']['limit']) ? $args['where']['limit'] : null;
        $related_posts = $yarpp->get_related($post->ID, $limit ? ['limit' => $limit] : null);
        $args['where']['in'] = array_map(function ($related_post) {
          return $related_post->ID;
        }, $related_posts);

        $resolver = new \WPGraphQL\Data\Connection\PostObjectConnectionResolver(null, $args, $context, $info, 'post'); // course was post
        $result = $resolver->get_connection();
        return $result;
      }
    ]);
  }
});

add_action('graphql_register_types', function () {
    global $yarpp;
    if ($yarpp) {
      \register_graphql_connection([
        'fromType' => 'course', /* was Post */
        'fromFieldName' => 'relatedCourses',
        'toType' => 'course', /* was Post */
        'connectionTypeName' => 'CourseRealtedCourses',
        'connectionArgs' => [
          'limit' => [
            'name' => 'limit',
            'type' => 'Int',
            'description' => 'Override\'s YARPP setting\'s "Maximum number of related posts." The maximum number is 20.'
          ]
        ],
        'resolve' => function ($post, $args, $context, $info) {
          global $yarpp;
          $limit = isset($args['where']['limit']) ? $args['where']['limit'] : null;
          $related_posts = $yarpp->get_related($post->ID, $limit ? ['limit' => $limit] : null);
          $args['where']['in'] = array_map(function ($related_post) {
            return $related_post->ID;
          }, $related_posts);
  
          $resolver = new \WPGraphQL\Data\Connection\PostObjectConnectionResolver(null, $args, $context, $info, 'course'); // course was post
          $result = $resolver->get_connection();
          return $result;
        }
      ]);
    }
  });
  
 add_action('graphql_register_types', function () {
  global $yarpp;
  if ($yarpp) {
    \register_graphql_connection([
      'fromType' => 'book', /* was Post */
      'fromFieldName' => 'relatedBooks',
      'toType' => 'book', /* was Post */
      'connectionTypeName' => 'BookRelatedBooks',
      'connectionArgs' => [
        'limit' => [
          'name' => 'limit',
          'type' => 'Int',
          'description' => 'Override\'s YARPP setting\'s "Maximum number of related posts." The maximum number is 20.'
        ]
      ],
      'resolve' => function ($post, $args, $context, $info) {
        global $yarpp;
        $limit = isset($args['where']['limit']) ? $args['where']['limit'] : null;
        $related_posts = $yarpp->get_related($post->ID, $limit ? ['limit' => $limit] : null);
        $args['where']['in'] = array_map(function ($related_post) {
          return $related_post->ID;
        }, $related_posts);

        $resolver = new \WPGraphQL\Data\Connection\PostObjectConnectionResolver(null, $args, $context, $info, 'book'); // course was post
        $result = $resolver->get_connection();
        return $result;
      }
    ]);
  }
});

function create_relateds_field($from, $to, $name) {
    global $yarpp;
    if ($yarpp) {
        \register_graphql_connection([
            'fromType' => $from,
            'fromFieldName' => $name,
            'toType' => $to,
            'connectionTypeName' => $from . $to . $name,
            'connectionArgs' => [
                'limit' => [
                  'name' => 'limit',
                  'type' => 'Int',
                  'description' => 'Override\'s YARPP setting\'s "Maximum number of related posts." The maximum number is 20.'
                ]
            ],
            'resolve' => function ($post, $args, $context, $info) use ($to, $yarpp) {
            $limit = isset($args['where']['limit']) ? $args['where']['limit'] : 4;
            $related_posts = $yarpp->get_related($post->ID,
              array(
                'limit' => $limit,  // limit is for display options?
                // 'weight' => array(
                //   'title' => 1,
                //   'tax' => array(
                //     'category' => 2
                //   )
                // ),
                'post_type' => $to,
                'threshold' => 5
              )
            );
            
            $args['where']['in'] = array_map(function ($related_post) {
              return $related_post->ID;
            }, $related_posts);
    
            $resolver = new \WPGraphQL\Data\Connection\PostObjectConnectionResolver(null, $args, $context, $info, $to);
            $result = $resolver->get_connection();
            return $result;
          }
        ]);
    };
};

add_action('graphql_register_types', function() {
    create_relateds_field('post', 'course', 'relatedCourses');
});

add_action('graphql_register_types', function() {
    create_relateds_field('post', 'book', 'relatedBooks');
});
