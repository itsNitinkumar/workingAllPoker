/**
 * Stripe Script
 *
 * @author Shahzaib
 */

"use strict";

$( function ()
{
  if ( typeof stripePublishableKey === 'undefined' || $( '#card-element' ).length == 0 ) return;
  
  var stripe     = Stripe( stripePublishableKey );
  var elements   = stripe.elements();
  var card       = elements.create( 'card' );
  var btnPreText = $( '#proceed' ).html();
  
  card.mount( '#card-element' );
  
  // Related modal main data management:
  $( '.tool' ).on( 'click', function ()
  {
    $( '#sp-main-title' ).text( $( this ).attr( 'data-title' ) );
    $( '#sp-sub-title' ).text( '(' + $( this ).attr( 'data-type' ) + ')' );
    $( '[name="price"]' ).val( $( this ).attr( 'data-price' ) );
  });
  
  // Reset card details input field:
  $( '#pay-with-stripe' ).on( 'hidden.bs.modal', function ()
  {
    card.clear();
  });
  
  // Submit the form with token ID:
  $( '#stripe-pay-form' ).on( 'submit', function ( event )
  {
    event.preventDefault();
    
    stripe.createToken( card ).then( function ( result )
    {
      if ( result.error )
      {
        $( '#proceed' ).removeAttr( 'disabled' );
        $( '#proceed' ).html( btnPreText );
        
        showStripeError( result );
      }
      else
      {
        stripeTokenHandler( result );
        hideStripeError();
      }
    });
    
    $( '#proceed' ).attr( 'disabled', 'disabled' );
    $( '#proceed' ).html( getSpinnerMarkup() );
  });
  
  // Handle real time validation errors:
  card.addEventListener( 'change', function ( event )
  {
    if ( event.error ) stripeTokenHandler( event );
    else hideStripeError();
  });
});