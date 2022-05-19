(function($) {

    'use strict';

    var getBaseURL = function() {
        var url = document.URL;
        return url.substr(0, url.lastIndexOf('/'));
    };


    var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
// Success color: #10CFBD
    elems.forEach(function(html) {
        var switchery = new Switchery(html, {color: '#10CFBD'});
    });

    $(document).ready(function() {

        var defaulTree = $("#default-tree");
        if(defaulTree.length){
            defaulTree.dynatree({
                fx: {
                    height: "toggle",
                    duration: 200
                } //Slide down animation
            });
        }
        var dragTree = $("#drag-tree");
        if(dragTree.length){
            dragTree.dynatree({
                fx: {
                    height: "toggle",
                    duration: 200
                }, //Slide down animation
                dnd: {
                    preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
                    onDragStart: function(node) {
                        /** This function MUST be defined to enable dragging for the tree.
                         *  Return false to cancel dragging of node.
                         */
                        return true;
                    },
                    onDragEnter: function(node, sourceNode) {
                        /** sourceNode may be null for non-dynatree droppables.
                         *  Return false to disallow dropping on node. In this case
                         *  onDragOver and onDragLeave are not called.
                         *  Return 'over', 'before, or 'after' to force a hitMode.
                         *  Return ['before', 'after'] to restrict available hitModes.
                         *  Any other return value will calc the hitMode from the cursor position.
                         */
                        // Prevent dropping a parent below another parent (only sort
                        // nodes under the same parent)
                        if (node.parent !== sourceNode.parent) {
                            return false;
                        }
                        // Don't allow dropping *over* a node (would create a child)
                        return ["before", "after"];
                    },
                    onDrop: function(node, sourceNode, hitMode, ui, draggable) {
                        /** This function MUST be defined to enable dropping of items on
                         *  the tree.
                         */
                        sourceNode.move(node, hitMode);
                    }
                }
            });
        }

        //Date Pickers
        $('#datepicker-range, #datepicker-component, #datepicker-component2').datepicker();

        $('#datepicker-embeded').datepicker({
            daysOfWeekDisabled: "0,1"
        });

        // disabling dates
        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

        $('#form-project').validate();
    });

})(window.jQuery);