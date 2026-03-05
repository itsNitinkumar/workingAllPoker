/**
 * Script ( User )
 *
 * @author Shahzaib
 */

"use strict";

$( function ()
{
  
  // @version 2.0
  // Manage the banning reason text area based on selected input:
  $( '#user-account-status' ).on( 'change', function ()
  {
    if ( this.value == 0 )
    {
      $( '#banning-reason' ).removeClass( 'd-none' );
    }
    else
    {
      $( '#banning-reason' ).addClass( 'd-none' );
    }
  });
  
  
  // @version 1.5:
  if ( $( window ).width() < 768 )
  {
    $( 'body' ).removeClass( 'layout-fixed layout-navbar-fixed' );
  }
  
  
  // Manage modal ( get data ) by sending the ajax request:
  $( '.z-table, .z-card' ).on( 'click', function ( event )
  {
    var $element = $( event.target );
    var isFine   = true;
    var source   = '';
    
    /**
     * The element you want to use to send a request to read some data from the server,
     * must have a class "get-data-tool".
     *
     * If the requesting element is the child of "get-data-tool" class, add the
     * "get-data-tool-c" class also to the child element.
     *
     * The element that is having "get-data-tool" class, must have these attributes:
     * "data-id" Record ID
     *
     * "data-reference" Area/Controller name e.g. admin/tools
     * "data-requirement" Data requirement
     *
     * OR
     *
     * "data-source" Full URL
     */
    
    if ( $element.hasClass( 'get-data-tool-c' ) )
    {
      $element = $element.parent( '.get-data-tool' );
    }
    else
    {
      if ( ! $element.hasClass( 'get-data-tool' ) )
      {
        isFine = false;
      }
    }
    
    if ( isFine === true )
    {
      var dataSource = $element.attr( 'data-source' );
      var dataID     = $element.attr( 'data-id' );
      
      
      if ( ! dataSource )
      {
        var dataReference   = $element.attr( 'data-reference' );
        var dataRequirement = $element.attr( 'data-requirement' );        
        
        /**
         * The request receiver controller must be inside the "actions/" directory.
         * The receiver controller must have a method called "read()".
         *
         * @global string baseURL ( with slash at the end )
         */
        source  = baseURL + 'actions/' + dataReference + '/read/';
        source += dataRequirement;
      }
      else
      {
        source = $element.attr( 'data-source' );
      }
      
      if ( source !== '' )
      {
        getRecord( dataID, source, $element );
      }
      else
      {
        console.log( 'Invalid Source' );
      }
    }
  });
  
  
  // Tooltip:
  $( '[data-toggle="tooltip"]' ).tooltip();
  
  
  // Popover:
  $( '[data-toggle="popover"]' ).popover(
  {
    trigger: 'focus'
  });
  
  
  // Select 2:
  readySelect2();
  
  
  /**
   * jQuery Upload Preview
   *
   * @global string changeFile
   */
  var jupJson = {
    input_field: "#image-upload",
    preview_box: "#image-preview",
    label_field: "#image-label"
  };
  
  if ( typeof changeFile !== 'undefined' )
  {
    jupJson.label_selected = changeFile;
  }
  
  if ( typeof chooseFile !== 'undefined' )
  {
    jupJson.label_default = chooseFile;
  }
  
  $.uploadPreview( jupJson );
  
  
  // Prevent Checkbox to Mark as Checked:
  $( '.prevent-cb' ).on( 'click', function( event )
  {
    if ( ! $( this ).is( ':checked' ) )
    {
      event.preventDefault();
    }
  });
  
  
  /**
   * Fields management for the email settings page.
   *
   * @global string eProtocol
   */
  if ( typeof eProtocol !== 'undefined' )
  {
    if ( eProtocol === 'smtp' )
    {
      $( '.smtp-field' ).css( 'display', 'block' );
    }
  }
  
  $( '#protocol' ).on( 'change', function ()
  {
    if ( this.value === 'smtp' )
    {
      $( '.smtp-field' ).css( 'display', 'block' );
    }
    else
    {
      $( '.smtp-field' ).css( 'display', 'none' );
    }
  });
  
  
  /**
   * Field management for the adjust balance page.
   *
   * @version 1.8
   */
  $( 'select#log-visible-to-user' ).on( 'change', function ()
  {
    if ( this.value == 1 )
    {
      $( '#create-invoice-wrapper' ).css( 'display', 'block' );
    }
    else
    {
      $( '#create-invoice-wrapper' ).css( 'display', 'none' );
    }
  });
  
  
  /**
   * Manage the state (e.g. collapsed) of sidebar menu.
   *
   * @version 1.8
   */
  if ( $.isFunction( $.cookie ) )
  {
    $( '.sidebar-toggle' ).on( 'click', function ()
    {
      var collapsed = 1;
      
      if ( $.cookie( sidebarCookie ) == 1 ) collapsed = 0;
      
      $.cookie( sidebarCookie, collapsed, { expires: 365, path: '/' } );
    });
  }
  
  
  // Take backup form behaviour management:
  $( '#backup-action' ).on( 'change', function ()
  {
    if ( this.value == 1 )
    {
      $( '#take-backup-form' ).removeClass( 'form' );
    }
    else
    {
      $( '#take-backup-form' ).addClass( 'form' );
    }
  });
  
  
  // Read Announcements:
  // @global string csrfToken
  $( document ).on( 'click', '.a-read', function ()
  {
    $.ajax(
    {
      url: $( this ).attr( 'data-action' ),
      data: {z_csrf: csrfToken},
      method: 'POST',
      success: function ()
      {
        $( '.announcements-opener' ).removeClass( 'a-read' );
        $( '.badge-announcements' ).remove();
      }
    });
  });
  
  
  // Summernote:
  if ( $.isFunction( $.fn.summernote ) )
  {
    $( '.textarea' ).summernote(
    {
      height: 245,
      dialogsInBody: true,
      callbacks: {
        // https://github.com/summernote/summernote/issues/303
        onPaste: function ( event )
        {
          const bufferText = ( ( event.originalEvent || event ).clipboardData || window.clipboardData ).getData( 'Text' );

          event.preventDefault();

          setTimeout( function ()
          {
            document.execCommand( 'insertText', false, bufferText );
          }, 10 );
        },
        onImageUpload: function ( image )
        {
          sendFile( image[0], this );
        }
      },
      toolbar: [
        [
          'style',
          [
            'style'
          ]
        ],
        [
          'font',
          [
            'bold',
            'underline'
          ]
        ],
        [
          'fontsize',
          [
            'fontsize'
          ]
        ],
        [
          'para',
          [
            'paragraph',
            'ul',
            'ol'
          ]
        ],
        [
          'table',
          [
            'table'
          ]
        ],
        ['insert',
          [
            'link',
            'picture'
          ]
        ],
        [
          'view', 
          [
            'codeview',
            'fullscreen'
          ]
        ]
      ]
    });
  }
});
