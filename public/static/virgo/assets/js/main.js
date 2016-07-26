$(function() {
  'use strict';

  // Tab
  var $tabList = $('.tab__list'),
      $tabContentItem = $('.tab__content--item');

  $tabContentItem.hide().filter(':first').show();

  $tabList.find('a').on('click', function(event) {
    event.preventDefault();
    var $self = $(this),
        hash = this.hash;

    $tabList.find('a').removeClass('active');
    $self.addClass('active');
    $tabContentItem.hide().filter(hash).fadeIn(300);
  });

  // Thumbnail
  $('#thumbnail').find('a').on('click', function(event) {
    event.preventDefault();
    var $self = $(this),
        $selfImg = $self.find('img').attr('src'),
        $thumb = $self.parents('.thumbnail').prev();

    $thumb.attr('src', $selfImg);
  });

  // Dropdown
  $('#dropdown').on('click', function(event) {
    event.preventDefault();
    var $self = $(this);
    var $selfLink = $self.children('.header__nav--link');
    var $selfChildren = $self.children('.header__nav--child');

    $selfLink.toggleClass('active');
    $selfChildren.toggleClass('is-visible');
  });

  $(document).on('mouseup', function(event) {
    var $dropdown = $('#dropdown');
    var $dropdownLink = $dropdown.children('.header__nav--link');
    var $dropdownChildren = $dropdown.children('.header__nav--child');

    if ($dropdown.has(event.target).length === 0) {
      $dropdownLink.removeClass('active');
      $dropdownChildren.removeClass('is-visible');
    }
  });
});