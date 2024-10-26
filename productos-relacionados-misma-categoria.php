<?php
/**
 * Plugin Name: Productos Relacionados Misma Categoría
 * Description: Muestra productos relacionados solo de la misma categoría.
 * Version: 3.0
 * Author: Nxvermore
 */

function mostrar_productos_relacionados_misma_categoria( $related_products, $product_id ) {
    $product = wc_get_product( $product_id );

    if ( ! $product ) {
        return $related_products;
    }

    $categories = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

    if ( empty( $categories ) ) {
        return $related_products;
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post__not_in'   => array( $product_id ),
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $categories,
                'operator' => 'IN',
            ),
        ),
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        $related_products = wp_list_pluck( $query->posts, 'ID' );
    }

    wp_reset_postdata();

    return $related_products;
}
add_filter( 'woocommerce_related_products', 'mostrar_productos_relacionados_misma_categoria', 10, 2 );
