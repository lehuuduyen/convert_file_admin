jQuery(function ($) {
  function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
      sURLVariables = sPageURL.split("&"),
      sParameterName,
      i;

    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split("=");

      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined
          ? true
          : decodeURIComponent(sParameterName[1]);
      }
    }
    return false;
  }
  let id = getUrlParameter("post");


  
  $('li.wp-has-current-submenu').addClass('wp-not-current-submenu').removeClass('wp-has-current-submenu');
  $('a.wp-has-current-submenu').addClass('wp-not-current-submenu').removeClass('wp-has-current-submenu');
  $('li#toplevel_page_post-post-'+id+'-action-edit').addClass('wp-has-current-submenu').removeClass('wp-not-current-submenu');
  $('li#toplevel_page_post-post-'+id+'-action-edit > a').addClass('wp-has-current-submenu').removeClass('wp-not-current-submenu');


  //Banner large: Maximum 2 banner
  // $(document).on(
  //   "click",
  //   '[data-selector="page_about_bannder_ads_large_group_repeat"]',
  //   function (event) {
  //     var bannerLarge = $(
  //       "#page_about_bannder_ads_large_group_repeat .cmb-repeatable-grouping"
  //     ).length;
  //     if (bannerLarge > 1) {
  //       $("#page_about_bannder_ads_large_group_repeat .cmb-add-group-row").hide();
  //     } else {
  //       $("#page_about_bannder_ads_large_group_repeat .cmb-add-group-row").show();
  //     }
  //   }
  // );

  $(document).on("click", "#publish", function (event) {
    event.preventDefault();
  
    $("form#post").submit();

  });
});
