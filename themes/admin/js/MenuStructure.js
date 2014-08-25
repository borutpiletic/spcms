/**
 * MenuStructure
 * 
 * This is a simple class for creating fancyTree menu instances.
 */

var MenuStructure = {

    menuCount: 0,

    init: function(menuCount) 
    {        
        // Check if plugin Fancytree is loaded
        if ($.fn.fancytree === undefined)
            throw ('jQuery plugin "fancytree" not loaded!');
        
        // Create instances
        for(var i = 0; i < menuCount; i++)
            MenuStructure.fancyTreeInstance(i);
    },

    fancyTreeInstance: function(i)
    {
        $('#menu' + i).fancytree(
        {
                extensions: ["dnd"],
                dnd: {
                  autoExpandMS: 400,
                  draggable: { // modify default jQuery draggable options
                        zIndex: 1000,
                        scroll: false
                  },
                  preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
                  preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
                  dragStart: function(node, data) {
console.log(data);
                        // This function MUST be defined to enable dragging for the tree.
                        // Return false to cancel dragging of node.
          //    if( data.originalEvent.shiftKey ) ...          
                        return true;
                  },
                  dragEnter: function(node, data) {
                        /* data.otherNode may be null for non-fancytree droppables.
                         * Return false to disallow dropping on node. In this case
                         * dragOver and dragLeave are not called.
                         * Return 'over', 'before, or 'after' to force a hitMode.
                         * Return ['before', 'after'] to restrict available hitModes.
                         * Any other return value will calc the hitMode from the cursor position.
                         */
                        // Prevent dropping a parent below another parent (only sort
                        // nodes under the same parent):
          //    if(node.parent !== data.otherNode.parent){
          //      return false;
          //    }
                        // Don't allow dropping *over* a node (would create a child). Just
                        // allow changing the order:
          //    return ["before", "after"];
                        // Accept everything:
                        return true;
                  },
                  dragOver: function(node, data) {
                  },
                  dragLeave: function(node, data) {
                  },
                  dragStop: function(node, data) {
                  },
                  dragDrop: function(node, data) {
                        // This function MUST be defined to enable dropping of items on the tree.
                        // hitMode is 'before', 'after', or 'over'.
                        // We could for example move the source to the new target:
                        data.otherNode.moveTo(node, data.hitMode);
                  }
                }
        });	        
    }
};    


