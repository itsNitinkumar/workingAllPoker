/**
 * Script
 *
 * @author Shahzaib
 */

"use strict";

$( function ()
{
  
  // Ajax requests handling:
  $( document ).on( 'submit', '.z-form', function ( event )
  {
    event.preventDefault();
    formAjaxRequest( $( this ) );
  });
  
  
  // Pay with credit item data management for related modal:
  $( '.tool.pwc' ).on( 'click', function ()
  {
    $( '#item-main-title' ).text( $( this ).attr( 'data-title' ) );
    $( '#item-sub-title' ).text( '(' + $( this ).attr( 'data-type' ) + ')' );
    $( '[name="price"]' ).val( $( this ).attr( 'data-price' ) );
  });
  
  
  // Send email to user modal management:
  $( '.seu-tool' ).on( 'click', function()
  {
    $( '#seu-email' ).val( $( this ).attr( 'data-email' ) );
  });
  
  $( '#send-email-user' ).on( 'hidden.bs.modal', function ()
  {
    $( '.textarea' ).summernote( 'reset' );
    resetForm( $( this ).find( 'form' ) );
  })
  
  
  // Manage requestor modals without sending the ajax request:
  $( '.z-table, .z-card' ).on( 'click', function ( event )
  {
    var $element = $( event.target );
    var isFine   = true;
    
    /**
     * The element you want to use to set the record ID for the modal form,
     * must have a class "tool". If the setter element is the child of "tool"
     * class, add the "tool-c" class also to the child element.
     *
     * The element that is having "tool" class, must have these attributes:
     * "data-target" Modal ID e.g. delete
     * "data-id" Record ID
     */
    
    if ( $element.hasClass( 'tool-c' ) )
    {
      $element = $element.parent( '.tool' );
    }
    else
    {
      if ( ! $element.hasClass( 'tool' ) )
      {
        isFine = false;
      }
    }
    
    if ( isFine === true )
    {
      var $modal = $( $element.attr( 'data-target' ) );
      var dataID = $element.attr( 'data-id' );
      
      $modal.find( '[name="id"]' ).val( dataID );
    }
  });
  
  
  // Google Analytics:
  if ( typeof googleAnalyticsID !== 'undefined' )
  {
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push( arguments ); }
    gtag( 'js', new Date() );

    gtag( 'config', googleAnalyticsID );
  }
  
  
  // Cookie Popup:
  if ( $.isFunction( $.cookie ) )
  {
    $( '.accept-btn' ).on( 'click', function()
    {
      $( '.cookie-popup' ).css( 'display', 'none' );
      $.cookie( 'z_accepted', true, { expires: 365, path: '/' } );
    });
    
    if ( $.cookie( 'z_accepted' ) == null )
    {
      $( '.cookie-popup' ).css( 'display', 'block' );
    }
  }
  
  
  // On modal shown, clear extra:
  $( window ).on( 'shown.bs.modal', function ()
  {
    resetResponseMessages();
  });
});


$( window ).on( 'load', function ()
{
  // Make pay modal button activated on the page is fully loaded:
  $( '.btn.pay-modal' ).removeAttr( 'disabled' );
});