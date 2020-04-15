wp.domReady( function() {

  /**
   * function to detect if current block has a parent block
   * @param  {String} clientId  clientId of current block
   * @return {Boolean}          true if block has a parent, false if it is a root block
   */
  const blockHasParent = ( clientId ) => clientId !== wp.data.select( 'core/block-editor' ).getBlockHierarchyRootClientId( clientId );

  var el = wp.element.createElement;

  /**
   * alter gutenberg Block Edit UI to include our controls
   * @param  {Object} BlockEdit )             
   * @return {Object}           
   */
  var withInspectorControls = wp.compose.createHigherOrderComponent( function( BlockEdit ) {
    return function( props ) {

      if(blockHasParent(props.clientId)) {
        props.setAttributes( { bootstrapContainer: "not set" } );
        return el(
            wp.element.Fragment,
            {},
            el(
                BlockEdit,
                props
            ));
      }

      // this is a new root Block if "not set": set Enabled by default
      // this way we do not need to set and save the attribute on all the child blocks
      if(props.attributes.bootstrapContainer == "not set") props.setAttributes({ bootstrapContainer: "enabled"});

      // if block is root Block
      return el(
          wp.element.Fragment,
          {},
          el(
              BlockEdit,
              props
          ),
          el(
              wp.blockEditor.InspectorControls,
              {},
              el(
                  wp.components.PanelBody,
                  {title: "Bootstrap settings"},
                  el(
                      wp.components.PanelRow,
                      {},
                      el(
                          wp.components.BaseControl,
                          {
                            children: el(
                                wp.components.ToggleControl,
                                {
                                  label: "display in container",
                                  checked: (props.attributes.bootstrapContainer == "enabled"),
                                  onChange: ( value ) => {
                                    if (value) {
                                      props.setAttributes( { bootstrapContainer: "enabled" } );
                                    } else {
                                      props.setAttributes( { bootstrapContainer: "disabled" } );
                                    }
                                  },
                                }
                            )
                          },
                      )
                  )
              )
          )
      );
    };
  }, 'withInspectorControls' );

  wp.hooks.addFilter(
      'editor.BlockEdit',
      'bootstrap-core-blocks/with-inspector-controls',
      withInspectorControls
  );

}); // end of wp.domReady

/**
 * Add custom attribute for mobile visibility.
 *
 * @param {Object} settings Settings for the block.
 *
 * @return {Object} settings Modified settings.
 */
function addAttributes( settings, name ) {

  //check if object exists for old Gutenberg version compatibility
  if( typeof settings.attributes !== 'undefined' ){

    settings.attributes = Object.assign( settings.attributes, {
      bootstrapContainer:{
        type: String,
        default: 'not set',
      }
    });
  }
  return settings;
}

// A: ADD ATTRIBUTE

wp.hooks.addFilter(
    'blocks.registerBlockType',
    'bootstrap-core-blocks/custom-attributes',
    addAttributes
);
