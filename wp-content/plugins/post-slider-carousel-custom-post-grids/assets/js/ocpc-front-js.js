jQuery(document).ready(function () {
    var $container = jQuery(".ocpc_insta_masonry_grid");

    $container.imagesLoaded(function () {
        $container.masonry();
    });
});