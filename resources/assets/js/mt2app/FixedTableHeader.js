/*
 * Angular Fixed Table Header
 * https://github.com/daniel-nagy/fixed-table-header
 * @license MIT
 * v0.2.1
 */

 /*
  * some modifications made to original script
  * changed from a module to a directive
  * additional custom scripts for calculating table height
  */

  mt2App.directive('fixHead', [ '$compile' , '$window' , '$log' , '$timeout' , function ( $compile , $window , $log , $timeout ) {
    return {
      compile : function (tElement) {

        var table = {
          clone: tElement.parent().clone().empty(),
          original: tElement.parent(),
          originalBody: tElement.next().next() // custom: tbody element
        };

        var header = {
          clone: tElement.clone(),
          original: tElement
        };

        // custom: hide window scrollbar
        angular.element( document.querySelector( 'body' ) ).css( 'overflow', 'hidden' );

        // prevent recursive compilation
        header.clone.removeAttr('fix-head').removeAttr('ng-if');

        table.clone.css({display: 'block', overflow: 'hidden'}).addClass('clone');
        header.clone.css('display', 'block');
        header.original.css('visibility', 'hidden');

        return function postLink(scope) {
          var scrollContainer = table.original.parent();

          // insert the element so when it is compiled it will link
          // with the correct scope and controllers
          header.original.after(header.clone);

          $compile(table.clone)(scope);
          $compile(header.clone)(scope);

          scrollContainer.parent()[0].insertBefore(table.clone.append(header.clone)[0], scrollContainer[0]);

          scrollContainer.on('scroll', function () {
            // use CSS transforms to move the cloned header when the table is scrolled horizontally
            header.clone.css('transform', 'translate3d(' + -(scrollContainer.prop('scrollLeft')) + 'px, 0, 0)');
          });

          function cells() {
            return header.clone.find('th').length;
          }

          function getCells(node) {
            return Array.prototype.map.call(node.find('th'), function (cell) {
              return jQLite(cell);
            });
          }

          function height() {
            return header.original.prop('clientHeight');
          }

          function jQLite(node) {
            return angular.element(node);
          }

          function marginTop(height) {
            var heightExcludingProgressBar = height - 3;

            table.original.css('marginTop', '-' + heightExcludingProgressBar + 'px');
          }

          /*
           * custom fx to dynamically calculate height of tbody
           */
          function getFixedTableHeight( table ) {
            var tableHeight = 0 ;

            tableHeight = angular.element( $window ).height() - getOffsetTop( table ) - 10;

            return tableHeight;
          }

          /*
           * custom fx to calculate offset top of tbody
           */
          function getOffsetTop( element ) {

            var top = 0;
            var count = 0;

            if (element == table.originalBody ){

              do {
                  top += element.prop( 'offsetTop' ) || 0;

                  element = angular.element( element.prop( 'offsetParent' ) );

              } while( element.prop( 'nodeName' ) != 'BODY' && element.prop( 'nodeName' ) != undefined );
            }

            return top;
          }

          function updateCells() {

            var cells = {
              clone: getCells(header.clone),
              original: getCells(header.original)
            };

            cells.clone.forEach(function (clone, index) {
              if(clone.data('isClone')) {
                return;
              }

              // prevent duplicating watch listeners
              clone.data('isClone', true);

              var cell = cells.original[index];
              var style = $window.getComputedStyle(cell[0]);

              var getWidth = function () {
                return style.width;
              };

              var setWidth = function () {

                marginTop(height());
                clone.css({minWidth: style.width, maxWidth: style.width});

                // custom: on window resize, update height of table
                if ( index === 0 ) {
                  table.original.parent().css( { 'overflow' : 'auto' , 'max-height' : getFixedTableHeight( table.originalBody ) + 'px' } );
                }
              };

              // custom: a delay for when switching tabs
              var delayedSetWidth = function () {
                $timeout(function() { setWidth() }, 200);
              };

              var listener = scope.$watch(getWidth, delayedSetWidth);

              $window.addEventListener('resize', delayedSetWidth);

              clone.on('$destroy', function () {
                listener();
                $window.removeEventListener('resize', delayedSetWidth);
              });

              cell.on('$destroy', function () {
                clone.remove();
              });
            });
          }

          scope.$watch(cells, updateCells);

          header.original.on('$destroy', function () {
            header.clone.remove();
          });
        };
      }
    };

  } ] );
