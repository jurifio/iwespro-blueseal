/*! Hammer.JS - v1.0.9 - 2014-03-18
 * http://eightmedia.github.com/hammer.js
 *
 * Copyright (c) 2014 Jorik Tangelder <j.tangelder@gmail.com>;
 * Licensed under the MIT license */

(function(window, undefined) {
    'use strict';

    /**
     * Hammer
     * use this to create instances
     * @param   {HTMLElement}   element
     * @param   {Object}        options
     * @returns {Hammer.Instance}
     * @constructor
     */
    var Hammer = function(element, options) {
        return new Hammer.Instance(element, options || {});
    };

// default settings
    Hammer.defaults = {
        // add styles and attributes to the element to prevent the browser from doing
        // its native behavior. this doesnt prevent the scrolling, but cancels
        // the contextmenu, tap highlighting etc
        // set to false to disable this
        stop_browser_behavior: {
            // this also triggers onselectstart=false for IE
            userSelect       : 'none',
            // this makes the element blocking in IE10 >, you could experiment with the value
            // see for more options this issue; https://github.com/EightMedia/hammer.js/issues/241
            touchAction      : 'none',
            touchCallout     : 'none',
            contentZooming   : 'none',
            userDrag         : 'none',
            tapHighlightColor: 'rgba(0,0,0,0)'
        }

        //
        // more settings are defined per gesture at gestures.js
        //
    };


// detect touchevents
    Hammer.HAS_POINTEREVENTS = window.navigator.pointerEnabled || window.navigator.msPointerEnabled;
    Hammer.HAS_TOUCHEVENTS = ('ontouchstart' in window);

// dont use mouseevents on mobile devices
    Hammer.MOBILE_REGEX = /mobile|tablet|ip(ad|hone|od)|android|silk/i;
    Hammer.NO_MOUSEEVENTS = Hammer.HAS_TOUCHEVENTS && window.navigator.userAgent.match(Hammer.MOBILE_REGEX);

// eventtypes per touchevent (start, move, end)
// are filled by Event.determineEventTypes on setup
    Hammer.EVENT_TYPES = {};

// interval in which Hammer recalculates current velocity in ms
    Hammer.UPDATE_VELOCITY_INTERVAL = 16;

// hammer document where the base events are added at
    Hammer.DOCUMENT = window.document;

// define these also as vars, for internal usage.
// direction defines
    var DIRECTION_DOWN = Hammer.DIRECTION_DOWN = 'down';
    var DIRECTION_LEFT = Hammer.DIRECTION_LEFT = 'left';
    var DIRECTION_UP = Hammer.DIRECTION_UP = 'up';
    var DIRECTION_RIGHT = Hammer.DIRECTION_RIGHT = 'right';

// pointer type
    var POINTER_MOUSE = Hammer.POINTER_MOUSE = 'mouse';
    var POINTER_TOUCH = Hammer.POINTER_TOUCH = 'touch';
    var POINTER_PEN = Hammer.POINTER_PEN = 'pen';

// touch event defines
    var EVENT_START = Hammer.EVENT_START = 'start';
    var EVENT_MOVE = Hammer.EVENT_MOVE = 'move';
    var EVENT_END = Hammer.EVENT_END = 'end';


// plugins and gestures namespaces
    Hammer.plugins = Hammer.plugins || {};
    Hammer.gestures = Hammer.gestures || {};


// if the window events are set...
    Hammer.READY = false;

    /**
     * setup events to detect gestures on the document
     */
    function setup() {
        if(Hammer.READY) {
            return;
        }

        // find what eventtypes we add listeners to
        Event.determineEventTypes();

        // Register all gestures inside Hammer.gestures
        Utils.each(Hammer.gestures, function(gesture){
            Detection.register(gesture);
        });

        // Add touch events on the document
        Event.onTouch(Hammer.DOCUMENT, EVENT_MOVE, Detection.detect);
        Event.onTouch(Hammer.DOCUMENT, EVENT_END, Detection.detect);

        // Hammer is ready...!
        Hammer.READY = true;
    }

    var Utils = Hammer.utils = {
        /**
         * extend method,
         * also used for cloning when dest is an empty object
         * @param   {Object}    dest
         * @param   {Object}    src
         * @parm  {Boolean}  merge    do a merge
         * @returns {Object}    dest
         */
        extend: function extend(dest, src, merge) {
            for(var key in src) {
                if(dest[key] !== undefined && merge) {
                    continue;
                }
                dest[key] = src[key];
            }
            return dest;
        },


        /**
         * for each
         * @param obj
         * @param iterator
         */
        each: function(obj, iterator, context) {
            var i, o;
            // native forEach on arrays
            if ('forEach' in obj) {
                obj.forEach(iterator, context);
            }
            // arrays
            else if(obj.length !== undefined) {
                for(i=-1; (o=obj[++i]);) {
                    if (iterator.call(context, o, i, obj) === false) {
                        return;
                    }
                }
            }
            // objects
            else {
                for(i in obj) {
                    if(obj.hasOwnProperty(i) &&
                        iterator.call(context, obj[i], i, obj) === false) {
                        return;
                    }
                }
            }
        },

        /**
         * find if a node is in the given parent
         * used for event delegation tricks
         * @param   {HTMLElement}   node
         * @param   {HTMLElement}   parent
         * @returns {boolean}       has_parent
         */
        hasParent: function(node, parent) {
            while(node) {
                if(node == parent) {
                    return true;
                }
                node = node.parentNode;
            }
            return false;
        },


        /**
         * get the center of all the touches
         * @param   {Array}     touches
         * @returns {Object}    center
         */
        getCenter: function getCenter(touches) {
            var valuesX = [], valuesY = [];

            Utils.each(touches, function(touch) {
                // I prefer clientX because it ignore the scrolling position
                valuesX.push(typeof touch.clientX !== 'undefined' ? touch.clientX : touch.pageX);
                valuesY.push(typeof touch.clientY !== 'undefined' ? touch.clientY : touch.pageY);
            });

            return {
                pageX: (Math.min.apply(Math, valuesX) + Math.max.apply(Math, valuesX)) / 2,
                pageY: (Math.min.apply(Math, valuesY) + Math.max.apply(Math, valuesY)) / 2
            };
        },


        /**
         * calculate the velocity between two points
         * @param   {Number}    delta_time
         * @param   {Number}    delta_x
         * @param   {Number}    delta_y
         * @returns {Object}    velocity
         */
        getVelocity: function getVelocity(delta_time, delta_x, delta_y) {
            return {
                x: Math.abs(delta_x / delta_time) || 0,
                y: Math.abs(delta_y / delta_time) || 0
            };
        },


        /**
         * calculate the angle between two coordinates
         * @param   {Touch}     touch1
         * @param   {Touch}     touch2
         * @returns {Number}    angle
         */
        getAngle: function getAngle(touch1, touch2) {
            var y = touch2.pageY - touch1.pageY
                , x = touch2.pageX - touch1.pageX;
            return Math.atan2(y, x) * 180 / Math.PI;
        },


        /**
         * angle to direction define
         * @param   {Touch}     touch1
         * @param   {Touch}     touch2
         * @returns {String}    direction constant, like DIRECTION_LEFT
         */
        getDirection: function getDirection(touch1, touch2) {
            var x = Math.abs(touch1.pageX - touch2.pageX)
                , y = Math.abs(touch1.pageY - touch2.pageY);
            if(x >= y) {
                return touch1.pageX - touch2.pageX > 0 ? DIRECTION_LEFT : DIRECTION_RIGHT;
            }
            return touch1.pageY - touch2.pageY > 0 ? DIRECTION_UP : DIRECTION_DOWN;
        },


        /**
         * calculate the distance between two touches
         * @param   {Touch}     touch1
         * @param   {Touch}     touch2
         * @returns {Number}    distance
         */
        getDistance: function getDistance(touch1, touch2) {
            var x = touch2.pageX - touch1.pageX
                , y = touch2.pageY - touch1.pageY;
            return Math.sqrt((x * x) + (y * y));
        },


        /**
         * calculate the scale factor between two touchLists (fingers)
         * no scale is 1, and goes down to 0 when pinched together, and bigger when pinched out
         * @param   {Array}     start
         * @param   {Array}     end
         * @returns {Number}    scale
         */
        getScale: function getScale(start, end) {
            // need two fingers...
            if(start.length >= 2 && end.length >= 2) {
                return this.getDistance(end[0], end[1]) / this.getDistance(start[0], start[1]);
            }
            return 1;
        },


        /**
         * calculate the rotation degrees between two touchLists (fingers)
         * @param   {Array}     start
         * @param   {Array}     end
         * @returns {Number}    rotation
         */
        getRotation: function getRotation(start, end) {
            // need two fingers
            if(start.length >= 2 && end.length >= 2) {
                return this.getAngle(end[1], end[0]) - this.getAngle(start[1], start[0]);
            }
            return 0;
        },


        /**
         * boolean if the direction is vertical
         * @param    {String}    direction
         * @returns  {Boolean}   is_vertical
         */
        isVertical: function isVertical(direction) {
            return direction == DIRECTION_UP || direction == DIRECTION_DOWN;
        },


        /**
         * toggle browser default behavior with css props
         * @param   {HtmlElement}   element
         * @param   {Object}        css_props
         * @param   {Boolean}       toggle
         */
        toggleDefaultBehavior: function toggleDefaultBehavior(element, css_props, toggle) {
            if(!css_props || !element || !element.style) {
                return;
            }

            // with css properties for modern browsers
            Utils.each(['webkit', 'moz', 'Moz', 'ms', 'o', ''], function(vendor) {
                Utils.each(css_props, function(value, prop) {
                    // vender prefix at the property
                    if(vendor) {
                        prop = vendor + prop.substring(0, 1).toUpperCase() + prop.substring(1);
                    }
                    // set the style
                    if(prop in element.style) {
                        element.style[prop] = !toggle && value;
                    }
                });
            });

            var false_fn = function(){ return false; };

            // also the disable onselectstart
            if(css_props.userSelect == 'none') {
                element.onselectstart = !toggle && false_fn;
            }
            // and disable ondragstart
            if(css_props.userDrag == 'none') {
                element.ondragstart = !toggle && false_fn;
            }
        }
    };


    /**
     * create new hammer instance
     * all methods should return the instance itself, so it is chainable.
     * @param   {HTMLElement}       element
     * @param   {Object}            [options={}]
     * @returns {Hammer.Instance}
     * @constructor
     */
    Hammer.Instance = function(element, options) {
        var self = this;

        // setup HammerJS window events and register all gestures
        // this also sets up the default options
        setup();

        this.element = element;

        // start/stop detection option
        this.enabled = true;

        // merge options
        this.options = Utils.extend(
            Utils.extend({}, Hammer.defaults),
            options || {});

        // add some css to the element to prevent the browser from doing its native behavoir
        if(this.options.stop_browser_behavior) {
            Utils.toggleDefaultBehavior(this.element, this.options.stop_browser_behavior, false);
        }

        // start detection on touchstart
        this.eventStartHandler = Event.onTouch(element, EVENT_START, function(ev) {
            if(self.enabled) {
                Detection.startDetect(self, ev);
            }
        });

        // keep a list of user event handlers which needs to be removed when calling 'dispose'
        this.eventHandlers = [];

        // return instance
        return this;
    };


    Hammer.Instance.prototype = {
        /**
         * bind events to the instance
         * @param   {String}      gesture
         * @param   {Function}    handler
         * @returns {Hammer.Instance}
         */
        on: function onEvent(gesture, handler) {
            var gestures = gesture.split(' ');
            Utils.each(gestures, function(gesture) {
                this.element.addEventListener(gesture, handler, false);
                this.eventHandlers.push({ gesture: gesture, handler: handler });
            }, this);
            return this;
        },


        /**
         * unbind events to the instance
         * @param   {String}      gesture
         * @param   {Function}    handler
         * @returns {Hammer.Instance}
         */
        off: function offEvent(gesture, handler) {
            var gestures = gesture.split(' ')
                , i, eh;
            Utils.each(gestures, function(gesture) {
                this.element.removeEventListener(gesture, handler, false);

                // remove the event handler from the internal list
                for(i=-1; (eh=this.eventHandlers[++i]);) {
                    if(eh.gesture === gesture && eh.handler === handler) {
                        this.eventHandlers.splice(i, 1);
                    }
                }
            }, this);
            return this;
        },


        /**
         * trigger gesture event
         * @param   {String}      gesture
         * @param   {Object}      [eventData]
         * @returns {Hammer.Instance}
         */
        trigger: function triggerEvent(gesture, eventData) {
            // optional
            if(!eventData) {
                eventData = {};
            }

            // create DOM event
            var event = Hammer.DOCUMENT.createEvent('Event');
            event.initEvent(gesture, true, true);
            event.gesture = eventData;

            // trigger on the target if it is in the instance element,
            // this is for event delegation tricks
            var element = this.element;
            if(Utils.hasParent(eventData.target, element)) {
                element = eventData.target;
            }

            element.dispatchEvent(event);
            return this;
        },


        /**
         * enable of disable hammer.js detection
         * @param   {Boolean}   state
         * @returns {Hammer.Instance}
         */
        enable: function enable(state) {
            this.enabled = state;
            return this;
        },


        /**
         * dispose this hammer instance
         * @returns {Hammer.Instance}
         */
        dispose: function dispose() {
            var i, eh;

            // undo all changes made by stop_browser_behavior
            if(this.options.stop_browser_behavior) {
                Utils.toggleDefaultBehavior(this.element, this.options.stop_browser_behavior, true);
            }

            // unbind all custom event handlers
            for(i=-1; (eh=this.eventHandlers[++i]);) {
                this.element.removeEventListener(eh.gesture, eh.handler, false);
            }
            this.eventHandlers = [];

            // unbind the start event listener
            Event.unbindDom(this.element, Hammer.EVENT_TYPES[EVENT_START], this.eventStartHandler);

            return null;
        }
    };


    /**
     * this holds the last move event,
     * used to fix empty touchend issue
     * see the onTouch event for an explanation
     * @type {Object}
     */
    var last_move_event = null;

    /**
     * when the mouse is hold down, this is true
     * @type {Boolean}
     */
    var enable_detect = false;

    /**
     * when touch events have been fired, this is true
     * @type {Boolean}
     */
    var touch_triggered = false;

    var Event = Hammer.event = {
        /**
         * simple addEventListener
         * @param   {HTMLElement}   element
         * @param   {String}        type
         * @param   {Function}      handler
         */
        bindDom: function(element, type, handler) {
            var types = type.split(' ');
            Utils.each(types, function(type){
                element.addEventListener(type, handler, false);
            });
        },


        /**
         * simple removeEventListener
         * @param   {HTMLElement}   element
         * @param   {String}        type
         * @param   {Function}      handler
         */
        unbindDom: function(element, type, handler) {
            var types = type.split(' ');
            Utils.each(types, function(type){
                element.removeEventListener(type, handler, false);
            });
        },


        /**
         * touch events with mouse fallback
         * @param   {HTMLElement}   element
         * @param   {String}        eventType        like EVENT_MOVE
         * @param   {Function}      handler
         */
        onTouch: function onTouch(element, eventType, handler) {
            var self = this;

            var bindDomOnTouch = function(ev) {
                var srcEventType = ev.type.toLowerCase();

                // onmouseup, but when touchend has been fired we do nothing.
                // this is for touchdevices which also fire a mouseup on touchend
                if(srcEventType.match(/mouse/) && touch_triggered) {
                    return;
                }

                // mousebutton must be down or a touch event
                else if(srcEventType.match(/touch/) ||   // touch events are always on screen
                    srcEventType.match(/pointerdown/) || // pointerevents touch
                    (srcEventType.match(/mouse/) && ev.which === 1)   // mouse is pressed
                ) {
                    enable_detect = true;
                }

                // mouse isn't pressed
                else if(srcEventType.match(/mouse/) && !ev.which) {
                    enable_detect = false;
                }


                // we are in a touch event, set the touch triggered bool to true,
                // this for the conflicts that may occur on ios and android
                if(srcEventType.match(/touch|pointer/)) {
                    touch_triggered = true;
                }

                // count the total touches on the screen
                var count_touches = 0;

                // when touch has been triggered in this detection session
                // and we are now handling a mouse event, we stop that to prevent conflicts
                if(enable_detect) {
                    // update pointerevent
                    if(Hammer.HAS_POINTEREVENTS && eventType != EVENT_END) {
                        count_touches = PointerEvent.updatePointer(eventType, ev);
                    }
                    // touch
                    else if(srcEventType.match(/touch/)) {
                        count_touches = ev.touches.length;
                    }
                    // mouse
                    else if(!touch_triggered) {
                        count_touches = srcEventType.match(/up/) ? 0 : 1;
                    }

                    // if we are in a end event, but when we remove one touch and
                    // we still have enough, set eventType to move
                    if(count_touches > 0 && eventType == EVENT_END) {
                        eventType = EVENT_MOVE;
                    }
                    // no touches, force the end event
                    else if(!count_touches) {
                        eventType = EVENT_END;
                    }

                    // store the last move event
                    if(count_touches || last_move_event === null) {
                        last_move_event = ev;
                    }

                    // trigger the handler
                    handler.call(Detection, self.collectEventData(element, eventType,
                        self.getTouchList(last_move_event, eventType),
                        ev));

                    // remove pointerevent from list
                    if(Hammer.HAS_POINTEREVENTS && eventType == EVENT_END) {
                        count_touches = PointerEvent.updatePointer(eventType, ev);
                    }
                }

                // on the end we reset everything
                if(!count_touches) {
                    last_move_event = null;
                    enable_detect = false;
                    touch_triggered = false;
                    PointerEvent.reset();
                }
            };

            this.bindDom(element, Hammer.EVENT_TYPES[eventType], bindDomOnTouch);

            // return the bound function to be able to unbind it later
            return bindDomOnTouch;
        },


        /**
         * we have different events for each device/browser
         * determine what we need and set them in the Hammer.EVENT_TYPES constant
         */
        determineEventTypes: function determineEventTypes() {
            // determine the eventtype we want to set
            var types;

            // pointerEvents magic
            if(Hammer.HAS_POINTEREVENTS) {
                types = PointerEvent.getEvents();
            }
            // on Android, iOS, blackberry, windows mobile we dont want any mouseevents
            else if(Hammer.NO_MOUSEEVENTS) {
                types = [
                    'touchstart',
                    'touchmove',
                    'touchend touchcancel'];
            }
            // for non pointer events browsers and mixed browsers,
            // like chrome on windows8 touch laptop
            else {
                types = [
                    'touchstart mousedown',
                    'touchmove mousemove',
                    'touchend touchcancel mouseup'];
            }

            Hammer.EVENT_TYPES[EVENT_START] = types[0];
            Hammer.EVENT_TYPES[EVENT_MOVE] = types[1];
            Hammer.EVENT_TYPES[EVENT_END] = types[2];
        },


        /**
         * create touchlist depending on the event
         * @param   {Object}    ev
         * @param   {String}    eventType   used by the fakemultitouch plugin
         */
        getTouchList: function getTouchList(ev/*, eventType*/) {
            // get the fake pointerEvent touchlist
            if(Hammer.HAS_POINTEREVENTS) {
                return PointerEvent.getTouchList();
            }

            // get the touchlist
            if(ev.touches) {
                return ev.touches;
            }

            // make fake touchlist from mouse position
            ev.identifier = 1;
            return [ev];
        },


        /**
         * collect event data for Hammer js
         * @param   {HTMLElement}   element
         * @param   {String}        eventType        like EVENT_MOVE
         * @param   {Object}        eventData
         */
        collectEventData: function collectEventData(element, eventType, touches, ev) {
            // find out pointerType
            var pointerType = POINTER_TOUCH;
            if(ev.type.match(/mouse/) || PointerEvent.matchType(POINTER_MOUSE, ev)) {
                pointerType = POINTER_MOUSE;
            }

            return {
                center     : Utils.getCenter(touches),
                timeStamp  : new Date().getTime(),
                target     : ev.target,
                touches    : touches,
                eventType  : eventType,
                pointerType: pointerType,
                srcEvent   : ev,

                /**
                 * prevent the browser default actions
                 * mostly used to disable scrolling of the browser
                 */
                preventDefault: function() {
                    if(this.srcEvent.preventManipulation) {
                        this.srcEvent.preventManipulation();
                    }

                    if(this.srcEvent.preventDefault) {
                        this.srcEvent.preventDefault();
                    }
                },

                /**
                 * stop bubbling the event up to its parents
                 */
                stopPropagation: function() {
                    this.srcEvent.stopPropagation();
                },

                /**
                 * immediately stop gesture detection
                 * might be useful after a swipe was detected
                 * @return {*}
                 */
                stopDetect: function() {
                    return Detection.stopDetect();
                }
            };
        }
    };

    var PointerEvent = Hammer.PointerEvent = {
        /**
         * holds all pointers
         * @type {Object}
         */
        pointers: {},

        /**
         * get a list of pointers
         * @returns {Array}     touchlist
         */
        getTouchList: function() {
            var touchlist = [];
            // we can use forEach since pointerEvents only is in IE10
            Utils.each(this.pointers, function(pointer){
                touchlist.push(pointer);
            });

            return touchlist;
        },

        /**
         * update the position of a pointer
         * @param   {String}   type             EVENT_END
         * @param   {Object}   pointerEvent
         */
        updatePointer: function(type, pointerEvent) {
            if(type == EVENT_END) {
                delete this.pointers[pointerEvent.pointerId];
            }
            else {
                pointerEvent.identifier = pointerEvent.pointerId;
                this.pointers[pointerEvent.pointerId] = pointerEvent;
            }

            // it's save to use Object.keys, since pointerEvents are only in newer browsers
            return Object.keys(this.pointers).length;
        },

        /**
         * check if ev matches pointertype
         * @param   {String}        pointerType     POINTER_MOUSE
         * @param   {PointerEvent}  ev
         */
        matchType: function(pointerType, ev) {
            if(!ev.pointerType) {
                return false;
            }

            var pt = ev.pointerType
                , types = {};

            types[POINTER_MOUSE] = (pt === POINTER_MOUSE);
            types[POINTER_TOUCH] = (pt === POINTER_TOUCH);
            types[POINTER_PEN] = (pt === POINTER_PEN);
            return types[pointerType];
        },


        /**
         * get events
         */
        getEvents: function() {
            return [
                'pointerdown MSPointerDown',
                'pointermove MSPointerMove',
                'pointerup pointercancel MSPointerUp MSPointerCancel'
            ];
        },

        /**
         * reset the list
         */
        reset: function() {
            this.pointers = {};
        }
    };


    var Detection = Hammer.detection = {
        // contains all registred Hammer.gestures in the correct order
        gestures: [],

        // data of the current Hammer.gesture detection session
        current : null,

        // the previous Hammer.gesture session data
        // is a full clone of the previous gesture.current object
        previous: null,

        // when this becomes true, no gestures are fired
        stopped : false,


        /**
         * start Hammer.gesture detection
         * @param   {Hammer.Instance}   inst
         * @param   {Object}            eventData
         */
        startDetect: function startDetect(inst, eventData) {
            // already busy with a Hammer.gesture detection on an element
            if(this.current) {
                return;
            }

            this.stopped = false;

            this.current = {
                inst              : inst, // reference to HammerInstance we're working for
                startEvent        : Utils.extend({}, eventData), // start eventData for distances, timing etc
                lastEvent         : false, // last eventData
                lastVelocityEvent : false, // last eventData for velocity.
                velocity          : false, // current velocity
                name              : '' // current gesture we're in/detected, can be 'tap', 'hold' etc
            };

            this.detect(eventData);
        },


        /**
         * Hammer.gesture detection
         * @param   {Object}    eventData
         */
        detect: function detect(eventData) {
            if(!this.current || this.stopped) {
                return;
            }

            // extend event data with calculations about scale, distance etc
            eventData = this.extendEventData(eventData);

            // instance options
            var inst_options = this.current.inst.options;

            // call Hammer.gesture handlers
            Utils.each(this.gestures, function(gesture) {
                // only when the instance options have enabled this gesture
                if(!this.stopped && inst_options[gesture.name] !== false) {
                    // if a handler returns false, we stop with the detection
                    if(gesture.handler.call(gesture, eventData, this.current.inst) === false) {
                        this.stopDetect();
                        return false;
                    }
                }
            }, this);

            // store as previous event event
            if(this.current) {
                this.current.lastEvent = eventData;
            }

            // endevent, but not the last touch, so dont stop
            if(eventData.eventType == EVENT_END && !eventData.touches.length - 1) {
                this.stopDetect();
            }

            return eventData;
        },


        /**
         * clear the Hammer.gesture vars
         * this is called on endDetect, but can also be used when a final Hammer.gesture has been detected
         * to stop other Hammer.gestures from being fired
         */
        stopDetect: function stopDetect() {
            // clone current data to the store as the previous gesture
            // used for the double tap gesture, since this is an other gesture detect session
            this.previous = Utils.extend({}, this.current);

            // reset the current
            this.current = null;

            // stopped!
            this.stopped = true;
        },


        /**
         * extend eventData for Hammer.gestures
         * @param   {Object}   ev
         * @returns {Object}   ev
         */
        extendEventData: function extendEventData(ev) {
            var cur = this.current
                , startEv = cur.startEvent;

            // if the touches change, set the new touches over the startEvent touches
            // this because touchevents don't have all the touches on touchstart, or the
            // user must place his fingers at the EXACT same time on the screen, which is not realistic
            // but, sometimes it happens that both fingers are touching at the EXACT same time
            if(ev.touches.length != startEv.touches.length || ev.touches === startEv.touches) {
                // extend 1 level deep to get the touchlist with the touch objects
                startEv.touches = [];
                Utils.each(ev.touches, function(touch) {
                    startEv.touches.push(Utils.extend({}, touch));
                });
            }

            var delta_time = ev.timeStamp - startEv.timeStamp
                , delta_x = ev.center.pageX - startEv.center.pageX
                , delta_y = ev.center.pageY - startEv.center.pageY
                , interimAngle
                , interimDirection
                , velocityEv = cur.lastVelocityEvent
                , velocity = cur.velocity;

            // calculate velocity every x ms
            if (velocityEv && ev.timeStamp - velocityEv.timeStamp > Hammer.UPDATE_VELOCITY_INTERVAL) {
                velocity = Utils.getVelocity(ev.timeStamp - velocityEv.timeStamp,
                    ev.center.pageX - velocityEv.center.pageX,
                    ev.center.pageY - velocityEv.center.pageY);

                cur.lastVelocityEvent = ev;
                cur.velocity = velocity;
            }
            else if(!cur.velocity) {
                velocity = Utils.getVelocity(delta_time, delta_x, delta_y);

                cur.lastVelocityEvent = ev;
                cur.velocity = velocity;
            }

            // end events (e.g. dragend) don't have useful values for interimDirection & interimAngle
            // because the previous event has exactly the same coordinates
            // so for end events, take the previous values of interimDirection & interimAngle
            // instead of recalculating them and getting a spurious '0'
            if(ev.eventType == EVENT_END) {
                interimAngle = cur.lastEvent && cur.lastEvent.interimAngle;
                interimDirection = cur.lastEvent && cur.lastEvent.interimDirection;
            }
            else {
                interimAngle = cur.lastEvent &&
                    Utils.getAngle(cur.lastEvent.center, ev.center);
                interimDirection = cur.lastEvent &&
                    Utils.getDirection(cur.lastEvent.center, ev.center);
            }

            Utils.extend(ev, {
                deltaTime: delta_time,

                deltaX: delta_x,
                deltaY: delta_y,

                velocityX: velocity.x,
                velocityY: velocity.y,

                distance: Utils.getDistance(startEv.center, ev.center),

                angle: Utils.getAngle(startEv.center, ev.center),
                interimAngle: interimAngle,

                direction: Utils.getDirection(startEv.center, ev.center),
                interimDirection: interimDirection,

                scale: Utils.getScale(startEv.touches, ev.touches),
                rotation: Utils.getRotation(startEv.touches, ev.touches),

                startEvent: startEv
            });

            return ev;
        },


        /**
         * register new gesture
         * @param   {Object}    gesture object, see gestures.js for documentation
         * @returns {Array}     gestures
         */
        register: function register(gesture) {
            // add an enable gesture options if there is no given
            var options = gesture.defaults || {};
            if(options[gesture.name] === undefined) {
                options[gesture.name] = true;
            }

            // extend Hammer default options with the Hammer.gesture options
            Utils.extend(Hammer.defaults, options, true);

            // set its index
            gesture.index = gesture.index || 1000;

            // add Hammer.gesture to the list
            this.gestures.push(gesture);

            // sort the list by index
            this.gestures.sort(function(a, b) {
                if(a.index < b.index) { return -1; }
                if(a.index > b.index) { return 1; }
                return 0;
            });

            return this.gestures;
        }
    };


    /**
     * Drag
     * Move with x fingers (default 1) around on the page. Blocking the scrolling when
     * moving left and right is a good practice. When all the drag events are blocking
     * you disable scrolling on that area.
     * @events  drag, drapleft, dragright, dragup, dragdown
     */
    Hammer.gestures.Drag = {
        name     : 'drag',
        index    : 50,
        defaults : {
            drag_min_distance            : 10,

            // Set correct_for_drag_min_distance to true to make the starting point of the drag
            // be calculated from where the drag was triggered, not from where the touch started.
            // Useful to avoid a jerk-starting drag, which can make fine-adjustments
            // through dragging difficult, and be visually unappealing.
            correct_for_drag_min_distance: true,

            // set 0 for unlimited, but this can conflict with transform
            drag_max_touches             : 1,

            // prevent default browser behavior when dragging occurs
            // be careful with it, it makes the element a blocking element
            // when you are using the drag gesture, it is a good practice to set this true
            drag_block_horizontal        : false,
            drag_block_vertical          : false,

            // drag_lock_to_axis keeps the drag gesture on the axis that it started on,
            // It disallows vertical directions if the initial direction was horizontal, and vice versa.
            drag_lock_to_axis            : false,

            // drag lock only kicks in when distance > drag_lock_min_distance
            // This way, locking occurs only when the distance has become large enough to reliably determine the direction
            drag_lock_min_distance       : 25
        },

        triggered: false,
        handler  : function dragGesture(ev, inst) {
            // current gesture isnt drag, but dragged is true
            // this means an other gesture is busy. now call dragend
            if(Detection.current.name != this.name && this.triggered) {
                inst.trigger(this.name + 'end', ev);
                this.triggered = false;
                return;
            }

            // max touches
            if(inst.options.drag_max_touches > 0 &&
                ev.touches.length > inst.options.drag_max_touches) {
                return;
            }

            switch(ev.eventType) {
                case EVENT_START:
                    this.triggered = false;
                    break;

                case EVENT_MOVE:
                    // when the distance we moved is too small we skip this gesture
                    // or we can be already in dragging
                    if(ev.distance < inst.options.drag_min_distance &&
                        Detection.current.name != this.name) {
                        return;
                    }

                    // we are dragging!
                    if(Detection.current.name != this.name) {
                        Detection.current.name = this.name;
                        if(inst.options.correct_for_drag_min_distance && ev.distance > 0) {
                            // When a drag is triggered, set the event center to drag_min_distance pixels from the original event center.
                            // Without this correction, the dragged distance would jumpstart at drag_min_distance pixels instead of at 0.
                            // It might be useful to save the original start point somewhere
                            var factor = Math.abs(inst.options.drag_min_distance / ev.distance);
                            Detection.current.startEvent.center.pageX += ev.deltaX * factor;
                            Detection.current.startEvent.center.pageY += ev.deltaY * factor;

                            // recalculate event data using new start point
                            ev = Detection.extendEventData(ev);
                        }
                    }

                    // lock drag to axis?
                    if(Detection.current.lastEvent.drag_locked_to_axis ||
                        ( inst.options.drag_lock_to_axis &&
                            inst.options.drag_lock_min_distance <= ev.distance
                        )) {
                        ev.drag_locked_to_axis = true;
                    }
                    var last_direction = Detection.current.lastEvent.direction;
                    if(ev.drag_locked_to_axis && last_direction !== ev.direction) {
                        // keep direction on the axis that the drag gesture started on
                        if(Utils.isVertical(last_direction)) {
                            ev.direction = (ev.deltaY < 0) ? DIRECTION_UP : DIRECTION_DOWN;
                        }
                        else {
                            ev.direction = (ev.deltaX < 0) ? DIRECTION_LEFT : DIRECTION_RIGHT;
                        }
                    }

                    // first time, trigger dragstart event
                    if(!this.triggered) {
                        inst.trigger(this.name + 'start', ev);
                        this.triggered = true;
                    }

                    // trigger events
                    inst.trigger(this.name, ev);
                    inst.trigger(this.name + ev.direction, ev);

                    var is_vertical = Utils.isVertical(ev.direction);

                    // block the browser events
                    if((inst.options.drag_block_vertical && is_vertical) ||
                        (inst.options.drag_block_horizontal && !is_vertical)) {
                        ev.preventDefault();
                    }
                    break;

                case EVENT_END:
                    // trigger dragend
                    if(this.triggered) {
                        inst.trigger(this.name + 'end', ev);
                    }

                    this.triggered = false;
                    break;
            }
        }
    };

    /**
     * Hold
     * Touch stays at the same place for x time
     * @events  hold
     */
    Hammer.gestures.Hold = {
        name    : 'hold',
        index   : 10,
        defaults: {
            hold_timeout  : 500,
            hold_threshold: 1
        },
        timer   : null,

        handler : function holdGesture(ev, inst) {
            switch(ev.eventType) {
                case EVENT_START:
                    // clear any running timers
                    clearTimeout(this.timer);

                    // set the gesture so we can check in the timeout if it still is
                    Detection.current.name = this.name;

                    // set timer and if after the timeout it still is hold,
                    // we trigger the hold event
                    this.timer = setTimeout(function() {
                        if(Detection.current.name == 'hold') {
                            inst.trigger('hold', ev);
                        }
                    }, inst.options.hold_timeout);
                    break;

                // when you move or end we clear the timer
                case EVENT_MOVE:
                    if(ev.distance > inst.options.hold_threshold) {
                        clearTimeout(this.timer);
                    }
                    break;

                case EVENT_END:
                    clearTimeout(this.timer);
                    break;
            }
        }
    };

    /**
     * Release
     * Called as last, tells the user has released the screen
     * @events  release
     */
    Hammer.gestures.Release = {
        name   : 'release',
        index  : Infinity,
        handler: function releaseGesture(ev, inst) {
            if(ev.eventType == EVENT_END) {
                inst.trigger(this.name, ev);
            }
        }
    };

    /**
     * Swipe
     * triggers swipe events when the end velocity is above the threshold
     * for best usage, set prevent_default (on the drag gesture) to true
     * @events  swipe, swipeleft, swiperight, swipeup, swipedown
     */
    Hammer.gestures.Swipe = {
        name    : 'swipe',
        index   : 40,
        defaults: {
            swipe_min_touches: 1,
            swipe_max_touches: 1,
            swipe_velocity   : 0.7
        },
        handler : function swipeGesture(ev, inst) {
            if(ev.eventType == EVENT_END) {
                // max touches
                if(ev.touches.length < inst.options.swipe_min_touches ||
                    ev.touches.length > inst.options.swipe_max_touches) {
                    return;
                }

                // when the distance we moved is too small we skip this gesture
                // or we can be already in dragging
                if(ev.velocityX > inst.options.swipe_velocity ||
                    ev.velocityY > inst.options.swipe_velocity) {
                    // trigger swipe events
                    inst.trigger(this.name, ev);
                    inst.trigger(this.name + ev.direction, ev);
                }
            }
        }
    };

    /**
     * Tap/DoubleTap
     * Quick touch at a place or double at the same place
     * @events  tap, doubletap
     */
    Hammer.gestures.Tap = {
        name    : 'tap',
        index   : 100,
        defaults: {
            tap_max_touchtime : 250,
            tap_max_distance  : 10,
            tap_always        : true,
            doubletap_distance: 20,
            doubletap_interval: 300
        },

        has_moved: false,

        handler : function tapGesture(ev, inst) {
            var prev, since_prev, did_doubletap;

            // reset moved state
            if(ev.eventType == EVENT_START) {
                this.has_moved = false;
            }

            // Track the distance we've moved. If it's above the max ONCE, remember that (fixes #406).
            else if(ev.eventType == EVENT_MOVE && !this.moved) {
                this.has_moved = (ev.distance > inst.options.tap_max_distance);
            }

            else if(ev.eventType == EVENT_END &&
                ev.srcEvent.type != 'touchcancel' &&
                ev.deltaTime < inst.options.tap_max_touchtime && !this.has_moved) {

                // previous gesture, for the double tap since these are two different gesture detections
                prev = Detection.previous;
                since_prev = prev && prev.lastEvent && ev.timeStamp - prev.lastEvent.timeStamp;
                did_doubletap = false;

                // check if double tap
                if(prev && prev.name == 'tap' &&
                    (since_prev && since_prev < inst.options.doubletap_interval) &&
                    ev.distance < inst.options.doubletap_distance) {
                    inst.trigger('doubletap', ev);
                    did_doubletap = true;
                }

                // do a single tap
                if(!did_doubletap || inst.options.tap_always) {
                    Detection.current.name = 'tap';
                    inst.trigger(Detection.current.name, ev);
                }
            }
        }
    };

    /**
     * Touch
     * Called as first, tells the user has touched the screen
     * @events  touch
     */
    Hammer.gestures.Touch = {
        name    : 'touch',
        index   : -Infinity,
        defaults: {
            // call preventDefault at touchstart, and makes the element blocking by
            // disabling the scrolling of the page, but it improves gestures like
            // transforming and dragging.
            // be careful with using this, it can be very annoying for users to be stuck
            // on the page
            prevent_default    : false,

            // disable mouse events, so only touch (or pen!) input triggers events
            prevent_mouseevents: false
        },
        handler : function touchGesture(ev, inst) {
            if(inst.options.prevent_mouseevents &&
                ev.pointerType == POINTER_MOUSE) {
                ev.stopDetect();
                return;
            }

            if(inst.options.prevent_default) {
                ev.preventDefault();
            }

            if(ev.eventType == EVENT_START) {
                inst.trigger(this.name, ev);
            }
        }
    };


    /**
     * Transform
     * User want to scale or rotate with 2 fingers
     * @events  transform, pinch, pinchin, pinchout, rotate
     */
    Hammer.gestures.Transform = {
        name     : 'transform',
        index    : 45,
        defaults : {
            // factor, no scale is 1, zoomin is to 0 and zoomout until higher then 1
            transform_min_scale      : 0.01,
            // rotation in degrees
            transform_min_rotation   : 1,
            // prevent default browser behavior when two touches are on the screen
            // but it makes the element a blocking element
            // when you are using the transform gesture, it is a good practice to set this true
            transform_always_block   : false,
            // ensures that all touches occurred within the instance element
            transform_within_instance: false
        },

        triggered: false,

        handler  : function transformGesture(ev, inst) {
            // current gesture isnt drag, but dragged is true
            // this means an other gesture is busy. now call dragend
            if(Detection.current.name != this.name && this.triggered) {
                inst.trigger(this.name + 'end', ev);
                this.triggered = false;
                return;
            }

            // at least multitouch
            if(ev.touches.length < 2) {
                return;
            }

            // prevent default when two fingers are on the screen
            if(inst.options.transform_always_block) {
                ev.preventDefault();
            }

            // check if all touches occurred within the instance element
            if(inst.options.transform_within_instance) {
                for(var i=-1; ev.touches[++i];) {
                    if(!Utils.hasParent(ev.touches[i].target, inst.element)) {
                        return;
                    }
                }
            }

            switch(ev.eventType) {
                case EVENT_START:
                    this.triggered = false;
                    break;

                case EVENT_MOVE:
                    var scale_threshold = Math.abs(1 - ev.scale);
                    var rotation_threshold = Math.abs(ev.rotation);

                    // when the distance we moved is too small we skip this gesture
                    // or we can be already in dragging
                    if(scale_threshold < inst.options.transform_min_scale &&
                        rotation_threshold < inst.options.transform_min_rotation) {
                        return;
                    }

                    // we are transforming!
                    Detection.current.name = this.name;

                    // first time, trigger dragstart event
                    if(!this.triggered) {
                        inst.trigger(this.name + 'start', ev);
                        this.triggered = true;
                    }

                    inst.trigger(this.name, ev); // basic transform event

                    // trigger rotate event
                    if(rotation_threshold > inst.options.transform_min_rotation) {
                        inst.trigger('rotate', ev);
                    }

                    // trigger pinch event
                    if(scale_threshold > inst.options.transform_min_scale) {
                        inst.trigger('pinch', ev);
                        inst.trigger('pinch' + (ev.scale<1 ? 'in' : 'out'), ev);
                    }
                    break;

                case EVENT_END:
                    // trigger dragend
                    if(this.triggered) {
                        inst.trigger(this.name + 'end', ev);
                    }

                    this.triggered = false;
                    break;
            }
        }
    };

// AMD export
    if(typeof define == 'function' && define.amd) {
        define(function(){
            return Hammer;
        });
    }
// commonjs export
    else if(typeof module == 'object' && module.exports) {
        module.exports = Hammer;
    }
// browser export
    else {
        window.Hammer = Hammer;
    }

})(window);

/*! Copyright (c) 2013 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.1.3
 *
 * Requires: 1.2.2+
 */

(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var toFix = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'];
    var toBind = 'onwheel' in document || document.documentMode >= 9 ? ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'];
    var lowestDelta, lowestDeltaXY;

    if ( $.event.fixHooks ) {
        for ( var i = toFix.length; i; ) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }

    $.event.special.mousewheel = {
        setup: function() {
            if ( this.addEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.addEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = handler;
            }
        },

        teardown: function() {
            if ( this.removeEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.removeEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = null;
            }
        }
    };

    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
        },

        unmousewheel: function(fn) {
            return this.unbind("mousewheel", fn);
        }
    });


    function handler(event) {
        var orgEvent = event || window.event,
            args = [].slice.call(arguments, 1),
            delta = 0,
            deltaX = 0,
            deltaY = 0,
            absDelta = 0,
            absDeltaXY = 0,
            fn;
        event = $.event.fix(orgEvent);
        event.type = "mousewheel";

        // Old school scrollwheel delta
        if ( orgEvent.wheelDelta ) { delta = orgEvent.wheelDelta; }
        if ( orgEvent.detail )     { delta = orgEvent.detail * -1; }

        // New school wheel delta (wheel event)
        if ( orgEvent.deltaY ) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if ( orgEvent.deltaX ) {
            deltaX = orgEvent.deltaX;
            delta  = deltaX * -1;
        }

        // Webkit
        if ( orgEvent.wheelDeltaY !== undefined ) { deltaY = orgEvent.wheelDeltaY; }
        if ( orgEvent.wheelDeltaX !== undefined ) { deltaX = orgEvent.wheelDeltaX * -1; }

        // Look for lowest delta to normalize the delta values
        absDelta = Math.abs(delta);
        if ( !lowestDelta || absDelta < lowestDelta ) { lowestDelta = absDelta; }
        absDeltaXY = Math.max(Math.abs(deltaY), Math.abs(deltaX));
        if ( !lowestDeltaXY || absDeltaXY < lowestDeltaXY ) { lowestDeltaXY = absDeltaXY; }

        // Get a whole value for the deltas
        fn = delta > 0 ? 'floor' : 'ceil';
        delta  = Math[fn](delta / lowestDelta);
        deltaX = Math[fn](deltaX / lowestDeltaXY);
        deltaY = Math[fn](deltaY / lowestDeltaXY);

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

}));

/**
 * Mapplic - Custom Interactive Map Plugin by @sekler
 * http://www.mapplic.com
 */

(function($) {

    var Mapplic = function() {
        var self = this;
		console.log('oh');
        self.o = {
            source: '/asset/locations.json',
            height: 420,
            locations: true,
            minimap: true,
            sidebar: true,
            deeplinking: true,
            search: true,
            clearbutton: true,
            hovertip: true,
            fullscreen: false,
            developer: false,
            animate: true,
            maxscale: 4
        };

        self.init = function(el, params) {
	        console.log(el,params);
            // Extend options
            self.o = $.extend(self.o, params);

            self.x = 0;
            self.y = 0;
            self.scale = 1;

            self.el = el.addClass('mapplic-element mapplic-loading').height(self.o.height);

            // Process JSON file
            $.getJSON(self.o.source, function(data) { // Success
	            console.log(data);
                processData(data);
                self.el.removeClass('mapplic-loading');

                // Controls
                addControls();

            }).fail(function() { // Failure: couldn't load JSON file, or it is invalid.
                console.error('Couldn\'t load map data. (Make sure you are running the script through a server and not just opening the html file with your browser)');
                alert('Data file missing or invalid!');
            });

            return self;
        }

        // Tooltip
        function Tooltip() {
            this.el = null;
            this.shift = 6;
            this.drop = 0;

            this.init = function() {
                var s = this;

                // Construct
                this.el = $('<div></div>').addClass('mapplic-tooltip');
                $('<a></a>').addClass('mapplic-tooltip-close').attr('href', '#').click(function(e) {
                    e.preventDefault();
                    self.deeplinking.clear();
                    s.hide();
                }).appendTo(this.el);
                this.image = $('<img>').addClass('mapplic-tooltip-image').hide().appendTo(this.el);
                this.title = $('<h4></h4>').addClass('mapplic-tooltip-title').appendTo(this.el);
                this.content = $('<div></div>').addClass('mapplic-tooltip-content').appendTo(this.el);
                this.desc = $('<div></div>').addClass('mapplic-tooltip-description').appendTo(this.content);
                this.link = $('<a>More</a>').addClass('mapplic-tooltip-link').attr('href', '#').hide().appendTo(this.el);
                $('<div></div>').addClass('mapplic-tooltip-triangle').prependTo(this.el);

                // Append
                self.map.append(this.el);
            }

            this.set = function(location) {
                if (location) {
                    if (location.action == 'none') {
                        this.el.stop().fadeOut(300);
                        return;
                    }

                    var s = this;

                    if (location.image) this.image.attr('src', location.image).show();
                    else this.image.hide();

                    if (location.link) this.link.attr('href', location.link).show();
                    else this.link.hide();

                    this.title.text(location.title);
                    this.desc.html(location.description);

                    // Shift
                    var pinselect = $('.mapplic-pin[data-location="' + location.id + '"]');
                    if (pinselect.length == 0) {
                        this.shift = 6;
                    }
                    else this.shift = pinselect.height() + 6;

                    // Loading & positioning
                    $('img', this.desc).load(function() {
                        s.position(location);
                    });

                    this.position(location);
                }
            }

            this.show = function(location) {
                if (location) {
                    if (location.action == 'none') {
                        this.el.stop().fadeOut(300);
                        return;
                    }

                    var s = this;

                    if (location.image) this.image.attr('src', location.image).show();
                    else this.image.hide();

                    if (location.link) this.link.attr('href', location.link).show();
                    else this.link.hide();

                    this.title.text(location.title);
                    this.desc.html(location.description);

                    // Shift
                    var pinselect = $('.mapplic-pin[data-location="' + location.id + '"]');
                    if (pinselect.length == 0) {
                        this.shift = 6;
                    }
                    else this.shift = pinselect.height() + 6;

                    // Loading & positioning
                    $('img', this.desc).load(function() {
                        s.position(location);
                    });

                    this.position(location);

                    // Making it visible
                    this.el.stop().fadeIn(200).show();
                }
            }

            this.position = function(location) {
                var x = location.x * 100;
                y = location.y * 100;
                mt = -this.el.outerHeight() - this.shift,
                    ml = -this.el.outerWidth() / 2;
                this.el.css({
                    left: x + '%',
                    top: y + '%',
                    marginTop: mt,
                    marginLeft: ml
                });
                this.drop = this.el.outerHeight() + this.shift;
            }

            this.hide = function() {
                var s = this;

                this.el.stop().fadeOut(300, function() {
                    s.desc.empty();
                });
            }
        }

        // Deeplinking
        function Deeplinking() {
            this.init = function() {
                // Check hash for location
                var id = location.hash.slice(1);
                if (id) {
                    var locationData = getLocationData(id);

                    self.tooltip.set(locationData);
                    showLocation(id, 0);
                    self.tooltip.show(locationData);
                }
                else zoomTo(0.5, 0.5, 1, 0);

                // Hashchange
                $(window).on('hashchange', function() {
                    var id = location.hash.slice(1);

                    if (id) {
                        var locationData = getLocationData(id);

                        self.tooltip.set(locationData);
                        showLocation(id, 800);
                        self.tooltip.show(locationData);
                    }
                });
            }

            this.clear = function() {
                // if IE 6-8, else normal browsers
                if (history.pushState) history.pushState('', document.title, window.location.pathname);
                else window.location.hash = '';
            }
        }

        // HoverTooltip
        function HoverTooltip() {
            this.el = null;
            this.shift = 6;

            this.init = function() {
                var s = this;

                // Construct
                this.el = $('<div></div>').addClass('mapplic-tooltip mapplic-hovertip');
                this.title = $('<h4></h4>').addClass('mapplic-tooltip-title').appendTo(this.el);
                $('<div></div>').addClass('mapplic-tooltip-triangle').appendTo(this.el);

                // Events
                $(self.map).on('mouseover', '.mapplic-layer a', function() {
                    var data = '';
                    if ($(this).hasClass('mapplic-pin')) {
                        data = $(this).data('location');
                        s.shift = $(this).height() + 6;
                    }
                    else {
                        data = $(this).attr('xlink:href').slice(1);
                        s.shift = 6;
                    }

                    var location = getLocationData(data);
                    if (location) s.show(location);
                }).on('mouseout', function() {
                    s.hide();
                });

                self.map.append(this.el);
            }

            this.show = function(location) {
                this.title.text(location.title);

                var x = location.x * 100,
                    y = location.y * 100,
                    mt = -this.el.outerHeight() - this.shift,
                    ml = -this.el.outerWidth() / 2;
                this.el.css({
                    left: x + '%',
                    top: y + '%',
                    marginTop: mt,
                    marginLeft: ml
                });

                this.el.stop().fadeIn(100);
            }

            this.hide = function() {
                this.el.stop().fadeOut(200);
            }
        }

        // Minimap
        function Minimap() {
            this.el = null;

            this.init = function() {
                this.el = $('<div></div>').addClass('mapplic-minimap').appendTo(self.container);
                this.el.css('height', this.el.width() * self.hw_ratio);
                this.el.click(function(e) {
                    e.preventDefault();

                    var x = (e.pageX - $(this).offset().left) / $(this).width(),
                        y = (e.pageY - $(this).offset().top) / $(this).height();

                    zoomTo(x, y, self.scale / self.fitscale, 100);
                });
            }

            this.addLayer = function(data) {
                var layer = $('<div></div>').addClass('mapplic-minimap-layer').addClass(data.id).appendTo(this.el);
                $('<img>').attr('src', data.minimap).addClass('mapplic-minimap-background').appendTo(layer);
                $('<div></div>').addClass('mapplic-minimap-overlay').appendTo(layer);
                $('<img>').attr('src', data.minimap).addClass('mapplic-minimap-active').appendTo(layer);
            }

            this.show = function(target) {
                $('.mapplic-minimap-layer:visible', this.el).hide();
                $('.mapplic-minimap-layer.' + target, this.el).show();
            }

            this.update = function(x, y) {
                var active = $('.mapplic-minimap-active', this.el);

                if (x === undefined) x = self.x;
                if (y === undefined) y = self.y;

                var width = Math.round(self.container.width() / self.contentWidth / self.scale * this.el.width()),
                    height = Math.round(self.container.height() / self.contentHeight / self.scale * this.el.height()),
                    top = Math.round(-y / self.contentHeight / self.scale * this.el.height()),
                    left = Math.round(-x / self.contentWidth / self.scale * this.el.width()),
                    right = left + width,
                    bottom = top + height;

                active.css('clip', 'rect(' + top + 'px, ' + right + 'px, ' + bottom + 'px, ' + left + 'px)');
            }
        }

        // Sidebar
        function Sidebar() {
            this.el = null;
            this.list = null;

            this.init = function() {
                var s = this;

                this.el = $('<div></div>').addClass('mapplic-sidebar').appendTo(self.el);

                if (self.o.search) {
                    var form = $('<form></form>').addClass('mapplic-search-form').submit(function() {
                        return false;
                    }).appendTo(this.el);
                    self.clear = $('<button></button>').addClass('mapplic-search-clear').click(function() {
                        input.val('');
                        input.keyup();
                    }).appendTo(form);
                    var input = $('<input>').attr({'type': 'text', 'spellcheck': 'false', 'placeholder': 'Search for location...'}).addClass('mapplic-search-input').keyup(function() {
                        var keyword = $(this).val();
                        s.search(keyword);
                    }).prependTo(form);
                }

                var listContainer = $('<div></div>').addClass('mapplic-list-container').appendTo(this.el);
                this.list = $('<ol></ol>').addClass('mapplic-list').appendTo(listContainer);
                this.notfound = $('<p></p>').addClass('mapplic-not-found').text('Nothing found. Please try a different search.').appendTo(listContainer);

                if (!self.o.search) listContainer.css('padding-top', '0');
            }

            this.addCategories = function(categories) {
                var list = this.list;

                $.each(categories, function(index, category) {
                    var item = $('<li></li>').addClass('mapplic-list-category').addClass(category.id);
                    var ol = $('<ol></ol>').css('border-color', category.color).appendTo(item);
                    if (category.show == 'false') ol.hide();
                    var link = $('<a></a>').attr('href', '#').attr('title', category.title).css('background-color', category.color).text(category.title).click(function(e) {
                        ol.slideToggle(200);
                        return false;
                    }).prependTo(item);
                    if (category.icon) $('<img>').attr('src', category.icon).addClass('mapplic-list-thumbnail').prependTo(link);
                    $('<span></span>').text('0').addClass('mapplic-list-count').prependTo(link);
                    list.append(item);
                });
            }

            this.addLocation = function(data) {
                var item = $('<li></li>').addClass('mapplic-list-location').addClass('mapplic-list-shown');
                var link = $('<a></a>').attr('href', '#' + data.id).appendTo(item);
                if (data.thumbnail) $('<img>').attr('src', data.thumbnail).addClass('mapplic-list-thumbnail').appendTo(link);
                $('<h4></h4>').text(data.title).appendTo(link)
                $('<span></span>').html(data.about).appendTo(link);
                var category = $('.mapplic-list-category.' + data.category);

                if (category.length) $('ol', category).append(item);
                else this.list.append(item);

                // Count
                $('.mapplic-list-count', category).text($('.mapplic-list-shown', category).length);
            }

            this.search = function(keyword) {
                if (keyword) self.clear.fadeIn(100);
                else self.clear.fadeOut(100);

                $('.mapplic-list li', self.el).each(function() {
                    if ($(this).text().search(new RegExp(keyword, "i")) < 0) {
                        $(this).removeClass('mapplic-list-shown');
                        $(this).slideUp(200);
                    } else {
                        $(this).addClass('mapplic-list-shown');
                        $(this).show();
                    }
                });

                $('.mapplic-list > li', self.el).each(function() {
                    var count = $('.mapplic-list-shown', this).length;
                    $('.mapplic-list-count', this).text(count);
                });

                // Show not-found text
                if ($('.mapplic-list > li.mapplic-list-shown').length > 0) this.notfound.fadeOut(200);
                else this.notfound.fadeIn(200);
            }
        }

        // Developer tools
        function DevTools() {
            this.el = null;

            this.init = function() {
                this.el = $('<div></div>').addClass('mapplic-coordinates').appendTo(self.container);
                this.el.append('x: ');
                $('<code></code>').addClass('mapplic-coordinates-x').appendTo(this.el);
                this.el.append(' y: ');
                $('<code></code>').addClass('mapplic-coordinates-y').appendTo(this.el);

                $('.mapplic-layer', self.map).on('mousemove', function(e) {
                    var x = (e.pageX - self.map.offset().left) / self.map.width(),
                        y = (e.pageY - self.map.offset().top) / self.map.height();
                    $('.mapplic-coordinates-x').text(parseFloat(x).toFixed(4));
                    $('.mapplic-coordinates-y').text(parseFloat(y).toFixed(4));
                });
            }
        }

        // Clear Button
        function ClearButton() {
            this.el = null;

            this.init = function() {
                this.el = $('<a></a>').attr('href', '#').addClass('mapplic-clear-button').click(function(e) {
                    e.preventDefault();
                    self.deeplinking.clear();
                    self.tooltip.hide();
                    zoomTo(0.5, 0.5, 1);
                }).appendTo(self.container);
            }
        }

        // Full Screen
        function FullScreen() {
            this.el = null;

            this.init = function() {
                var s = this;
                this.element = self.el[0];

                $('<a></a>').attr('href', '#').attr('href', '#').addClass('mapplic-fullscreen-button').click(function(e) {
                    e.preventDefault();

                    if (s.isFull()) s.exitFull();
                    else s.goFull();

                }).appendTo(self.container);
            }

            this.goFull = function() {
                if (this.element.requestFullscreen) this.element.requestFullscreen();
                else if(this.element.mozRequestFullScreen) this.element.mozRequestFullScreen();
                else if(this.element.webkitRequestFullscreen) this.element.webkitRequestFullscreen();
                else if(this.element.msRequestFullscreen) this.element.msRequestFullscreen();
            }

            this.exitFull = function() {
                if (document.exitFullscreen) document.exitFullscreen();
                else if(document.mozCancelFullScreen) document.mozCancelFullScreen();
                else if(document.webkitExitFullscreen) document.webkitExitFullscreen();
            }

            this.isFull = function() {
                if (window.innerHeight == screen.height) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        // Functions
        var processData = function(data) {
            self.data = data;
            var nrlevels = 0;
            var shownLevel;

            self.container = $('<div></div>').addClass('mapplic-container').appendTo(self.el);
            self.map = $('<div></div>').addClass('mapplic-map').appendTo(self.container);

            self.levelselect = $('<select></select>').addClass('mapplic-levels-select');

            if (!self.o.sidebar) self.container.css('width', '100%');

            self.contentWidth = data.mapwidth;
            self.contentHeight = data.mapheight;

            self.hw_ratio = data.mapheight / data.mapwidth;
            if (data.mapheight / self.container.height() > data.mapwidth / self.container.width()) {
                self.min_width = self.container.width();
                self.min_height = self.container.width() * self.hw_ratio;
            }
            else {
                self.min_height = self.container.height();
                self.min_width = self.container.height() / self.hw_ratio;
            }

            self.map.css({
                'width': data.mapwidth,
                'height': data.mapheight
            });

            // Create minimap
            if (self.o.minimap) {
                self.minimap = new Minimap();
                self.minimap.init();
            }

            // Create sidebar
            if (self.o.sidebar) {
                self.sidebar = new Sidebar();
                self.sidebar.init();
                self.sidebar.addCategories(data.categories);
            }

            // Iterate through levels
            if (data.levels) {
                $.each(data.levels, function(index, value) {
                    var source = value.map;
                    var extension = source.substr((source.lastIndexOf('.') + 1)).toLowerCase();

                    // Create new map layer
                    var layer = $('<div></div>').addClass('mapplic-layer').addClass(value.id).hide().appendTo(self.map);
                    switch (extension) {

                        // Image formats
                        case 'jpg': case 'jpeg': case 'png': case 'gif':
                        $('<img>').attr('src', source).addClass('mapplic-map-image').appendTo(layer);
                        break;

                        // Vector format
                        case 'svg':
                            $('<div></div>').addClass('mapplic-map-image').load(source).appendTo(layer);
                            break;

                        // Other
                        default:
                            alert('File type ' + extension + ' is not supported!');
                    }

                    // Create new minimap layer
                    if (self.minimap) self.minimap.addLayer(value);

                    // Build layer control
                    self.levelselect.prepend($('<option></option>').attr('value', value.id).text(value.title));

                    if (!shownLevel || value.show) {
                        shownLevel = value.id;
                    }

                    /* Iterate through locations */
                    $.each(value.locations, function(index, value) {
                        var top = value.y * 100;
                        var left = value.x * 100;

                        if (value.pin != 'hidden') {
                            if (self.o.locations) {
                                var target = '#' + value.id;
                                if (value.action == 'redirect') target = value.link;

                                var pin = $('<a></a>').attr('href', target).addClass('mapplic-pin').css({'top': top + '%', 'left': left + '%'}).appendTo(layer);
                                pin.attr('data-location', value.id);
                                pin.addClass(value.pin);
                            }
                        }

                        if (self.sidebar) self.sidebar.addLocation(value);
                    });

                    nrlevels++;
                });
            }

            // Pin animation
            if (self.o.animate) {
                $('.mapplic-pin').css('opacity', '0');
                window.setTimeout(animateNext, 200);
            }

            function animateNext() {
                var select = $('.mapplic-pin:not(.mapplic-animate):visible');

                //console.log('enter');

                if (select.length > 0) {
                    select.first().addClass('mapplic-animate');
                    window.setTimeout(animateNext, 200);
                }
                else {
                    $('.mapplic-animate').removeClass('mapplic-animate');
                    $('.mapplic-pin').css('opacity', '1');
                }
            }

            // COMPONENTS

            // Hover Tooltip
            if (self.o.hovertip) self.hovertip = new HoverTooltip().init();

            // Tooltip
            self.tooltip = new Tooltip();
            self.tooltip.init();

            // Developer tools
            if (self.o.developer) self.devtools = new DevTools().init();

            // Clear button
            if (self.o.clearbutton) self.clearbutton = new ClearButton().init();

            // Fullscreen
            if (self.o.fullscreen) self.fullscreen = new FullScreen().init();

            // Levels
            if (nrlevels > 1) {
                self.levels = $('<div></div>').addClass('mapplic-levels');
                var up = $('<a href="#"></a>').addClass('mapplic-levels-up').appendTo(self.levels);
                self.levelselect.appendTo(self.levels);
                var down = $('<a href="#"></a>').addClass('mapplic-levels-down').appendTo(self.levels);
                self.container.append(self.levels);

                self.levelselect.change(function() {
                    var value = $(this).val();
                    level(value);
                });

                up.click(function(e) {
                    e.preventDefault();
                    if (!$(this).hasClass('disabled')) level('+');
                });

                down.click(function(e) {
                    e.preventDefault();
                    if (!$(this).hasClass('disabled')) level('-');
                });
            }
            level(shownLevel);

            // Browser resize
            $(window).resize(function() {
                var wr = self.container.width() / self.contentWidth,
                    hr = self.container.height() / self.contentHeight;

                if (wr > hr) self.fitscale = wr;
                else self.fitscale = hr;

                self.scale = normalizeScale(self.scale);
                self.x = normalizeX(self.x);
                self.y = normalizeY(self.y);

                moveTo(self.x, self.y, self.scale, 100);
            }).resize();

            // Deeplinking
            if (self.o.deeplinking) {
                self.deeplinking = new Deeplinking();
                self.deeplinking.init();
            }
        }

        var addControls = function() {
            var map = self.map,
                mapbody = $('.mapplic-map-image', self.map);

            document.ondragstart = function() { return false; } // IE drag fix

            // Drag & drop
            mapbody.on('mousedown', function(event) {
                map.stop();

                map.data('mouseX', event.pageX);
                map.data('mouseY', event.pageY);
                map.data('lastX', self.x);
                map.data('lastY', self.y);

                map.addClass('mapplic-dragging');

                self.map.on('mousemove', function(event) {
                    var x = event.pageX - map.data('mouseX') + self.x;
                    y = event.pageY - map.data('mouseY') + self.y;

                    x = normalizeX(x);
                    y = normalizeY(y);

                    moveTo(x, y);
                    map.data('lastX', x);
                    map.data('lastY', y);
                });

                $(document).on('mouseup', function(event) {
                    self.x = map.data('lastX');
                    self.y = map.data('lastY');

                    self.map.off('mousemove');
                    $(document).off('mouseup');

                    map.removeClass('mapplic-dragging');
                });
            });

            // Double click
            $(document).on('dblclick', '.mapplic-map-image', function(event) {
                var mapPos = self.map.offset();
                var x = (event.pageX - mapPos.left) / self.map.width();
                var y = (event.pageY - mapPos.top) / self.map.height();
                var z = self.map.width() / self.min_width * 2;

                zoomTo(x, y, z, 600);
            });

            // Mousewheel
            $('.mapplic-layer', this.el).bind('mousewheel DOMMouseScroll', function(event, delta) {
                event.preventDefault();

                var scale = self.scale;
                self.scale = normalizeScale(scale + scale * delta/5);

                self.x = normalizeX(self.x - (event.pageX - self.container.offset().left - self.x) * (self.scale/scale - 1));
                self.y = normalizeY(self.y - (event.pageY - self.container.offset().top - self.y) * (self.scale/scale - 1));

                moveTo(self.x, self.y, self.scale, 100);
            });

            // Touch support
            if (!('ontouchstart' in window || 'onmsgesturechange' in window)) return true;

            mapbody.on('touchstart', function(e) {
                var orig = e.originalEvent,
                    pos = map.position();

                map.data('touchY', orig.changedTouches[0].pageY - pos.top);
                map.data('touchX', orig.changedTouches[0].pageX - pos.left);

                mapbody.on('touchmove', function(e) {
                    e.preventDefault();
                    var orig = e.originalEvent;
                    var touches = orig.touches.length;

                    if (touches == 1) {
                        self.x = normalizeX(orig.changedTouches[0].pageX - map.data('touchX'));
                        self.y = normalizeY(orig.changedTouches[0].pageY - map.data('touchY'));

                        moveTo(self.x, self.y, self.scale, 100);
                    }
                    else {
                        mapbody.off('touchmove');
                    }
                });

                mapbody.on('touchend', function(e) {
                    mapbody.off('touchmove touchend');
                });
            });

            // Pinch zoom
            var mapPinch = Hammer(self.map[0], {
                transform_always_block: true,
                drag_block_horizontal: true,
                drag_block_vertical: true
            });

            var scale=1, last_scale;

            mapPinch.on('touch transform', function(ev) {
                switch(ev.type) {
                    case 'touch':
                        last_scale = scale;
                        break;

                    case 'transform':
                        var center = ev.gesture.center;
                        scale = Math.max(1, Math.min(last_scale * ev.gesture.scale, 10));

                        var oldscale = self.scale;
                        self.scale = normalizeScale(scale * self.fitscale);

                        self.x = normalizeX(self.x - (center.pageX - self.container.offset().left - self.x) * (self.scale/oldscale - 1));
                        self.y = normalizeY(self.y - (center.pageY - self.container.offset().top - self.y) * (self.scale/oldscale - 1));

                        moveTo(self.x, self.y, self.scale, 200);

                        break;
                }
            });
        }

        var level = function(target) {
            switch (target) {
                case '+':
                    target = $('option:selected', self.levelselect).removeAttr('selected').prev().prop('selected', 'selected').val();
                    break;
                case '-':
                    target = $('option:selected', self.levelselect).removeAttr('selected').next().prop('selected', 'selected').val();
                    break;
                default:
                    $('option[value="' + target + '"]', self.levelselect).prop('selected', 'selected');
            }

            var layer = $('.mapplic-layer.' + target, self.map);

            // Target layer is active
            if (layer.is(':visible')) return;

            // Hide Tooltip
            self.tooltip.hide();

            // Show target layer
            $('.mapplic-layer:visible', self.map).hide();
            layer.show();

            // Show target minimap layer
            if (self.minimap) self.minimap.show(target);

            // Update control
            var index = self.levelselect.get(0).selectedIndex,
                up = $('.mapplic-levels-up', self.levels),
                down = $('.mapplic-levels-down', self.levels);

            up.removeClass('disabled');
            down.removeClass('disabled');
            if (index == 0) {
                up.addClass('disabled');
            }
            else if (index == self.levelselect.get(0).length - 1) {
                down.addClass('disabled');
            }
        }

        var getLocationData = function(id) {
            var data = null;
            $.each(self.data.levels, function(index, layer) {
                $.each(layer.locations, function(index, value) {
                    if (value.id == id) {
                        data = value;
                    }
                });
            });
            return data;
        }

        var showLocation = function(id, duration) {
            $.each(self.data.levels, function(index, layer) {
                $.each(layer.locations, function(index, value) {
                    if (value.id == id) {
                        var zoom = typeof value.zoom !== 'undefined' ? value.zoom : 4,
                            drop = self.tooltip.drop / self.contentHeight / zoom;

                        level(layer.id);

                        zoomTo(value.x, parseFloat(value.y) - drop, zoom, duration, 'easeInOutCubic');
                    }
                });
            });
        };

        var normalizeX = function(x) {
            var minX = self.container.width() - self.contentWidth * self.scale;

            if (x > 0) x = 0;
            else if (x < minX) x = minX;

            return x;
        }

        var normalizeY = function(y) {
            var minY = self.container.height() - self.contentHeight * self.scale;

            if (y >= 0) y = 0;
            else if (y < minY) y = minY;

            return y;
        }

        var normalizeScale = function(scale) {
            if (scale < self.fitscale) scale = self.fitscale;
            else if (scale > self.o.maxscale) scale = self.o.maxscale;

            return scale;
        }

        var zoomTo = function(x, y, s, duration, easing) {
            duration = typeof duration !== 'undefined' ? duration : 400;

            self.scale = normalizeScale(self.fitscale * s);
            var scale = self.contentWidth * self.scale;

            self.x = normalizeX(self.container.width() * 0.5 - self.scale * self.contentWidth * x);
            self.y = normalizeY(self.container.height() * 0.5 - self.scale * self.contentHeight * y);

            moveTo(self.x, self.y, self.scale, duration, easing);
        }

        var moveTo = function(x, y, scale, d, easing) {
            if (scale !== undefined) {
                self.map.stop().animate({
                    'left': x,
                    'top': y,
                    'width': self.contentWidth * scale,
                    'height': self.contentHeight * scale
                }, d, easing);
            }
            else {
                self.map.css({
                    'left': x,
                    'top': y
                });
            }
            if (self.minimap) self.minimap.update(x, y);
        }
    };

    //  Create a jQuery plugin
    $.fn.mapplic = function(params) {
        var len = this.length;

        return this.each(function(index) {
            var me = $(this),
                key = 'mapplic' + (len > 1 ? '-' + ++index : ''),
                instance = (new Mapplic).init(me, params);

            me.data(key, instance).data('key', key);
        });
    };
})(jQuery);

(function(root,factory){if(typeof define==="function"&&define.amd){define(["d3"],function(d3){return root.Rickshaw=factory(d3)})}else if(typeof exports==="object")
{module.exports=factory(require("d3"))}else{root.Rickshaw=factory(d3)}})(this,function(d3){var Rickshaw={namespace:function(namespace,obj){var parts=namespace.split(".");var parent=Rickshaw;for(var i=1,length=parts.length;i<length;i++){var currentPart=parts[i];parent[currentPart]=parent[currentPart]||{};parent=parent[currentPart]}return parent},keys:function(obj){var keys=[];for(var key in obj)keys.push(key);return keys},extend:function(destination,source){for(var property in source){destination[property]=source[property]}return destination},clone:function(obj){return JSON.parse(JSON.stringify(obj))}};(function(globalContext){var _toString=Object.prototype.toString,NULL_TYPE="Null",UNDEFINED_TYPE="Undefined",BOOLEAN_TYPE="Boolean",NUMBER_TYPE="Number",STRING_TYPE="String",OBJECT_TYPE="Object",FUNCTION_CLASS="[object Function]";function isFunction(object){return _toString.call(object)===FUNCTION_CLASS}function extend(destination,source){for(var property in source)if(source.hasOwnProperty(property))destination[property]=source[property];return destination}function keys(object){if(Type(object)!==OBJECT_TYPE){throw new TypeError}var results=[];for(var property in object){if(object.hasOwnProperty(property)){results.push(property)}}return results}function Type(o){switch(o){case null:return NULL_TYPE;case void 0:return UNDEFINED_TYPE}var type=typeof o;switch(type){case"boolean":return BOOLEAN_TYPE;case"number":return NUMBER_TYPE;case"string":return STRING_TYPE}return OBJECT_TYPE}function isUndefined(object){return typeof object==="undefined"}var slice=Array.prototype.slice;function argumentNames(fn){var names=fn.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1].replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g,"").replace(/\s+/g,"").split(",");return names.length==1&&!names[0]?[]:names}function wrap(fn,wrapper){var __method=fn;return function(){var a=update([bind(__method,this)],arguments);return wrapper.apply(this,a)}}function update(array,args){var arrayLength=array.length,length=args.length;while(length--)array[arrayLength+length]=args[length];return array}function merge(array,args){array=slice.call(array,0);return update(array,args)}function bind(fn,context){if(arguments.length<2&&isUndefined(arguments[0]))return this;var __method=fn,args=slice.call(arguments,2);return function(){var a=merge(args,arguments);return __method.apply(context,a)}}var emptyFunction=function(){};var Class=function(){var IS_DONTENUM_BUGGY=function(){for(var p in{toString:1}){if(p==="toString")return false}return true}();function subclass(){}function create(){var parent=null,properties=[].slice.apply(arguments);if(isFunction(properties[0]))parent=properties.shift();function klass(){this.initialize.apply(this,arguments)}extend(klass,Class.Methods);klass.superclass=parent;klass.subclasses=[];if(parent){subclass.prototype=parent.prototype;klass.prototype=new subclass;try{parent.subclasses.push(klass)}catch(e){}}for(var i=0,length=properties.length;i<length;i++)klass.addMethods(properties[i]);if(!klass.prototype.initialize)klass.prototype.initialize=emptyFunction;klass.prototype.constructor=klass;return klass}function addMethods(source){var ancestor=this.superclass&&this.superclass.prototype,properties=keys(source);if(IS_DONTENUM_BUGGY){if(source.toString!=Object.prototype.toString)properties.push("toString");if(source.valueOf!=Object.prototype.valueOf)properties.push("valueOf")}for(var i=0,length=properties.length;i<length;i++){var property=properties[i],value=source[property];if(ancestor&&isFunction(value)&&argumentNames(value)[0]=="$super"){var method=value;value=wrap(function(m){return function(){return ancestor[m].apply(this,arguments)}}(property),method);value.valueOf=bind(method.valueOf,method);value.toString=bind(method.toString,method)}this.prototype[property]=value}return this}return{create:create,Methods:{addMethods:addMethods}}}();if(globalContext.exports){globalContext.exports.Class=Class}else{globalContext.Class=Class}})(Rickshaw);Rickshaw.namespace("Rickshaw.Compat.ClassList");Rickshaw.Compat.ClassList=function(){if(typeof document!=="undefined"&&!("classList"in document.createElement("a"))){(function(view){"";"use strict";var classListProp="classList",protoProp="prototype",elemCtrProto=(view.HTMLElement||view.Element)[protoProp],objCtr=Object,strTrim=String[protoProp].trim||function(){return this.replace(/^\s+|\s+$/g,"")},arrIndexOf=Array[protoProp].indexOf||function(item){var i=0,len=this.length;for(;i<len;i++){if(i in this&&this[i]===item){return i}}return-1},DOMEx=function(type,message){this.name=type;this.code=DOMException[type];this.message=message},checkTokenAndGetIndex=function(classList,token){if(token===""){throw new DOMEx("SYNTAX_ERR","An invalid or illegal string was specified")}if(/\s/.test(token)){throw new DOMEx("INVALID_CHARACTER_ERR","String contains an invalid character")}return arrIndexOf.call(classList,token)},ClassList=function(elem){var trimmedClasses=strTrim.call(elem.className),classes=trimmedClasses?trimmedClasses.split(/\s+/):[],i=0,len=classes.length;for(;i<len;i++){this.push(classes[i])}this._updateClassName=function(){elem.className=this.toString()}},classListProto=ClassList[protoProp]=[],classListGetter=function(){return new ClassList(this)};DOMEx[protoProp]=Error[protoProp];classListProto.item=function(i){return this[i]||null};classListProto.contains=function(token){token+="";return checkTokenAndGetIndex(this,token)!==-1};classListProto.add=function(token){token+="";if(checkTokenAndGetIndex(this,token)===-1){this.push(token);this._updateClassName()}};classListProto.remove=function(token){token+="";var index=checkTokenAndGetIndex(this,token);if(index!==-1){this.splice(index,1);this._updateClassName()}};classListProto.toggle=function(token){token+="";if(checkTokenAndGetIndex(this,token)===-1){this.add(token)}else{this.remove(token)}};classListProto.toString=function(){return this.join(" ")};if(objCtr.defineProperty){var classListPropDesc={get:classListGetter,enumerable:true,configurable:true};try{objCtr.defineProperty(elemCtrProto,classListProp,classListPropDesc)}catch(ex){if(ex.number===-2146823252){classListPropDesc.enumerable=false;objCtr.defineProperty(elemCtrProto,classListProp,classListPropDesc)}}}else if(objCtr[protoProp].__defineGetter__){elemCtrProto.__defineGetter__(classListProp,classListGetter)}})(window)}};if(typeof RICKSHAW_NO_COMPAT!=="undefined"&&!RICKSHAW_NO_COMPAT||typeof RICKSHAW_NO_COMPAT==="undefined"){new Rickshaw.Compat.ClassList}Rickshaw.namespace("Rickshaw.Graph");Rickshaw.Graph=function(args){var self=this;this.initialize=function(args){if(!args.element)throw"Rickshaw.Graph needs a reference to an element";if(args.element.nodeType!==1)throw"Rickshaw.Graph element was defined but not an HTML element";this.element=args.element;this.series=args.series;this.window={};this.updateCallbacks=[];this.configureCallbacks=[];this.defaults={interpolation:"cardinal",offset:"zero",min:undefined,max:undefined,preserve:false,xScale:undefined,yScale:undefined,stack:true};this._loadRenderers();this.configure(args);this.validateSeries(args.series);this.series.active=function(){return self.series.filter(function(s){return!s.disabled})};this.setSize({width:args.width,height:args.height});this.element.classList.add("rickshaw_graph");this.vis=d3.select(this.element).append("svg:svg").attr("width",this.width).attr("height",this.height);this.discoverRange()};this._loadRenderers=function(){for(var name in Rickshaw.Graph.Renderer){if(!name||!Rickshaw.Graph.Renderer.hasOwnProperty(name))continue;var r=Rickshaw.Graph.Renderer[name];if(!r||!r.prototype||!r.prototype.render)continue;self.registerRenderer(new r({graph:self}))}};this.validateSeries=function(series){if(!Array.isArray(series)&&!(series instanceof Rickshaw.Series)){var seriesSignature=Object.prototype.toString.apply(series);throw"series is not an array: "+seriesSignature}var pointsCount;series.forEach(function(s){if(!(s instanceof Object)){throw"series element is not an object: "+s}if(!s.data){throw"series has no data: "+JSON.stringify(s)}if(!Array.isArray(s.data)){throw"series data is not an array: "+JSON.stringify(s.data)}if(s.data.length>0){var x=s.data[0].x;var y=s.data[0].y;if(typeof x!="number"||typeof y!="number"&&y!==null){throw"x and y properties of points should be numbers instead of "+typeof x+" and "+typeof y}}if(s.data.length>=3){if(s.data[2].x<s.data[1].x||s.data[1].x<s.data[0].x||s.data[s.data.length-1].x<s.data[0].x){throw"series data needs to be sorted on x values for series name: "+s.name}}},this)};this.dataDomain=function(){var data=this.series.map(function(s){return s.data});var min=d3.min(data.map(function(d){return d[0].x}));var max=d3.max(data.map(function(d){return d[d.length-1].x}));return[min,max]};this.discoverRange=function(){var domain=this.renderer.domain();this.x=(this.xScale||d3.scale.linear()).copy().domain(domain.x).range([0,this.width]);this.y=(this.yScale||d3.scale.linear()).copy().domain(domain.y).range([this.height,0]);this.x.magnitude=d3.scale.linear().domain([domain.x[0]-domain.x[0],domain.x[1]-domain.x[0]]).range([0,this.width]);this.y.magnitude=d3.scale.linear().domain([domain.y[0]-domain.y[0],domain.y[1]-domain.y[0]]).range([0,this.height])};this.render=function(){var stackedData=this.stackData();this.discoverRange();this.renderer.render();this.updateCallbacks.forEach(function(callback){callback()})};this.update=this.render;this.stackData=function(){var data=this.series.active().map(function(d){return d.data}).map(function(d){return d.filter(function(d){return this._slice(d)},this)},this);var preserve=this.preserve;if(!preserve){this.series.forEach(function(series){if(series.scale){preserve=true}})}data=preserve?Rickshaw.clone(data):data;this.series.active().forEach(function(series,index){if(series.scale){var seriesData=data[index];if(seriesData){seriesData.forEach(function(d){d.y=series.scale(d.y)})}}});this.stackData.hooks.data.forEach(function(entry){data=entry.f.apply(self,[data])});var stackedData;if(!this.renderer.unstack){this._validateStackable();var layout=d3.layout.stack();layout.offset(self.offset);stackedData=layout(data)}stackedData=stackedData||data;if(this.renderer.unstack){stackedData.forEach(function(seriesData){seriesData.forEach(function(d){d.y0=d.y0===undefined?0:d.y0})})}this.stackData.hooks.after.forEach(function(entry){stackedData=entry.f.apply(self,[data])});var i=0;this.series.forEach(function(series){if(series.disabled)return;series.stack=stackedData[i++]});this.stackedData=stackedData;return stackedData};this._validateStackable=function(){var series=this.series;var pointsCount;series.forEach(function(s){pointsCount=pointsCount||s.data.length;if(pointsCount&&s.data.length!=pointsCount){throw"stacked series cannot have differing numbers of points: "+pointsCount+" vs "+s.data.length+"; see Rickshaw.Series.fill()"}},this)};this.stackData.hooks={data:[],after:[]};this._slice=function(d){if(this.window.xMin||this.window.xMax){var isInRange=true;if(this.window.xMin&&d.x<this.window.xMin)isInRange=false;if(this.window.xMax&&d.x>this.window.xMax)isInRange=false;return isInRange}return true};this.onUpdate=function(callback){this.updateCallbacks.push(callback)};this.onConfigure=function(callback){this.configureCallbacks.push(callback)};this.registerRenderer=function(renderer){this._renderers=this._renderers||{};this._renderers[renderer.name]=renderer};this.configure=function(args){this.config=this.config||{};if(args.width||args.height){this.setSize(args)}Rickshaw.keys(this.defaults).forEach(function(k){this.config[k]=k in args?args[k]:k in this?this[k]:this.defaults[k]},this);Rickshaw.keys(this.config).forEach(function(k){this[k]=this.config[k]},this);if("stack"in args)args.unstack=!args.stack;var renderer=args.renderer||this.renderer&&this.renderer.name||"stack";this.setRenderer(renderer,args);this.configureCallbacks.forEach(function(callback){callback(args)})};this.setRenderer=function(r,args){if(typeof r=="function"){this.renderer=new r({graph:self});this.registerRenderer(this.renderer)}else{if(!this._renderers[r]){throw"couldn't find renderer "+r}this.renderer=this._renderers[r]}if(typeof args=="object"){this.renderer.configure(args)}};this.setSize=function(args){args=args||{};if(typeof window!==undefined){var style=window.getComputedStyle(this.element,null);var elementWidth=parseInt(style.getPropertyValue("width"),10);var elementHeight=parseInt(style.getPropertyValue("height"),10)}this.width=args.width||elementWidth||400;this.height=args.height||elementHeight||250;this.vis&&this.vis.attr("width",this.width).attr("height",this.height)};this.initialize(args)};Rickshaw.namespace("Rickshaw.Fixtures.Color");Rickshaw.Fixtures.Color=function(){this.schemes={};this.schemes.spectrum14=["#ecb796","#dc8f70","#b2a470","#92875a","#716c49","#d2ed82","#bbe468","#a1d05d","#e7cbe6","#d8aad6","#a888c2","#9dc2d3","#649eb9","#387aa3"].reverse();this.schemes.spectrum2000=["#57306f","#514c76","#646583","#738394","#6b9c7d","#84b665","#a7ca50","#bfe746","#e2f528","#fff726","#ecdd00","#d4b11d","#de8800","#de4800","#c91515","#9a0000","#7b0429","#580839","#31082b"];this.schemes.spectrum2001=["#2f243f","#3c2c55","#4a3768","#565270","#6b6b7c","#72957f","#86ad6e","#a1bc5e","#b8d954","#d3e04e","#ccad2a","#cc8412","#c1521d","#ad3821","#8a1010","#681717","#531e1e","#3d1818","#320a1b"];this.schemes.classic9=["#423d4f","#4a6860","#848f39","#a2b73c","#ddcb53","#c5a32f","#7d5836","#963b20","#7c2626","#491d37","#2f254a"].reverse();this.schemes.httpStatus={503:"#ea5029",502:"#d23f14",500:"#bf3613",410:"#efacea",409:"#e291dc",403:"#f457e8",408:"#e121d2",401:"#b92dae",405:"#f47ceb",404:"#a82a9f",400:"#b263c6",301:"#6fa024",302:"#87c32b",307:"#a0d84c",304:"#28b55c",200:"#1a4f74",206:"#27839f",201:"#52adc9",202:"#7c979f",203:"#a5b8bd",204:"#c1cdd1"};this.schemes.colorwheel=["#b5b6a9","#858772","#785f43","#96557e","#4682b4","#65b9ac","#73c03a","#cb513a"].reverse();this.schemes.cool=["#5e9d2f","#73c03a","#4682b4","#7bc3b8","#a9884e","#c1b266","#a47493","#c09fb5"];this.schemes.munin=["#00cc00","#0066b3","#ff8000","#ffcc00","#330099","#990099","#ccff00","#ff0000","#808080","#008f00","#00487d","#b35a00","#b38f00","#6b006b","#8fb300","#b30000","#bebebe","#80ff80","#80c9ff","#ffc080","#ffe680","#aa80ff","#ee00cc","#ff8080","#666600","#ffbfff","#00ffcc","#cc6699","#999900"]};Rickshaw.namespace("Rickshaw.Fixtures.RandomData");Rickshaw.Fixtures.RandomData=function(timeInterval){var addData;timeInterval=timeInterval||1;var lastRandomValue=200;var timeBase=Math.floor((new Date).getTime()/1e3);this.addData=function(data){var randomValue=Math.random()*100+15+lastRandomValue;var index=data[0].length;var counter=1;data.forEach(function(series){var randomVariance=Math.random()*20;var v=randomValue/25+counter++ +(Math.cos(index*counter*11/960)+2)*15+(Math.cos(index/7)+2)*7+(Math.cos(index/17)+2)*1;series.push({x:index*timeInterval+timeBase,y:v+randomVariance})});lastRandomValue=randomValue*.85};this.removeData=function(data){data.forEach(function(series){series.shift()});timeBase+=timeInterval}};Rickshaw.namespace("Rickshaw.Fixtures.Time");Rickshaw.Fixtures.Time=function(){var self=this;this.months=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];this.units=[{name:"decade",seconds:86400*365.25*10,formatter:function(d){return parseInt(d.getUTCFullYear()/10,10)*10}},{name:"year",seconds:86400*365.25,formatter:function(d){return d.getUTCFullYear()}},{name:"month",seconds:86400*30.5,formatter:function(d){return self.months[d.getUTCMonth()]}},{name:"week",seconds:86400*7,formatter:function(d){return self.formatDate(d)}},{name:"day",seconds:86400,formatter:function(d){return d.getUTCDate()}},{name:"6 hour",seconds:3600*6,formatter:function(d){return self.formatTime(d)}},{name:"hour",seconds:3600,formatter:function(d){return self.formatTime(d)}},{name:"15 minute",seconds:60*15,formatter:function(d){return self.formatTime(d)}},{name:"minute",seconds:60,formatter:function(d){return d.getUTCMinutes()}},{name:"15 second",seconds:15,formatter:function(d){return d.getUTCSeconds()+"s"}},{name:"second",seconds:1,formatter:function(d){return d.getUTCSeconds()+"s"}},{name:"decisecond",seconds:1/10,formatter:function(d){return d.getUTCMilliseconds()+"ms"}},{name:"centisecond",seconds:1/100,formatter:function(d){return d.getUTCMilliseconds()+"ms"}}];this.unit=function(unitName){return this.units.filter(function(unit){return unitName==unit.name}).shift()};this.formatDate=function(d){return d3.time.format("%b %e")(d)};this.formatTime=function(d){return d.toUTCString().match(/(\d+:\d+):/)[1]};this.ceil=function(time,unit){var date,floor,year;if(unit.name=="month"){date=new Date(time*1e3);floor=Date.UTC(date.getUTCFullYear(),date.getUTCMonth())/1e3;if(floor==time)return time;year=date.getUTCFullYear();var month=date.getUTCMonth();if(month==11){month=0;year=year+1}else{month+=1}return Date.UTC(year,month)/1e3}if(unit.name=="year"){date=new Date(time*1e3);floor=Date.UTC(date.getUTCFullYear(),0)/1e3;if(floor==time)return time;year=date.getUTCFullYear()+1;return Date.UTC(year,0)/1e3}return Math.ceil(time/unit.seconds)*unit.seconds}};Rickshaw.namespace("Rickshaw.Fixtures.Time.Local");Rickshaw.Fixtures.Time.Local=function(){var self=this;this.months=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];this.units=[{name:"decade",seconds:86400*365.25*10,formatter:function(d){return parseInt(d.getFullYear()/10,10)*10}},{name:"year",seconds:86400*365.25,formatter:function(d){return d.getFullYear()}},{name:"month",seconds:86400*30.5,formatter:function(d){return self.months[d.getMonth()]}},{name:"week",seconds:86400*7,formatter:function(d){return self.formatDate(d)}},{name:"day",seconds:86400,formatter:function(d){return d.getDate()}},{name:"6 hour",seconds:3600*6,formatter:function(d){return self.formatTime(d)}},{name:"hour",seconds:3600,formatter:function(d){return self.formatTime(d)}},{name:"15 minute",seconds:60*15,formatter:function(d){return self.formatTime(d)}},{name:"minute",seconds:60,formatter:function(d){return d.getMinutes()}},{name:"15 second",seconds:15,formatter:function(d){return d.getSeconds()+"s"}},{name:"second",seconds:1,formatter:function(d){return d.getSeconds()+"s"}},{name:"decisecond",seconds:1/10,formatter:function(d){return d.getMilliseconds()+"ms"}},{name:"centisecond",seconds:1/100,formatter:function(d){return d.getMilliseconds()+"ms"}}];this.unit=function(unitName){return this.units.filter(function(unit){return unitName==unit.name}).shift()};this.formatDate=function(d){return d3.time.format("%b %e")(d)};this.formatTime=function(d){return d.toString().match(/(\d+:\d+):/)[1]};this.ceil=function(time,unit){var date,floor,year;if(unit.name=="day"){var nearFuture=new Date((time+unit.seconds-1)*1e3);var rounded=new Date(0);rounded.setMilliseconds(0);rounded.setSeconds(0);rounded.setMinutes(0);rounded.setHours(0);rounded.setDate(nearFuture.getDate());rounded.setMonth(nearFuture.getMonth());rounded.setFullYear(nearFuture.getFullYear());return rounded.getTime()/1e3}if(unit.name=="month"){date=new Date(time*1e3);floor=new Date(date.getFullYear(),date.getMonth()).getTime()/1e3;if(floor==time)return time;year=date.getFullYear();var month=date.getMonth();if(month==11){month=0;year=year+1}else{month+=1}return new Date(year,month).getTime()/1e3}if(unit.name=="year"){date=new Date(time*1e3);floor=new Date(date.getUTCFullYear(),0).getTime()/1e3;if(floor==time)return time;year=date.getFullYear()+1;return new Date(year,0).getTime()/1e3}return Math.ceil(time/unit.seconds)*unit.seconds}};Rickshaw.namespace("Rickshaw.Fixtures.Number");Rickshaw.Fixtures.Number.formatKMBT=function(y){var abs_y=Math.abs(y);if(abs_y>=1e12){return y/1e12+"T"}else if(abs_y>=1e9){return y/1e9+"B"}else if(abs_y>=1e6){return y/1e6+"M"}else if(abs_y>=1e3){return y/1e3+"K"}else if(abs_y<1&&y>0){return y.toFixed(2)}else if(abs_y===0){return""}else{return y}};Rickshaw.Fixtures.Number.formatBase1024KMGTP=function(y){var abs_y=Math.abs(y);if(abs_y>=0x4000000000000){return y/0x4000000000000+"P"}else if(abs_y>=1099511627776){return y/1099511627776+"T"}else if(abs_y>=1073741824){return y/1073741824+"G"}else if(abs_y>=1048576){return y/1048576+"M"}else if(abs_y>=1024){return y/1024+"K"}else if(abs_y<1&&y>0){return y.toFixed(2)}else if(abs_y===0){return""}else{return y}};Rickshaw.namespace("Rickshaw.Color.Palette");Rickshaw.Color.Palette=function(args){var color=new Rickshaw.Fixtures.Color;args=args||{};this.schemes={};this.scheme=color.schemes[args.scheme]||args.scheme||color.schemes.colorwheel;this.runningIndex=0;this.generatorIndex=0;if(args.interpolatedStopCount){var schemeCount=this.scheme.length-1;var i,j,scheme=[];for(i=0;i<schemeCount;i++){scheme.push(this.scheme[i]);var generator=d3.interpolateHsl(this.scheme[i],this.scheme[i+1]);for(j=1;j<args.interpolatedStopCount;j++){scheme.push(generator(1/args.interpolatedStopCount*j))}}scheme.push(this.scheme[this.scheme.length-1]);this.scheme=scheme}this.rotateCount=this.scheme.length;this.color=function(key){return this.scheme[key]||this.scheme[this.runningIndex++]||this.interpolateColor()||"#808080"};this.interpolateColor=function(){if(!Array.isArray(this.scheme))return;var color;if(this.generatorIndex==this.rotateCount*2-1){color=d3.interpolateHsl(this.scheme[this.generatorIndex],this.scheme[0])(.5);this.generatorIndex=0;this.rotateCount*=2}else{color=d3.interpolateHsl(this.scheme[this.generatorIndex],this.scheme[this.generatorIndex+1])(.5);this.generatorIndex++}this.scheme.push(color);return color}};Rickshaw.namespace("Rickshaw.Graph.Ajax");Rickshaw.Graph.Ajax=Rickshaw.Class.create({initialize:function(args){this.dataURL=args.dataURL;this.onData=args.onData||function(d){return d};this.onComplete=args.onComplete||function(){};this.onError=args.onError||function(){};this.args=args;this.request()},request:function(){jQuery.ajax({url:this.dataURL,dataType:"json",success:this.success.bind(this),error:this.error.bind(this)})},error:function(){console.log("error loading dataURL: "+this.dataURL);this.onError(this)},success:function(data,status){data=this.onData(data);this.args.series=this._splice({data:data,series:this.args.series});this.graph=this.graph||new Rickshaw.Graph(this.args);this.graph.render();this.onComplete(this)},_splice:function(args){var data=args.data;var series=args.series;if(!args.series)return data;series.forEach(function(s){var seriesKey=s.key||s.name;if(!seriesKey)throw"series needs a key or a name";data.forEach(function(d){var dataKey=d.key||d.name;if(!dataKey)throw"data needs a key or a name";if(seriesKey==dataKey){var properties=["color","name","data"];properties.forEach(function(p){if(d[p])s[p]=d[p]})}})});return series}});Rickshaw.namespace("Rickshaw.Graph.Annotate");Rickshaw.Graph.Annotate=function(args){var graph=this.graph=args.graph;this.elements={timeline:args.element};var self=this;this.data={};this.elements.timeline.classList.add("rickshaw_annotation_timeline");this.add=function(time,content,end_time){self.data[time]=self.data[time]||{boxes:[]};self.data[time].boxes.push({content:content,end:end_time})};this.update=function(){Rickshaw.keys(self.data).forEach(function(time){var annotation=self.data[time];var left=self.graph.x(time);if(left<0||left>self.graph.x.range()[1]){if(annotation.element){annotation.line.classList.add("offscreen");annotation.element.style.display="none"}annotation.boxes.forEach(function(box){if(box.rangeElement)box.rangeElement.classList.add("offscreen")});return}if(!annotation.element){var element=annotation.element=document.createElement("div");element.classList.add("annotation");this.elements.timeline.appendChild(element);element.addEventListener("click",function(e){element.classList.toggle("active");annotation.line.classList.toggle("active");annotation.boxes.forEach(function(box){if(box.rangeElement)box.rangeElement.classList.toggle("active")})},false)}annotation.element.style.left=left+"px";annotation.element.style.display="block";annotation.boxes.forEach(function(box){var element=box.element;if(!element){element=box.element=document.createElement("div");element.classList.add("content");element.innerHTML=box.content;annotation.element.appendChild(element);annotation.line=document.createElement("div");annotation.line.classList.add("annotation_line");self.graph.element.appendChild(annotation.line);if(box.end){box.rangeElement=document.createElement("div");box.rangeElement.classList.add("annotation_range");self.graph.element.appendChild(box.rangeElement)}}if(box.end){var annotationRangeStart=left;var annotationRangeEnd=Math.min(self.graph.x(box.end),self.graph.x.range()[1]);if(annotationRangeStart>annotationRangeEnd){annotationRangeEnd=left;annotationRangeStart=Math.max(self.graph.x(box.end),self.graph.x.range()[0])}var annotationRangeWidth=annotationRangeEnd-annotationRangeStart;box.rangeElement.style.left=annotationRangeStart+"px";box.rangeElement.style.width=annotationRangeWidth+"px";box.rangeElement.classList.remove("offscreen")}annotation.line.classList.remove("offscreen");annotation.line.style.left=left+"px"})},this)};this.graph.onUpdate(function(){self.update()})};Rickshaw.namespace("Rickshaw.Graph.Axis.Time");Rickshaw.Graph.Axis.Time=function(args){var self=this;this.graph=args.graph;this.elements=[];this.ticksTreatment=args.ticksTreatment||"plain";this.fixedTimeUnit=args.timeUnit;var time=args.timeFixture||new Rickshaw.Fixtures.Time;this.appropriateTimeUnit=function(){var unit;var units=time.units;var domain=this.graph.x.domain();var rangeSeconds=domain[1]-domain[0];units.forEach(function(u){if(Math.floor(rangeSeconds/u.seconds)>=2){unit=unit||u}});return unit||time.units[time.units.length-1]};this.tickOffsets=function(){var domain=this.graph.x.domain();var unit=this.fixedTimeUnit||this.appropriateTimeUnit();var count=Math.ceil((domain[1]-domain[0])/unit.seconds);var runningTick=domain[0];var offsets=[];for(var i=0;i<count;i++){var tickValue=time.ceil(runningTick,unit);runningTick=tickValue+unit.seconds/2;offsets.push({value:tickValue,unit:unit})}return offsets};this.render=function(){this.elements.forEach(function(e){e.parentNode.removeChild(e)});this.elements=[];var offsets=this.tickOffsets();offsets.forEach(function(o){if(self.graph.x(o.value)>self.graph.x.range()[1])return;var element=document.createElement("div");element.style.left=self.graph.x(o.value)+"px";element.classList.add("x_tick");element.classList.add(self.ticksTreatment);var title=document.createElement("div");title.classList.add("title");title.innerHTML=o.unit.formatter(new Date(o.value*1e3));element.appendChild(title);self.graph.element.appendChild(element);self.elements.push(element)})};this.graph.onUpdate(function(){self.render()})};Rickshaw.namespace("Rickshaw.Graph.Axis.X");Rickshaw.Graph.Axis.X=function(args){var self=this;var berthRate=.1;this.initialize=function(args){this.graph=args.graph;this.orientation=args.orientation||"top";this.pixelsPerTick=args.pixelsPerTick||75;if(args.ticks)this.staticTicks=args.ticks;if(args.tickValues)this.tickValues=args.tickValues;this.tickSize=args.tickSize||4;this.ticksTreatment=args.ticksTreatment||"plain";if(args.element){this.element=args.element;this._discoverSize(args.element,args);this.vis=d3.select(args.element).append("svg:svg").attr("height",this.height).attr("width",this.width).attr("class","rickshaw_graph x_axis_d3");this.element=this.vis[0][0];this.element.style.position="relative";this.setSize({width:args.width,height:args.height})}else{this.vis=this.graph.vis}this.graph.onUpdate(function(){self.render()})};this.setSize=function(args){args=args||{};if(!this.element)return;this._discoverSize(this.element.parentNode,args);this.vis.attr("height",this.height).attr("width",this.width*(1+berthRate));var berth=Math.floor(this.width*berthRate/2);this.element.style.left=-1*berth+"px"};this.render=function(){if(this._renderWidth!==undefined&&this.graph.width!==this._renderWidth)this.setSize({auto:true});var axis=d3.svg.axis().scale(this.graph.x).orient(this.orientation);axis.tickFormat(args.tickFormat||function(x){return x});if(this.tickValues)axis.tickValues(this.tickValues);this.ticks=this.staticTicks||Math.floor(this.graph.width/this.pixelsPerTick);var berth=Math.floor(this.width*berthRate/2)||0;var transform;if(this.orientation=="top"){var yOffset=this.height||this.graph.height;transform="translate("+berth+","+yOffset+")"}else{transform="translate("+berth+", 0)"}if(this.element){this.vis.selectAll("*").remove()}this.vis.append("svg:g").attr("class",["x_ticks_d3",this.ticksTreatment].join(" ")).attr("transform",transform).call(axis.ticks(this.ticks).tickSubdivide(0).tickSize(this.tickSize));var gridSize=(this.orientation=="bottom"?1:-1)*this.graph.height;this.graph.vis.append("svg:g").attr("class","x_grid_d3").call(axis.ticks(this.ticks).tickSubdivide(0).tickSize(gridSize)).selectAll("text").each(function(){this.parentNode.setAttribute("data-x-value",this.textContent)});this._renderHeight=this.graph.height};this._discoverSize=function(element,args){if(typeof window!=="undefined"){var style=window.getComputedStyle(element,null);var elementHeight=parseInt(style.getPropertyValue("height"),10);if(!args.auto){var elementWidth=parseInt(style.getPropertyValue("width"),10)}}this.width=(args.width||elementWidth||this.graph.width)*(1+berthRate);this.height=args.height||elementHeight||40};this.initialize(args)};Rickshaw.namespace("Rickshaw.Graph.Axis.Y");Rickshaw.Graph.Axis.Y=Rickshaw.Class.create({initialize:function(args){this.graph=args.graph;this.orientation=args.orientation||"right";this.pixelsPerTick=args.pixelsPerTick||75;if(args.ticks)this.staticTicks=args.ticks;if(args.tickValues)this.tickValues=args.tickValues;this.tickSize=args.tickSize||4;this.ticksTreatment=args.ticksTreatment||"plain";this.tickFormat=args.tickFormat||function(y){return y};this.berthRate=.1;if(args.element){this.element=args.element;this.vis=d3.select(args.element).append("svg:svg").attr("class","rickshaw_graph y_axis");this.element=this.vis[0][0];this.element.style.position="relative";this.setSize({width:args.width,height:args.height})}else{this.vis=this.graph.vis}var self=this;this.graph.onUpdate(function(){self.render()})},setSize:function(args){args=args||{};if(!this.element)return;if(typeof window!=="undefined"){var style=window.getComputedStyle(this.element.parentNode,null);var elementWidth=parseInt(style.getPropertyValue("width"),10);if(!args.auto){var elementHeight=parseInt(style.getPropertyValue("height"),10)}}this.width=args.width||elementWidth||this.graph.width*this.berthRate;this.height=args.height||elementHeight||this.graph.height;this.vis.attr("width",this.width).attr("height",this.height*(1+this.berthRate));var berth=this.height*this.berthRate;if(this.orientation=="left"){this.element.style.top=-1*berth+"px"}},render:function(){if(this._renderHeight!==undefined&&this.graph.height!==this._renderHeight)this.setSize({auto:true});this.ticks=this.staticTicks||Math.floor(this.graph.height/this.pixelsPerTick);var axis=this._drawAxis(this.graph.y);this._drawGrid(axis);this._renderHeight=this.graph.height},_drawAxis:function(scale){var axis=d3.svg.axis().scale(scale).orient(this.orientation);axis.tickFormat(this.tickFormat);if(this.tickValues)axis.tickValues(this.tickValues);if(this.orientation=="left"){var berth=this.height*this.berthRate;var transform="translate("+this.width+", "+berth+")"}if(this.element){this.vis.selectAll("*").remove()}this.vis.append("svg:g").attr("class",["y_ticks",this.ticksTreatment].join(" ")).attr("transform",transform).call(axis.ticks(this.ticks).tickSubdivide(0).tickSize(this.tickSize));return axis},_drawGrid:function(axis){var gridSize=(this.orientation=="right"?1:-1)*this.graph.width;this.graph.vis.append("svg:g").attr("class","y_grid").call(axis.ticks(this.ticks).tickSubdivide(0).tickSize(gridSize)).selectAll("text").each(function(){this.parentNode.setAttribute("data-y-value",this.textContent)
})}});Rickshaw.namespace("Rickshaw.Graph.Axis.Y.Scaled");Rickshaw.Graph.Axis.Y.Scaled=Rickshaw.Class.create(Rickshaw.Graph.Axis.Y,{initialize:function($super,args){if(typeof args.scale==="undefined"){throw new Error("Scaled requires scale")}this.scale=args.scale;if(typeof args.grid==="undefined"){this.grid=true}else{this.grid=args.grid}$super(args)},_drawAxis:function($super,scale){var domain=this.scale.domain();var renderDomain=this.graph.renderer.domain().y;var extents=[Math.min.apply(Math,domain),Math.max.apply(Math,domain)];var extentMap=d3.scale.linear().domain([0,1]).range(extents);var adjExtents=[extentMap(renderDomain[0]),extentMap(renderDomain[1])];var adjustment=d3.scale.linear().domain(extents).range(adjExtents);var adjustedScale=this.scale.copy().domain(domain.map(adjustment)).range(scale.range());return $super(adjustedScale)},_drawGrid:function($super,axis){if(this.grid){$super(axis)}}});Rickshaw.namespace("Rickshaw.Graph.Behavior.Series.Highlight");Rickshaw.Graph.Behavior.Series.Highlight=function(args){this.graph=args.graph;this.legend=args.legend;var self=this;var colorSafe={};var activeLine=null;var disabledColor=args.disabledColor||function(seriesColor){return d3.interpolateRgb(seriesColor,d3.rgb("#d8d8d8"))(.8).toString()};this.addHighlightEvents=function(l){l.element.addEventListener("mouseover",function(e){if(activeLine)return;else activeLine=l;self.legend.lines.forEach(function(line){if(l===line){if(self.graph.renderer.unstack&&(line.series.renderer?line.series.renderer.unstack:true)){var seriesIndex=self.graph.series.indexOf(line.series);line.originalIndex=seriesIndex;var series=self.graph.series.splice(seriesIndex,1)[0];self.graph.series.push(series)}return}colorSafe[line.series.name]=colorSafe[line.series.name]||line.series.color;line.series.color=disabledColor(line.series.color)});self.graph.update()},false);l.element.addEventListener("mouseout",function(e){if(!activeLine)return;else activeLine=null;self.legend.lines.forEach(function(line){if(l===line&&line.hasOwnProperty("originalIndex")){var series=self.graph.series.pop();self.graph.series.splice(line.originalIndex,0,series);delete line.originalIndex}if(colorSafe[line.series.name]){line.series.color=colorSafe[line.series.name]}});self.graph.update()},false)};if(this.legend){this.legend.lines.forEach(function(l){self.addHighlightEvents(l)})}};Rickshaw.namespace("Rickshaw.Graph.Behavior.Series.Order");Rickshaw.Graph.Behavior.Series.Order=function(args){this.graph=args.graph;this.legend=args.legend;var self=this;if(typeof window.jQuery=="undefined"){throw"couldn't find jQuery at window.jQuery"}if(typeof window.jQuery.ui=="undefined"){throw"couldn't find jQuery UI at window.jQuery.ui"}jQuery(function(){jQuery(self.legend.list).sortable({containment:"parent",tolerance:"pointer",update:function(event,ui){var series=[];jQuery(self.legend.list).find("li").each(function(index,item){if(!item.series)return;series.push(item.series)});for(var i=self.graph.series.length-1;i>=0;i--){self.graph.series[i]=series.shift()}self.graph.update()}});jQuery(self.legend.list).disableSelection()});this.graph.onUpdate(function(){var h=window.getComputedStyle(self.legend.element).height;self.legend.element.style.height=h})};Rickshaw.namespace("Rickshaw.Graph.Behavior.Series.Toggle");Rickshaw.Graph.Behavior.Series.Toggle=function(args){this.graph=args.graph;this.legend=args.legend;var self=this;this.addAnchor=function(line){var anchor=document.createElement("a");anchor.innerHTML="&#10004;";anchor.classList.add("action");line.element.insertBefore(anchor,line.element.firstChild);anchor.onclick=function(e){if(line.series.disabled){line.series.enable();line.element.classList.remove("disabled")}else{if(this.graph.series.filter(function(s){return!s.disabled}).length<=1)return;line.series.disable();line.element.classList.add("disabled")}}.bind(this);var label=line.element.getElementsByTagName("span")[0];label.onclick=function(e){var disableAllOtherLines=line.series.disabled;if(!disableAllOtherLines){for(var i=0;i<self.legend.lines.length;i++){var l=self.legend.lines[i];if(line.series===l.series){}else if(l.series.disabled){}else{disableAllOtherLines=true;break}}}if(disableAllOtherLines){line.series.enable();line.element.classList.remove("disabled");self.legend.lines.forEach(function(l){if(line.series===l.series){}else{l.series.disable();l.element.classList.add("disabled")}})}else{self.legend.lines.forEach(function(l){l.series.enable();l.element.classList.remove("disabled")})}}};if(this.legend){var $=jQuery;if(typeof $!="undefined"&&$(this.legend.list).sortable){$(this.legend.list).sortable({start:function(event,ui){ui.item.bind("no.onclick",function(event){event.preventDefault()})},stop:function(event,ui){setTimeout(function(){ui.item.unbind("no.onclick")},250)}})}this.legend.lines.forEach(function(l){self.addAnchor(l)})}this._addBehavior=function(){this.graph.series.forEach(function(s){s.disable=function(){if(self.graph.series.length<=1){throw"only one series left"}s.disabled=true;self.graph.update()};s.enable=function(){s.disabled=false;self.graph.update()}})};this._addBehavior();this.updateBehaviour=function(){this._addBehavior()}};Rickshaw.namespace("Rickshaw.Graph.HoverDetail");Rickshaw.Graph.HoverDetail=Rickshaw.Class.create({initialize:function(args){var graph=this.graph=args.graph;this.xFormatter=args.xFormatter||function(x){return new Date(x*1e3).toUTCString()};this.yFormatter=args.yFormatter||function(y){return y===null?y:y.toFixed(2)};var element=this.element=document.createElement("div");element.className="detail";this.visible=true;graph.element.appendChild(element);this.lastEvent=null;this._addListeners();this.onShow=args.onShow;this.onHide=args.onHide;this.onRender=args.onRender;this.formatter=args.formatter||this.formatter},formatter:function(series,x,y,formattedX,formattedY,d){return series.name+":&nbsp;"+formattedY},update:function(e){e=e||this.lastEvent;if(!e)return;this.lastEvent=e;if(!e.target.nodeName.match(/^(path|svg|rect|circle)$/))return;var graph=this.graph;var eventX=e.offsetX||e.layerX;var eventY=e.offsetY||e.layerY;var j=0;var points=[];var nearestPoint;this.graph.series.active().forEach(function(series){var data=this.graph.stackedData[j++];if(!data.length)return;var domainX=graph.x.invert(eventX);var domainIndexScale=d3.scale.linear().domain([data[0].x,data.slice(-1)[0].x]).range([0,data.length-1]);var approximateIndex=Math.round(domainIndexScale(domainX));if(approximateIndex==data.length-1)approximateIndex--;var dataIndex=Math.min(approximateIndex||0,data.length-1);for(var i=approximateIndex;i<data.length-1;){if(!data[i]||!data[i+1])break;if(data[i].x<=domainX&&data[i+1].x>domainX){dataIndex=Math.abs(domainX-data[i].x)<Math.abs(domainX-data[i+1].x)?i:i+1;break}if(data[i+1].x<=domainX){i++}else{i--}}if(dataIndex<0)dataIndex=0;var value=data[dataIndex];var distance=Math.sqrt(Math.pow(Math.abs(graph.x(value.x)-eventX),2)+Math.pow(Math.abs(graph.y(value.y+value.y0)-eventY),2));var xFormatter=series.xFormatter||this.xFormatter;var yFormatter=series.yFormatter||this.yFormatter;var point={formattedXValue:xFormatter(value.x),formattedYValue:yFormatter(series.scale?series.scale.invert(value.y):value.y),series:series,value:value,distance:distance,order:j,name:series.name};if(!nearestPoint||distance<nearestPoint.distance){nearestPoint=point}points.push(point)},this);if(!nearestPoint)return;nearestPoint.active=true;var domainX=nearestPoint.value.x;var formattedXValue=nearestPoint.formattedXValue;this.element.innerHTML="";this.element.style.left=graph.x(domainX)+"px";this.visible&&this.render({points:points,detail:points,mouseX:eventX,mouseY:eventY,formattedXValue:formattedXValue,domainX:domainX})},hide:function(){this.visible=false;this.element.classList.add("inactive");if(typeof this.onHide=="function"){this.onHide()}},show:function(){this.visible=true;this.element.classList.remove("inactive");if(typeof this.onShow=="function"){this.onShow()}},render:function(args){var graph=this.graph;var points=args.points;var point=points.filter(function(p){return p.active}).shift();if(point.value.y===null)return;var formattedXValue=point.formattedXValue;var formattedYValue=point.formattedYValue;this.element.innerHTML="";this.element.style.left=graph.x(point.value.x)+"px";var xLabel=document.createElement("div");xLabel.className="x_label";xLabel.innerHTML=formattedXValue;this.element.appendChild(xLabel);var item=document.createElement("div");item.className="item";var series=point.series;var actualY=series.scale?series.scale.invert(point.value.y):point.value.y;item.innerHTML=this.formatter(series,point.value.x,actualY,formattedXValue,formattedYValue,point);item.style.top=this.graph.y(point.value.y0+point.value.y)+"px";this.element.appendChild(item);var dot=document.createElement("div");dot.className="dot";dot.style.top=item.style.top;dot.style.borderColor=series.color;this.element.appendChild(dot);if(point.active){item.classList.add("active");dot.classList.add("active")}var alignables=[xLabel,item];alignables.forEach(function(el){el.classList.add("left")});this.show();var leftAlignError=this._calcLayoutError(alignables);if(leftAlignError>0){alignables.forEach(function(el){el.classList.remove("left");el.classList.add("right")});var rightAlignError=this._calcLayoutError(alignables);if(rightAlignError>leftAlignError){alignables.forEach(function(el){el.classList.remove("right");el.classList.add("left")})}}if(typeof this.onRender=="function"){this.onRender(args)}},_calcLayoutError:function(alignables){var parentRect=this.element.parentNode.getBoundingClientRect();var error=0;var alignRight=alignables.forEach(function(el){var rect=el.getBoundingClientRect();if(!rect.width){return}if(rect.right>parentRect.right){error+=rect.right-parentRect.right}if(rect.left<parentRect.left){error+=parentRect.left-rect.left}});return error},_addListeners:function(){this.graph.element.addEventListener("mousemove",function(e){this.visible=true;this.update(e)}.bind(this),false);this.graph.onUpdate(function(){this.update()}.bind(this));this.graph.element.addEventListener("mouseout",function(e){if(e.relatedTarget&&!(e.relatedTarget.compareDocumentPosition(this.graph.element)&Node.DOCUMENT_POSITION_CONTAINS)){this.hide()}}.bind(this),false)}});Rickshaw.namespace("Rickshaw.Graph.JSONP");Rickshaw.Graph.JSONP=Rickshaw.Class.create(Rickshaw.Graph.Ajax,{request:function(){jQuery.ajax({url:this.dataURL,dataType:"jsonp",success:this.success.bind(this),error:this.error.bind(this)})}});Rickshaw.namespace("Rickshaw.Graph.Legend");Rickshaw.Graph.Legend=Rickshaw.Class.create({className:"rickshaw_legend",initialize:function(args){this.element=args.element;this.graph=args.graph;this.naturalOrder=args.naturalOrder;this.element.classList.add(this.className);this.list=document.createElement("ul");this.element.appendChild(this.list);this.render();this.graph.onUpdate(function(){})},render:function(){var self=this;while(this.list.firstChild){this.list.removeChild(this.list.firstChild)}this.lines=[];var series=this.graph.series.map(function(s){return s});if(!this.naturalOrder){series=series.reverse()}series.forEach(function(s){self.addLine(s)})},addLine:function(series){var line=document.createElement("li");line.className="line";if(series.disabled){line.className+=" disabled"}if(series.className){d3.select(line).classed(series.className,true)}var swatch=document.createElement("div");swatch.className="swatch";swatch.style.backgroundColor=series.color;line.appendChild(swatch);var label=document.createElement("span");label.className="label";label.innerHTML=series.name;line.appendChild(label);this.list.appendChild(line);line.series=series;if(series.noLegend){line.style.display="none"}var _line={element:line,series:series};if(this.shelving){this.shelving.addAnchor(_line);this.shelving.updateBehaviour()}if(this.highlighter){this.highlighter.addHighlightEvents(_line)}this.lines.push(_line);return line}});Rickshaw.namespace("Rickshaw.Graph.RangeSlider");Rickshaw.Graph.RangeSlider=Rickshaw.Class.create({initialize:function(args){var element=this.element=args.element;var graph=this.graph=args.graph;this.slideCallbacks=[];this.build();graph.onUpdate(function(){this.update()}.bind(this))},build:function(){var element=this.element;var graph=this.graph;var $=jQuery;var domain=graph.dataDomain();var self=this;$(function(){$(element).slider({range:true,min:domain[0],max:domain[1],values:[domain[0],domain[1]],slide:function(event,ui){if(ui.values[1]<=ui.values[0])return;graph.window.xMin=ui.values[0];graph.window.xMax=ui.values[1];graph.update();var domain=graph.dataDomain();if(domain[0]==ui.values[0]){graph.window.xMin=undefined}if(domain[1]==ui.values[1]){graph.window.xMax=undefined}self.slideCallbacks.forEach(function(callback){callback(graph,graph.window.xMin,graph.window.xMax)})}})});$(element)[0].style.width=graph.width+"px"},update:function(){var element=this.element;var graph=this.graph;var $=jQuery;var values=$(element).slider("option","values");var domain=graph.dataDomain();$(element).slider("option","min",domain[0]);$(element).slider("option","max",domain[1]);if(graph.window.xMin==null){values[0]=domain[0]}if(graph.window.xMax==null){values[1]=domain[1]}$(element).slider("option","values",values)},onSlide:function(callback){this.slideCallbacks.push(callback)}});Rickshaw.namespace("Rickshaw.Graph.RangeSlider.Preview");Rickshaw.Graph.RangeSlider.Preview=Rickshaw.Class.create({initialize:function(args){if(!args.element)throw"Rickshaw.Graph.RangeSlider.Preview needs a reference to an element";if(!args.graph&&!args.graphs)throw"Rickshaw.Graph.RangeSlider.Preview needs a reference to an graph or an array of graphs";this.element=args.element;this.element.style.position="relative";this.graphs=args.graph?[args.graph]:args.graphs;this.defaults={height:75,width:400,gripperColor:undefined,frameTopThickness:3,frameHandleThickness:10,frameColor:"#d4d4d4",frameOpacity:1,minimumFrameWidth:0,heightRatio:.2};this.heightRatio=args.heightRatio||this.defaults.heightRatio;this.defaults.gripperColor=d3.rgb(this.defaults.frameColor).darker().toString();this.configureCallbacks=[];this.slideCallbacks=[];this.previews=[];if(!args.width)this.widthFromGraph=true;if(!args.height)this.heightFromGraph=true;if(this.widthFromGraph||this.heightFromGraph){this.graphs[0].onConfigure(function(){this.configure(args);this.render()}.bind(this))}args.width=args.width||this.graphs[0].width||this.defaults.width;args.height=args.height||this.graphs[0].height*this.heightRatio||this.defaults.height;this.configure(args);this.render()},onSlide:function(callback){this.slideCallbacks.push(callback)},onConfigure:function(callback){this.configureCallbacks.push(callback)},configure:function(args){this.config=this.config||{};this.configureCallbacks.forEach(function(callback){callback(args)});Rickshaw.keys(this.defaults).forEach(function(k){this.config[k]=k in args?args[k]:k in this.config?this.config[k]:this.defaults[k]},this);if("width"in args||"height"in args){if(this.widthFromGraph){this.config.width=this.graphs[0].width}if(this.heightFromGraph){this.config.height=this.graphs[0].height*this.heightRatio;this.previewHeight=this.config.height}this.previews.forEach(function(preview){var height=this.previewHeight/this.graphs.length-this.config.frameTopThickness*2;var width=this.config.width-this.config.frameHandleThickness*2;preview.setSize({width:width,height:height});if(this.svg){var svgHeight=height+this.config.frameHandleThickness*2;var svgWidth=width+this.config.frameHandleThickness*2;this.svg.style("width",svgWidth+"px");this.svg.style("height",svgHeight+"px")}},this)}},render:function(){var self=this;this.svg=d3.select(this.element).selectAll("svg.rickshaw_range_slider_preview").data([null]);this.previewHeight=this.config.height-this.config.frameTopThickness*2;this.previewWidth=this.config.width-this.config.frameHandleThickness*2;this.currentFrame=[0,this.previewWidth];var buildGraph=function(parent,index){var graphArgs=Rickshaw.extend({},parent.config);var height=self.previewHeight/self.graphs.length;var renderer=parent.renderer.name;Rickshaw.extend(graphArgs,{element:this.appendChild(document.createElement("div")),height:height,width:self.previewWidth,series:parent.series,renderer:renderer});var graph=new Rickshaw.Graph(graphArgs);self.previews.push(graph);parent.onUpdate(function(){graph.render();self.render()});parent.onConfigure(function(args){delete args.height;args.width=args.width-self.config.frameHandleThickness*2;graph.configure(args);graph.render()});graph.render()};var graphContainer=d3.select(this.element).selectAll("div.rickshaw_range_slider_preview_container").data(this.graphs);var translateCommand="translate("+this.config.frameHandleThickness+"px, "+this.config.frameTopThickness+"px)";graphContainer.enter().append("div").classed("rickshaw_range_slider_preview_container",true).style("-webkit-transform",translateCommand).style("-moz-transform",translateCommand).style("-ms-transform",translateCommand).style("transform",translateCommand).each(buildGraph);graphContainer.exit().remove();var masterGraph=this.graphs[0];var domainScale=d3.scale.linear().domain([0,this.previewWidth]).range(masterGraph.dataDomain());var currentWindow=[masterGraph.window.xMin,masterGraph.window.xMax];this.currentFrame[0]=currentWindow[0]===undefined?0:Math.round(domainScale.invert(currentWindow[0]));if(this.currentFrame[0]<0)this.currentFrame[0]=0;this.currentFrame[1]=currentWindow[1]===undefined?this.previewWidth:domainScale.invert(currentWindow[1]);if(this.currentFrame[1]-this.currentFrame[0]<self.config.minimumFrameWidth){this.currentFrame[1]=(this.currentFrame[0]||0)+self.config.minimumFrameWidth}this.svg.enter().append("svg").classed("rickshaw_range_slider_preview",true).style("height",this.config.height+"px").style("width",this.config.width+"px").style("position","absolute").style("top",0);this._renderDimming();this._renderFrame();this._renderGrippers();this._renderHandles();this._renderMiddle();this._registerMouseEvents()},_renderDimming:function(){var element=this.svg.selectAll("path.dimming").data([null]);element.enter().append("path").attr("fill","white").attr("fill-opacity","0.7").attr("fill-rule","evenodd").classed("dimming",true);var path="";path+=" M "+this.config.frameHandleThickness+" "+this.config.frameTopThickness;path+=" h "+this.previewWidth;path+=" v "+this.previewHeight;path+=" h "+-this.previewWidth;path+=" z ";path+=" M "+Math.max(this.currentFrame[0],this.config.frameHandleThickness)+" "+this.config.frameTopThickness;path+=" H "+Math.min(this.currentFrame[1]+this.config.frameHandleThickness*2,this.previewWidth+this.config.frameHandleThickness);path+=" v "+this.previewHeight;path+=" H "+Math.max(this.currentFrame[0],this.config.frameHandleThickness);path+=" z";element.attr("d",path)},_renderFrame:function(){var element=this.svg.selectAll("path.frame").data([null]);element.enter().append("path").attr("stroke","white").attr("stroke-width","1px").attr("stroke-linejoin","round").attr("fill",this.config.frameColor).attr("fill-opacity",this.config.frameOpacity).attr("fill-rule","evenodd").classed("frame",true);var path="";path+=" M "+this.currentFrame[0]+" 0";path+=" H "+(this.currentFrame[1]+this.config.frameHandleThickness*2);path+=" V "+this.config.height;path+=" H "+this.currentFrame[0];path+=" z";path+=" M "+(this.currentFrame[0]+this.config.frameHandleThickness)+" "+this.config.frameTopThickness;path+=" H "+(this.currentFrame[1]+this.config.frameHandleThickness);path+=" v "+this.previewHeight;path+=" H "+(this.currentFrame[0]+this.config.frameHandleThickness);path+=" z";element.attr("d",path)},_renderGrippers:function(){var gripper=this.svg.selectAll("path.gripper").data([null]);gripper.enter().append("path").attr("stroke",this.config.gripperColor).classed("gripper",true);var path="";[.4,.6].forEach(function(spacing){path+=" M "+Math.round(this.currentFrame[0]+this.config.frameHandleThickness*spacing)+" "+Math.round(this.config.height*.3);path+=" V "+Math.round(this.config.height*.7);path+=" M "+Math.round(this.currentFrame[1]+this.config.frameHandleThickness*(1+spacing))+" "+Math.round(this.config.height*.3);path+=" V "+Math.round(this.config.height*.7)}.bind(this));gripper.attr("d",path)},_renderHandles:function(){var leftHandle=this.svg.selectAll("rect.left_handle").data([null]);leftHandle.enter().append("rect").attr("width",this.config.frameHandleThickness).style("cursor","ew-resize").style("fill-opacity","0").classed("left_handle",true);leftHandle.attr("x",this.currentFrame[0]).attr("height",this.config.height);var rightHandle=this.svg.selectAll("rect.right_handle").data([null]);rightHandle.enter().append("rect").attr("width",this.config.frameHandleThickness).style("cursor","ew-resize").style("fill-opacity","0").classed("right_handle",true);rightHandle.attr("x",this.currentFrame[1]+this.config.frameHandleThickness).attr("height",this.config.height)},_renderMiddle:function(){var middleHandle=this.svg.selectAll("rect.middle_handle").data([null]);middleHandle.enter().append("rect").style("cursor","move").style("fill-opacity","0").classed("middle_handle",true);middleHandle.attr("width",Math.max(0,this.currentFrame[1]-this.currentFrame[0])).attr("x",this.currentFrame[0]+this.config.frameHandleThickness).attr("height",this.config.height)},_registerMouseEvents:function(){var element=d3.select(this.element);var drag={target:null,start:null,stop:null,left:false,right:false,rigid:false};var self=this;function onMousemove(datum,index){drag.stop=self._getClientXFromEvent(d3.event,drag);var distanceTraveled=drag.stop-drag.start;var frameAfterDrag=self.frameBeforeDrag.slice(0);var minimumFrameWidth=self.config.minimumFrameWidth;if(drag.rigid){minimumFrameWidth=self.frameBeforeDrag[1]-self.frameBeforeDrag[0]}if(drag.left){frameAfterDrag[0]=Math.max(frameAfterDrag[0]+distanceTraveled,0)}if(drag.right){frameAfterDrag[1]=Math.min(frameAfterDrag[1]+distanceTraveled,self.previewWidth)}var currentFrameWidth=frameAfterDrag[1]-frameAfterDrag[0];if(currentFrameWidth<=minimumFrameWidth){if(drag.left){frameAfterDrag[0]=frameAfterDrag[1]-minimumFrameWidth}if(drag.right){frameAfterDrag[1]=frameAfterDrag[0]+minimumFrameWidth}if(frameAfterDrag[0]<=0){frameAfterDrag[1]-=frameAfterDrag[0];frameAfterDrag[0]=0}if(frameAfterDrag[1]>=self.previewWidth){frameAfterDrag[0]-=frameAfterDrag[1]-self.previewWidth;frameAfterDrag[1]=self.previewWidth}}self.graphs.forEach(function(graph){var domainScale=d3.scale.linear().interpolate(d3.interpolateNumber).domain([0,self.previewWidth]).range(graph.dataDomain());var windowAfterDrag=[domainScale(frameAfterDrag[0]),domainScale(frameAfterDrag[1])];self.slideCallbacks.forEach(function(callback){callback(graph,windowAfterDrag[0],windowAfterDrag[1])});if(frameAfterDrag[0]===0){windowAfterDrag[0]=undefined}if(frameAfterDrag[1]===self.previewWidth){windowAfterDrag[1]=undefined}graph.window.xMin=windowAfterDrag[0];graph.window.xMax=windowAfterDrag[1];graph.update()})}function onMousedown(){drag.target=d3.event.target;drag.start=self._getClientXFromEvent(d3.event,drag);self.frameBeforeDrag=self.currentFrame.slice();d3.event.preventDefault?d3.event.preventDefault():d3.event.returnValue=false;d3.select(document).on("mousemove.rickshaw_range_slider_preview",onMousemove);d3.select(document).on("mouseup.rickshaw_range_slider_preview",onMouseup);d3.select(document).on("touchmove.rickshaw_range_slider_preview",onMousemove);d3.select(document).on("touchend.rickshaw_range_slider_preview",onMouseup);d3.select(document).on("touchcancel.rickshaw_range_slider_preview",onMouseup)}function onMousedownLeftHandle(datum,index){drag.left=true;onMousedown()}function onMousedownRightHandle(datum,index){drag.right=true;onMousedown()}function onMousedownMiddleHandle(datum,index){drag.left=true;drag.right=true;drag.rigid=true;onMousedown()}function onMouseup(datum,index){d3.select(document).on("mousemove.rickshaw_range_slider_preview",null);d3.select(document).on("mouseup.rickshaw_range_slider_preview",null);d3.select(document).on("touchmove.rickshaw_range_slider_preview",null);d3.select(document).on("touchend.rickshaw_range_slider_preview",null);d3.select(document).on("touchcancel.rickshaw_range_slider_preview",null);delete self.frameBeforeDrag;drag.left=false;drag.right=false;drag.rigid=false}element.select("rect.left_handle").on("mousedown",onMousedownLeftHandle);element.select("rect.right_handle").on("mousedown",onMousedownRightHandle);element.select("rect.middle_handle").on("mousedown",onMousedownMiddleHandle);element.select("rect.left_handle").on("touchstart",onMousedownLeftHandle);element.select("rect.right_handle").on("touchstart",onMousedownRightHandle);element.select("rect.middle_handle").on("touchstart",onMousedownMiddleHandle)},_getClientXFromEvent:function(event,drag){switch(event.type){case"touchstart":case"touchmove":var touchList=event.changedTouches;var touch=null;for(var touchIndex=0;touchIndex<touchList.length;touchIndex++){if(touchList[touchIndex].target===drag.target){touch=touchList[touchIndex];break}}return touch!==null?touch.clientX:undefined;default:return event.clientX}}});Rickshaw.namespace("Rickshaw.Graph.Renderer");Rickshaw.Graph.Renderer=Rickshaw.Class.create({initialize:function(args){this.graph=args.graph;this.tension=args.tension||this.tension;this.configure(args)},seriesPathFactory:function(){},seriesStrokeFactory:function(){},defaults:function(){return{tension:.8,strokeWidth:2,unstack:true,padding:{top:.01,right:0,bottom:.01,left:0},stroke:false,fill:false}},domain:function(data){var stackedData=data||this.graph.stackedData||this.graph.stackData();var xMin=+Infinity;var xMax=-Infinity;var yMin=+Infinity;var yMax=-Infinity;stackedData.forEach(function(series){series.forEach(function(d){if(d.y==null)return;var y=d.y+d.y0;if(y<yMin)yMin=y;if(y>yMax)yMax=y});if(!series.length)return;if(series[0].x<xMin)xMin=series[0].x;if(series[series.length-1].x>xMax)xMax=series[series.length-1].x});xMin-=(xMax-xMin)*this.padding.left;xMax+=(xMax-xMin)*this.padding.right;yMin=this.graph.min==="auto"?yMin:this.graph.min||0;yMax=this.graph.max===undefined?yMax:this.graph.max;if(this.graph.min==="auto"||yMin<0){yMin-=(yMax-yMin)*this.padding.bottom}if(this.graph.max===undefined){yMax+=(yMax-yMin)*this.padding.top}return{x:[xMin,xMax],y:[yMin,yMax]}},render:function(args){args=args||{};var graph=this.graph;var series=args.series||graph.series;var vis=args.vis||graph.vis;vis.selectAll("*").remove();var data=series.filter(function(s){return!s.disabled}).map(function(s){return s.stack});var pathNodes=vis.selectAll("path.path").data(data).enter().append("svg:path").classed("path",true).attr("d",this.seriesPathFactory());if(this.stroke){var strokeNodes=vis.selectAll("path.stroke").data(data).enter().append("svg:path").classed("stroke",true).attr("d",this.seriesStrokeFactory())}var i=0;series.forEach(function(series){if(series.disabled)return;series.path=pathNodes[0][i];if(this.stroke)series.stroke=strokeNodes[0][i];this._styleSeries(series);i++},this)},_styleSeries:function(series){var fill=this.fill?series.color:"none";var stroke=this.stroke?series.color:"none";series.path.setAttribute("fill",fill);series.path.setAttribute("stroke",stroke);series.path.setAttribute("stroke-width",this.strokeWidth);if(series.className){d3.select(series.path).classed(series.className,true)}if(series.className&&this.stroke){d3.select(series.stroke).classed(series.className,true)}},configure:function(args){args=args||{};Rickshaw.keys(this.defaults()).forEach(function(key){if(!args.hasOwnProperty(key)){this[key]=this[key]||this.graph[key]||this.defaults()[key];return}if(typeof this.defaults()[key]=="object"){Rickshaw.keys(this.defaults()[key]).forEach(function(k){this[key][k]=args[key][k]!==undefined?args[key][k]:this[key][k]!==undefined?this[key][k]:this.defaults()[key][k]},this)}else{this[key]=args[key]!==undefined?args[key]:this[key]!==undefined?this[key]:this.graph[key]!==undefined?this.graph[key]:this.defaults()[key]}},this)},setStrokeWidth:function(strokeWidth){if(strokeWidth!==undefined){this.strokeWidth=strokeWidth}},setTension:function(tension){if(tension!==undefined){this.tension=tension}}});Rickshaw.namespace("Rickshaw.Graph.Renderer.Line");Rickshaw.Graph.Renderer.Line=Rickshaw.Class.create(Rickshaw.Graph.Renderer,{name:"line",defaults:function($super){return Rickshaw.extend($super(),{unstack:true,fill:false,stroke:true})},seriesPathFactory:function(){var graph=this.graph;var factory=d3.svg.line().x(function(d){return graph.x(d.x)}).y(function(d){return graph.y(d.y)}).interpolate(this.graph.interpolation).tension(this.tension);factory.defined&&factory.defined(function(d){return d.y!==null});return factory}});Rickshaw.namespace("Rickshaw.Graph.Renderer.Stack");Rickshaw.Graph.Renderer.Stack=Rickshaw.Class.create(Rickshaw.Graph.Renderer,{name:"stack",defaults:function($super){return Rickshaw.extend($super(),{fill:true,stroke:false,unstack:false})},seriesPathFactory:function(){var graph=this.graph;var factory=d3.svg.area().x(function(d){return graph.x(d.x)}).y0(function(d){return graph.y(d.y0)}).y1(function(d){return graph.y(d.y+d.y0)}).interpolate(this.graph.interpolation).tension(this.tension);factory.defined&&factory.defined(function(d){return d.y!==null});return factory}});Rickshaw.namespace("Rickshaw.Graph.Renderer.Bar");Rickshaw.Graph.Renderer.Bar=Rickshaw.Class.create(Rickshaw.Graph.Renderer,{name:"bar",defaults:function($super){var defaults=Rickshaw.extend($super(),{gapSize:.05,unstack:false});delete defaults.tension;return defaults},initialize:function($super,args){args=args||{};this.gapSize=args.gapSize||this.gapSize;$super(args)},domain:function($super){var domain=$super();var frequentInterval=this._frequentInterval(this.graph.stackedData.slice(-1).shift());domain.x[1]+=Number(frequentInterval.magnitude);return domain},barWidth:function(series){var frequentInterval=this._frequentInterval(series.stack);var barWidth=this.graph.x.magnitude(frequentInterval.magnitude)*(1-this.gapSize);return barWidth},render:function(args){args=args||{};var graph=this.graph;var series=args.series||graph.series;var vis=args.vis||graph.vis;vis.selectAll("*").remove();var barWidth=this.barWidth(series.active()[0]);var barXOffset=0;var activeSeriesCount=series.filter(function(s){return!s.disabled}).length;var seriesBarWidth=this.unstack?barWidth/activeSeriesCount:barWidth;var transform=function(d){var matrix=[1,0,0,d.y<0?-1:1,0,d.y<0?graph.y.magnitude(Math.abs(d.y))*2:0];return"matrix("+matrix.join(",")+")"};series.forEach(function(series){if(series.disabled)return;var barWidth=this.barWidth(series);var nodes=vis.selectAll("path").data(series.stack.filter(function(d){return d.y!==null})).enter().append("svg:rect").attr("x",function(d){return graph.x(d.x)+barXOffset}).attr("y",function(d){return graph.y(d.y0+Math.abs(d.y))*(d.y<0?-1:1)}).attr("width",seriesBarWidth).attr("height",function(d){return graph.y.magnitude(Math.abs(d.y))}).attr("transform",transform);Array.prototype.forEach.call(nodes[0],function(n){n.setAttribute("fill",series.color)});if(this.unstack)barXOffset+=seriesBarWidth},this)},_frequentInterval:function(data){var intervalCounts={};for(var i=0;i<data.length-1;i++){var interval=data[i+1].x-data[i].x;intervalCounts[interval]=intervalCounts[interval]||0;intervalCounts[interval]++}var frequentInterval={count:0,magnitude:1};Rickshaw.keys(intervalCounts).forEach(function(i){if(frequentInterval.count<intervalCounts[i]){frequentInterval={count:intervalCounts[i],magnitude:i}}});return frequentInterval}});Rickshaw.namespace("Rickshaw.Graph.Renderer.Area");Rickshaw.Graph.Renderer.Area=Rickshaw.Class.create(Rickshaw.Graph.Renderer,{name:"area",defaults:function($super){return Rickshaw.extend($super(),{unstack:false,fill:false,stroke:false})},seriesPathFactory:function(){var graph=this.graph;var factory=d3.svg.area().x(function(d){return graph.x(d.x)}).y0(function(d){return graph.y(d.y0)}).y1(function(d){return graph.y(d.y+d.y0)}).interpolate(graph.interpolation).tension(this.tension);
    factory.defined&&factory.defined(function(d){return d.y!==null});return factory},seriesStrokeFactory:function(){var graph=this.graph;var factory=d3.svg.line().x(function(d){return graph.x(d.x)}).y(function(d){return graph.y(d.y+d.y0)}).interpolate(graph.interpolation).tension(this.tension);factory.defined&&factory.defined(function(d){return d.y!==null});return factory},render:function(args){args=args||{};var graph=this.graph;var series=args.series||graph.series;var vis=args.vis||graph.vis;vis.selectAll("*").remove();var method=this.unstack?"append":"insert";var data=series.filter(function(s){return!s.disabled}).map(function(s){return s.stack});var nodes=vis.selectAll("path").data(data).enter()[method]("svg:g","g");nodes.append("svg:path").attr("d",this.seriesPathFactory()).attr("class","area");if(this.stroke){nodes.append("svg:path").attr("d",this.seriesStrokeFactory()).attr("class","line")}var i=0;series.forEach(function(series){if(series.disabled)return;series.path=nodes[0][i++];this._styleSeries(series)},this)},_styleSeries:function(series){if(!series.path)return;d3.select(series.path).select(".area").attr("fill",series.color);if(this.stroke){d3.select(series.path).select(".line").attr("fill","none").attr("stroke",series.stroke||d3.interpolateRgb(series.color,"black")(.125)).attr("stroke-width",this.strokeWidth)}if(series.className){series.path.setAttribute("class",series.className)}}});Rickshaw.namespace("Rickshaw.Graph.Renderer.ScatterPlot");Rickshaw.Graph.Renderer.ScatterPlot=Rickshaw.Class.create(Rickshaw.Graph.Renderer,{name:"scatterplot",defaults:function($super){return Rickshaw.extend($super(),{unstack:true,fill:true,stroke:false,padding:{top:.01,right:.01,bottom:.01,left:.01},dotSize:4})},initialize:function($super,args){$super(args)},render:function(args){args=args||{};var graph=this.graph;var series=args.series||graph.series;var vis=args.vis||graph.vis;var dotSize=this.dotSize;vis.selectAll("*").remove();series.forEach(function(series){if(series.disabled)return;var nodes=vis.selectAll("path").data(series.stack.filter(function(d){return d.y!==null})).enter().append("svg:circle").attr("cx",function(d){return graph.x(d.x)}).attr("cy",function(d){return graph.y(d.y)}).attr("r",function(d){return"r"in d?d.r:dotSize});if(series.className){nodes.classed(series.className,true)}Array.prototype.forEach.call(nodes[0],function(n){n.setAttribute("fill",series.color)})},this)}});Rickshaw.namespace("Rickshaw.Graph.Renderer.Multi");Rickshaw.Graph.Renderer.Multi=Rickshaw.Class.create(Rickshaw.Graph.Renderer,{name:"multi",initialize:function($super,args){$super(args)},defaults:function($super){return Rickshaw.extend($super(),{unstack:true,fill:false,stroke:true})},configure:function($super,args){args=args||{};this.config=args;$super(args)},domain:function($super){this.graph.stackData();var domains=[];var groups=this._groups();this._stack(groups);groups.forEach(function(group){var data=group.series.filter(function(s){return!s.disabled}).map(function(s){return s.stack});if(!data.length)return;var domain=null;if(group.renderer&&group.renderer.domain){domain=group.renderer.domain(data)}else{domain=$super(data)}domains.push(domain)});var xMin=d3.min(domains.map(function(d){return d.x[0]}));var xMax=d3.max(domains.map(function(d){return d.x[1]}));var yMin=d3.min(domains.map(function(d){return d.y[0]}));var yMax=d3.max(domains.map(function(d){return d.y[1]}));return{x:[xMin,xMax],y:[yMin,yMax]}},_groups:function(){var graph=this.graph;var renderGroups={};graph.series.forEach(function(series){if(series.disabled)return;if(!renderGroups[series.renderer]){var ns="http://www.w3.org/2000/svg";var vis=document.createElementNS(ns,"g");graph.vis[0][0].appendChild(vis);var renderer=graph._renderers[series.renderer];var config={};var defaults=[this.defaults(),renderer.defaults(),this.config,this.graph];defaults.forEach(function(d){Rickshaw.extend(config,d)});renderer.configure(config);renderGroups[series.renderer]={renderer:renderer,series:[],vis:d3.select(vis)}}renderGroups[series.renderer].series.push(series)},this);var groups=[];Object.keys(renderGroups).forEach(function(key){var group=renderGroups[key];groups.push(group)});return groups},_stack:function(groups){groups.forEach(function(group){var series=group.series.filter(function(series){return!series.disabled});var data=series.map(function(series){return series.stack});if(!group.renderer.unstack){var layout=d3.layout.stack();var stackedData=Rickshaw.clone(layout(data));series.forEach(function(series,index){series._stack=Rickshaw.clone(stackedData[index])})}},this);return groups},render:function(){this.graph.series.forEach(function(series){if(!series.renderer){throw new Error("Each series needs a renderer for graph 'multi' renderer")}});this.graph.vis.selectAll("*").remove();var groups=this._groups();groups=this._stack(groups);groups.forEach(function(group){var series=group.series.filter(function(series){return!series.disabled});series.active=function(){return series};group.renderer.render({series:series,vis:group.vis});series.forEach(function(s){s.stack=s._stack||s.stack||s.data})})}});Rickshaw.namespace("Rickshaw.Graph.Renderer.LinePlot");Rickshaw.Graph.Renderer.LinePlot=Rickshaw.Class.create(Rickshaw.Graph.Renderer,{name:"lineplot",defaults:function($super){return Rickshaw.extend($super(),{unstack:true,fill:false,stroke:true,padding:{top:.01,right:.01,bottom:.01,left:.01},dotSize:3,strokeWidth:2})},seriesPathFactory:function(){var graph=this.graph;var factory=d3.svg.line().x(function(d){return graph.x(d.x)}).y(function(d){return graph.y(d.y)}).interpolate(this.graph.interpolation).tension(this.tension);factory.defined&&factory.defined(function(d){return d.y!==null});return factory},render:function(args){args=args||{};var graph=this.graph;var series=args.series||graph.series;var vis=args.vis||graph.vis;var dotSize=this.dotSize;vis.selectAll("*").remove();var data=series.filter(function(s){return!s.disabled}).map(function(s){return s.stack});var nodes=vis.selectAll("path").data(data).enter().append("svg:path").attr("d",this.seriesPathFactory());var i=0;series.forEach(function(series){if(series.disabled)return;series.path=nodes[0][i++];this._styleSeries(series)},this);series.forEach(function(series){if(series.disabled)return;var nodes=vis.selectAll("x").data(series.stack.filter(function(d){return d.y!==null})).enter().append("svg:circle").attr("cx",function(d){return graph.x(d.x)}).attr("cy",function(d){return graph.y(d.y)}).attr("r",function(d){return"r"in d?d.r:dotSize});Array.prototype.forEach.call(nodes[0],function(n){if(!n)return;n.setAttribute("data-color",series.color);n.setAttribute("fill","white");n.setAttribute("stroke",series.color);n.setAttribute("stroke-width",this.strokeWidth)}.bind(this))},this)}});Rickshaw.namespace("Rickshaw.Graph.Smoother");Rickshaw.Graph.Smoother=Rickshaw.Class.create({initialize:function(args){this.graph=args.graph;this.element=args.element;this.aggregationScale=1;this.build();this.graph.stackData.hooks.data.push({name:"smoother",orderPosition:50,f:this.transformer.bind(this)})},build:function(){var self=this;var $=jQuery;if(this.element){$(function(){$(self.element).slider({min:1,max:100,slide:function(event,ui){self.setScale(ui.value);self.graph.update()}})})}},setScale:function(scale){if(scale<1){throw"scale out of range: "+scale}this.aggregationScale=scale;this.graph.update()},transformer:function(data){if(this.aggregationScale==1)return data;var aggregatedData=[];data.forEach(function(seriesData){var aggregatedSeriesData=[];while(seriesData.length){var avgX=0,avgY=0;var slice=seriesData.splice(0,this.aggregationScale);slice.forEach(function(d){avgX+=d.x/slice.length;avgY+=d.y/slice.length});aggregatedSeriesData.push({x:avgX,y:avgY})}aggregatedData.push(aggregatedSeriesData)}.bind(this));return aggregatedData}});Rickshaw.namespace("Rickshaw.Graph.Socketio");Rickshaw.Graph.Socketio=Rickshaw.Class.create(Rickshaw.Graph.Ajax,{request:function(){var socket=io.connect(this.dataURL);var self=this;socket.on("rickshaw",function(data){self.success(data)})}});Rickshaw.namespace("Rickshaw.Series");Rickshaw.Series=Rickshaw.Class.create(Array,{initialize:function(data,palette,options){options=options||{};this.palette=new Rickshaw.Color.Palette(palette);this.timeBase=typeof options.timeBase==="undefined"?Math.floor((new Date).getTime()/1e3):options.timeBase;var timeInterval=typeof options.timeInterval=="undefined"?1e3:options.timeInterval;this.setTimeInterval(timeInterval);if(data&&typeof data=="object"&&Array.isArray(data)){data.forEach(function(item){this.addItem(item)},this)}},addItem:function(item){if(typeof item.name==="undefined"){throw"addItem() needs a name"}item.color=item.color||this.palette.color(item.name);item.data=item.data||[];if(item.data.length===0&&this.length&&this.getIndex()>0){this[0].data.forEach(function(plot){item.data.push({x:plot.x,y:0})})}else if(item.data.length===0){item.data.push({x:this.timeBase-(this.timeInterval||0),y:0})}this.push(item);if(this.legend){this.legend.addLine(this.itemByName(item.name))}},addData:function(data,x){var index=this.getIndex();Rickshaw.keys(data).forEach(function(name){if(!this.itemByName(name)){this.addItem({name:name})}},this);this.forEach(function(item){item.data.push({x:x||(index*this.timeInterval||1)+this.timeBase,y:data[item.name]||0})},this)},getIndex:function(){return this[0]&&this[0].data&&this[0].data.length?this[0].data.length:0},itemByName:function(name){for(var i=0;i<this.length;i++){if(this[i].name==name)return this[i]}},setTimeInterval:function(iv){this.timeInterval=iv/1e3},setTimeBase:function(t){this.timeBase=t},dump:function(){var data={timeBase:this.timeBase,timeInterval:this.timeInterval,items:[]};this.forEach(function(item){var newItem={color:item.color,name:item.name,data:[]};item.data.forEach(function(plot){newItem.data.push({x:plot.x,y:plot.y})});data.items.push(newItem)});return data},load:function(data){if(data.timeInterval){this.timeInterval=data.timeInterval}if(data.timeBase){this.timeBase=data.timeBase}if(data.items){data.items.forEach(function(item){this.push(item);if(this.legend){this.legend.addLine(this.itemByName(item.name))}},this)}}});Rickshaw.Series.zeroFill=function(series){Rickshaw.Series.fill(series,0)};Rickshaw.Series.fill=function(series,fill){var x;var i=0;var data=series.map(function(s){return s.data});while(i<Math.max.apply(null,data.map(function(d){return d.length}))){x=Math.min.apply(null,data.filter(function(d){return d[i]}).map(function(d){return d[i].x}));data.forEach(function(d){if(!d[i]||d[i].x!=x){d.splice(i,0,{x:x,y:fill})}});i++}};Rickshaw.namespace("Rickshaw.Series.FixedDuration");Rickshaw.Series.FixedDuration=Rickshaw.Class.create(Rickshaw.Series,{initialize:function(data,palette,options){options=options||{};if(typeof options.timeInterval==="undefined"){throw new Error("FixedDuration series requires timeInterval")}if(typeof options.maxDataPoints==="undefined"){throw new Error("FixedDuration series requires maxDataPoints")}this.palette=new Rickshaw.Color.Palette(palette);this.timeBase=typeof options.timeBase==="undefined"?Math.floor((new Date).getTime()/1e3):options.timeBase;this.setTimeInterval(options.timeInterval);if(this[0]&&this[0].data&&this[0].data.length){this.currentSize=this[0].data.length;this.currentIndex=this[0].data.length}else{this.currentSize=0;this.currentIndex=0}this.maxDataPoints=options.maxDataPoints;if(data&&typeof data=="object"&&Array.isArray(data)){data.forEach(function(item){this.addItem(item)},this);this.currentSize+=1;this.currentIndex+=1}this.timeBase-=(this.maxDataPoints-this.currentSize)*this.timeInterval;if(typeof this.maxDataPoints!=="undefined"&&this.currentSize<this.maxDataPoints){for(var i=this.maxDataPoints-this.currentSize-1;i>1;i--){this.currentSize+=1;this.currentIndex+=1;this.forEach(function(item){item.data.unshift({x:((i-1)*this.timeInterval||1)+this.timeBase,y:0,i:i})},this)}}},addData:function($super,data,x){$super(data,x);this.currentSize+=1;this.currentIndex+=1;if(this.maxDataPoints!==undefined){while(this.currentSize>this.maxDataPoints){this.dropData()}}},dropData:function(){this.forEach(function(item){item.data.splice(0,1)});this.currentSize-=1},getIndex:function(){return this.currentIndex}});return Rickshaw});

!function(l){l.fn.metrojs={capabilities:null,checkCapabilities:function(e,t){return(null==l.fn.metrojs.capabilities||"undefined"!=typeof t&&1==t)&&(l.fn.metrojs.capabilities=new l.fn.metrojs.MetroModernizr(e)),l.fn.metrojs.capabilities}};var e=l.fn.metrojs,t=99e3;l.fn.liveTile=function(e){if(n[e]){for(var t=[],i=1;i<=arguments.length;i++)t[i-1]=arguments[i];return n[e].apply(this,t)}return"object"!=typeof e&&e?(l.error("Method "+e+" does not exist on jQuery.liveTile"),null):n.init.apply(this,arguments)},l.fn.liveTile.contentModules={modules:[],addContentModule:function(l,e){this.modules instanceof Array||(this.modules=[]),this.modules.push(e)},hasContentModule:function(l){if("undefined"==typeof l||!(this.modules instanceof Array))return-1;for(var e=0;e<this.modules.length;e++)if("undefined"!=typeof this.modules[e].moduleName&&this.modules[e].moduleName==l)return e;return-1}},l.fn.liveTile.defaults={mode:"slide",speed:500,initDelay:-1,delay:5e3,stops:"100%",stack:!1,direction:"vertical",animationDirection:"forward",tileSelector:">div,>li,>p,>img,>a",tileFaceSelector:">div,>li,>p,>img,>a",ignoreDataAttributes:!1,click:null,link:"",newWindow:!1,bounce:!1,bounceDirections:"all",bounceFollowsMove:!0,pauseOnHover:!1,pauseOnHoverEvent:"both",playOnHover:!1,playOnHoverEvent:"both",onHoverDelay:0,repeatCount:-1,appendBack:!0,alwaysTrigger:!1,flipListOnHover:!1,flipListOnHoverEvent:"mouseout",noHAflipOpacity:"1",haTransFunc:"ease",noHaTransFunc:"linear",currentIndex:0,startNow:!0,useModernizr:"undefined"!=typeof window.Modernizr,useHardwareAccel:!0,useTranslate:!0,faces:{$front:null,$back:null},animationStarting:function(){},animationComplete:function(){},triggerDelay:function(){return 3e3*Math.random()},swap:"",swapFront:"-",swapBack:"-",contentModules:[]};var n={init:function(t){var n=l.extend({},l.fn.liveTile.defaults,t);return e.checkCapabilities(n),o.getBrowserPrefix(),-1==l.fn.liveTile.contentModules.hasContentModule("image")&&l.fn.liveTile.contentModules.addContentModule("image",a.imageSwap),-1==l.fn.liveTile.contentModules.hasContentModule("html")&&l.fn.liveTile.contentModules.addContentModule("html",a.htmlSwap),l(this).each(function(e,t){var o=l(t),a=i.initTileData(o,n);a.faces=i.prepTile(o,a),a.fade=function(l){i.fade(o,l)},a.slide=function(l){i.slide(o,l)},a.carousel=function(l){i.carousel(o,l)},a.flip=function(l){i.flip(o,l)},a.flipList=function(l){i.flipList(o,l)};var r={fade:a.fade,slide:a.slide,carousel:a.carousel,flip:a.flip,"flip-list":a.flipList};a.doAction=function(l){var e=r[a.mode];"function"==typeof e&&(e(l),a.hasRun=!0),l==a.timer.repeatCount&&(a.runEvents=!1)},a.timer=new l.fn.metrojs.TileTimer(a.delay,a.doAction,a.repeatCount),o.data("LiveTile",a),("flip-list"!==a.mode||0==a.flipListOnHover)&&(a.pauseOnHover?i.bindPauseOnHover(o):a.playOnHover&&i.bindPlayOnHover(o,a)),(a.link.length>0||"function"==typeof a.click)&&o.css({cursor:"pointer"}).bind("click.liveTile",function(l){var e=!0;"function"==typeof a.click&&(e=a.click(o,a)||!1),e&&a.link.length>0&&(l.preventDefault(),a.newWindow?window.open(a.link):window.location=a.link)}),i.bindBounce(o,a),a.startNow&&"none"!=a.mode&&(a.runEvents=!0,a.timer.start(a.initDelay))})},"goto":function(e){var t,n=typeof e;if("undefined"===n&&(t={index:-99,delay:0,autoAniDirection:!1}),"number"!==n&&isNaN(e)){if("string"===n)if("next"==e)t={index:-99,delay:0};else{if(0!==e.indexOf("prev"))return l.error(e+' is not a recognized action for .liveTile("goto")'),l(this);t={index:-100,delay:0}}else if("object"===n){"undefined"==typeof e.delay&&(e.delay=0);var i=typeof e.index;"undefined"===i?e.index=0:"string"===i&&("next"===e.index?e.index=-99:0===e.index.indexOf("prev")&&(e.index=-100)),t=e}}else t={index:parseInt(e,10),delay:0};return l(this).each(function(e,n){var i=l(n),o=i.data("LiveTile"),a=i.data("metrojs.tile"),r=t.index;if(a.animating===!0)return l(this);if("carousel"===o.mode){var s=o.faces.$listTiles.filter(".active"),c=o.faces.$listTiles.index(s);if(-100===r?(("undefined"==typeof t.autoAniDirection||1==t.autoAniDirection)&&(o.tempValues.animationDirection="undefined"==typeof t.animationDirection?"backward":t.animationDirection),r=0===c?o.faces.$listTiles.length-1:c-1):-99===r&&(("undefined"==typeof t.autoAniDirection||1==t.autoAniDirection)&&(o.tempValues.animationDirection="undefined"==typeof t.animationDirection?"forward":t.animationDirection),r=c+1),c==r)return;"undefined"!=typeof t.direction&&(o.tempValues.direction=t.direction),"undefined"!=typeof t.animationDirection&&(o.tempValues.animationDirection=t.animationDirection),o.currentIndex=0==r?o.faces.$listTiles.length:r-1}else o.currentIndex=r;o.runEvents=!0,o.timer.start(t.delay>=0?t.delay:o.delay)})},play:function(e){var t,n=typeof e;return"undefined"===n?t={delay:-1}:"number"===n?t={delay:e}:"object"===n&&("undefined"==typeof e.delay&&(e.delay=-1),t=e),l(this).each(function(e,n){var i=l(n),o=i.data("LiveTile");o.runEvents=!0,t.delay<0&&!o.hasRun&&(t.delay=o.initDelay),o.timer.start(t.delay>=0?t.delay:o.delay)})},animate:function(){return l(this).each(function(e,t){var n=l(t),i=n.data("LiveTile");i.doAction()})},stop:function(){return l(this).each(function(e,t){var n=l(t),i=n.data("LiveTile");i.hasRun=!1,i.runEvents=!1,i.timer.stop(),window.clearTimeout(i.eventTimeout),window.clearTimeout(i.flCompleteTimeout),window.clearTimeout(i.completeTimeout),"flip-list"===i.mode&&i.faces.$listTiles.each(function(e,t){var n=l(t).data("metrojs.tile");window.clearTimeout(n.eventTimeout),window.clearTimeout(n.flCompleteTimeout),window.clearTimeout(n.completeTimeout)})})},pause:function(){return l(this).each(function(e,t){var n=l(t),i=n.data("LiveTile");i.timer.pause(),i.runEvents=!1,window.clearTimeout(i.eventTimeout),window.clearTimeout(i.flCompleteTimeout),window.clearTimeout(i.completeTimeout),"flip-list"===i.mode&&i.faces.$listTiles.each(function(e,t){var n=l(t).data("metrojs.tile");window.clearTimeout(n.eventTimeout),window.clearTimeout(n.flCompleteTimeout),window.clearTimeout(n.completeTimeout)})})},restart:function(e){var t,n=typeof e;return"undefined"===n?t={delay:-1}:"number"===n?t={delay:e}:"object"===n&&("undefined"==typeof e.delay&&(e.delay=-1),t=e),l(this).each(function(e,n){var i=l(n),o=i.data("LiveTile");t.delay<0&&!o.hasRun&&(t.delay=o.initDelay),o.hasRun=!1,o.runEvents=!0,o.timer.restart(t.delay>=0?t.delay:o.delay)})},rebind:function(e){return l(this).each(function(l,t){"undefined"!=typeof e?("undefined"!=typeof e.timer&&null!=e.timer&&e.timer.stop(),e.hasRun=!1,n.init.apply(t,e)):n.init.apply(t,{})})},destroy:function(e){var t,n=typeof e;return"undefined"===n?t={removeCss:!1}:"boolean"===n?t={removeCss:e}:"object"===n&&("undefined"==typeof e.removeCss&&(e.removeCss=!1),t=e),l(this).each(function(e,n){var a=l(n),r=a.data("LiveTile");if("undefined"!=typeof r){a.unbind(".liveTile");var s=o.appendStyleProperties({margin:"",cursor:""},["transform","transition"],["",""]);r.timer.stop(),window.clearTimeout(r.eventTimeout),window.clearTimeout(r.flCompleteTimeout),window.clearTimeout(r.completeTimeout),null!=r.faces.$listTiles&&r.faces.$listTiles.each(function(e,n){var o=l(n);if("flip-list"===r.mode){var a=o.data("metrojs.tile");window.clearTimeout(a.eventTimeout),window.clearTimeout(a.flCompleteTimeout),window.clearTimeout(a.completeTimeout)}else if("carousel"===r.mode){var c=r.listData[e];c.bounce&&i.unbindMsBounce(o,c)}t.removeCss?(o.removeClass("ha"),o.find(r.tileFaceSelector).unbind(".liveTile").removeClass("bounce flip-front flip-back ha slide slide-front slide-back").css(s)):o.find(r.tileFaceSelector).unbind(".liveTile"),o.removeData("metrojs.tile")}).unbind(".liveTile"),null!=r.faces.$front&&t.removeCss&&r.faces.$front.removeClass("flip-front flip-back ha slide slide-front slide-back").css(s),null!=r.faces.$back&&t.removeCss&&r.faces.$back.removeClass("flip-front flip-back ha slide slide-front slide-back").css(s),r.bounce&&i.unbindMsBounce(a,r),r.playOnHover&&i.unbindMsPlayOnHover(a,r),r.pauseOnhover&&i.unbindMsPauseOnHover(a,r),a.removeClass("ha"),a.removeData("LiveTile"),a.removeData("metrojs.tile"),r=null}})}},i={dataAtr:function(l,e,t){return"undefined"!=typeof l.attr("data-"+e)?l.attr("data-"+e):t},dataMethod:function(l,e,t){return"undefined"!=typeof l.data(e)?l.data(e):t},getDataOrDefault:null,initTileData:function(t,n){var o=0==n.ignoreDataAttributes,a=null;null==this.getDataOrDefault&&(this.getDataOrDefault=e.capabilities.isOldJQuery?this.dataAtr:this.dataMethod),a=o?{speed:this.getDataOrDefault(t,"speed",n.speed),delay:this.getDataOrDefault(t,"delay",n.delay),stops:this.getDataOrDefault(t,"stops",n.stops),stack:this.getDataOrDefault(t,"stack",n.stack),mode:this.getDataOrDefault(t,"mode",n.mode),direction:this.getDataOrDefault(t,"direction",n.direction),useHardwareAccel:this.getDataOrDefault(t,"ha",n.useHardwareAccel),repeatCount:this.getDataOrDefault(t,"repeat",n.repeatCount),swap:this.getDataOrDefault(t,"swap",n.swap),appendBack:this.getDataOrDefault(t,"appendback",n.appendBack),currentIndex:this.getDataOrDefault(t,"start-index",n.currentIndex),animationDirection:this.getDataOrDefault(t,"ani-direction",n.animationDirection),startNow:this.getDataOrDefault(t,"start-now",n.startNow),tileSelector:this.getDataOrDefault(t,"tile-selector",n.tileSelector),tileFaceSelector:this.getDataOrDefault(t,"face-selector",n.tileFaceSelector),bounce:this.getDataOrDefault(t,"bounce",n.bounce),bounceDirections:this.getDataOrDefault(t,"bounce-dir",n.bounceDirections),bounceFollowsMove:this.getDataOrDefault(t,"bounce-follows",n.bounceFollowsMove),click:this.getDataOrDefault(t,"click",n.click),link:this.getDataOrDefault(t,"link",n.link),newWindow:this.getDataOrDefault(t,"new-window",n.newWindow),alwaysTrigger:this.getDataOrDefault(t,"always-trigger",n.alwaysTrigger),flipListOnHover:this.getDataOrDefault(t,"flip-onhover",n.flipListOnHover),pauseOnHover:this.getDataOrDefault(t,"pause-onhover",n.pauseOnHover),playOnHover:this.getDataOrDefault(t,"play-onhover",n.playOnHover),onHoverDelay:this.getDataOrDefault(t,"hover-delay",n.onHoverDelay),noHAflipOpacity:this.getDataOrDefault(t,"flip-opacity",n.noHAflipOpacity),useTranslate:this.getDataOrDefault(t,"use-translate",n.useTranslate),runEvents:!1,isReversed:!1,loopCount:0,contentModules:[],listData:[],height:t.height(),width:t.width(),tempValues:{}}:l.extend(!0,{runEvents:!1,isReversed:!1,loopCount:0,contentModules:[],listData:[],height:t.height(),width:t.width(),tempValues:{}},n),a.useTranslate=a.useTranslate&&a.useHardwareAccel&&e.capabilities.canTransform&&e.capabilities.canTransition,a.margin="vertical"===a.direction?a.height/2:a.width/2,a.stops="object"==typeof n.stops&&n.stops instanceof Array?n.stops:(""+a.stops).split(","),1===a.stops.length&&a.stops.push("0px");var r=a.swap.replace(" ","").split(","),s=o?this.getDataOrDefault(t,"swap-front",n.swapFront):n.swapFront,c=o?this.getDataOrDefault(t,"swap-back",n.swapBack):n.swapBack;a.swapFront="-"===s?r:s.replace(" ","").split(","),a.swapBack="-"===c?r:c.replace(" ","").split(",");var d;for(d=0;d<a.swapFront.length;d++)a.swapFront[d].length>0&&-1===l.inArray(a.swapFront[d],r)&&r.push(a.swapFront[d]);for(d=0;d<a.swapBack.length;d++)a.swapBack[d].length>0&&-1===l.inArray(a.swapBack[d],r)&&r.push(a.swapBack[d]);for(a.swap=r,d=0;d<r.length;d++)if(r[d].length>0){var u=l.fn.liveTile.contentModules.hasContentModule(r[d]);u>-1&&a.contentModules.push(l.fn.liveTile.contentModules.modules[u])}a.initDelay=o?this.getDataOrDefault(t,"initdelay",n.initDelay):n.initDelay,a.delay<-1?a.delay=n.triggerDelay(1):a.delay<0&&(a.delay=3500+4501*Math.random()),a.initDelay<0&&(a.initDelay=a.delay);var h={};for(d=0;d<a.contentModules.length;d++)l.extend(h,a.contentModules[d].data);l.extend(h,n,a);var f;for("flip-list"===h.mode?(f=t.find(h.tileSelector).not(".tile-title"),f.each(function(e,t){var n=l(t),a={direction:o?i.getDataOrDefault(n,"direction",h.direction):h.direction,newWindow:o?i.getDataOrDefault(n,"new-window",!1):!1,link:o?i.getDataOrDefault(n,"link",""):"",faces:{$front:null,$back:null},height:n.height(),width:n.width(),isReversed:!1};a.margin="vertical"===a.direction?a.height/2:a.width/2,h.listData.push(a)})):"carousel"===h.mode&&(h.stack=!0,f=t.find(h.tileSelector).not(".tile-title"),f.each(function(e,t){var n=l(t),a={bounce:o?i.getDataOrDefault(n,"bounce",!1):!1,bounceDirections:o?i.getDataOrDefault(n,"bounce-dir","all"):"all",link:o?i.getDataOrDefault(n,"link",""):"",newWindow:o?i.getDataOrDefault(n,"new-window",!1):!1,animationDirection:o?i.getDataOrDefault(n,"ani-direction",""):"",direction:o?i.getDataOrDefault(n,"direction",""):""};h.listData.push(a)})),d=0;d<a.contentModules.length;d++)"function"==typeof h.contentModules[d].initData&&h.contentModules[d].initData(h,t);return a=null,h},prepTile:function(n,a){n.addClass(a.mode);var r,s,c,d,u={$tileFaces:null,$listTiles:null,$front:null,$back:null};switch(a.mode){case"fade":u.$tileFaces=n.find(a.tileFaceSelector).not(".tile-title"),u.$front=null!=a.faces.$front&&a.faces.$front.length>0?a.faces.$front.addClass("fade-front"):u.$tileFaces.filter(":first").addClass("fade-front"),u.$back=null!=a.faces.$back&&a.faces.$back.length>0?a.faces.$back.addClass("fade-back"):u.$tileFaces.length>1?u.$tileFaces.filter(":last").addClass("fade-back"):a.appendBack?l('<div class="fade-back"></div>').appendTo(n):l("<div></div>");break;case"slide":if(u.$tileFaces=n.find(a.tileFaceSelector).not(".tile-title"),u.$front=null!=a.faces.$front&&a.faces.$front.length>0?a.faces.$front.addClass("slide-front"):u.$tileFaces.filter(":first").addClass("slide-front"),u.$back=null!=a.faces.$back&&a.faces.$back.length>0?a.faces.$back.addClass("slide-back"):u.$tileFaces.length>1?u.$tileFaces.filter(":last").addClass("slide-back"):a.appendBack?l('<div class="slide-back"></div>').appendTo(n):l("<div></div>"),1==a.stack){var h,f;"vertical"===a.direction?(h="top",f="translate(0%, -100%) translateZ(0)"):(h="left",f="translate(-100%, 0%) translateZ(0)"),c={},a.useTranslate?o.appendStyleProperties(c,["transform"],[f]):c[h]="-100%",u.$back.css(c)}n.data("metrojs.tile",{animating:!1}),e.capabilities.canTransition&&a.useHardwareAccel&&(n.addClass("ha"),u.$front.addClass("ha"),u.$back.addClass("ha"));break;case"carousel":u.$listTiles=n.find(a.tileSelector).not(".tile-title");var p=u.$listTiles.length;n.data("metrojs.tile",{animating:!1}),a.currentIndex=Math.min(a.currentIndex,p-1),u.$listTiles.each(function(t,n){var r=l(n).addClass("slide"),s=a.listData[t],c="string"==typeof s.animationDirection&&s.animationDirection.length>0?s.animationDirection:a.animationDirection,u="string"==typeof s.direction&&s.direction.length>0?s.direction:a.direction;t==a.currentIndex?r.addClass("active"):"forward"===c?"vertical"===u?(d=a.useTranslate?o.appendStyleProperties({},["transform"],["translate(0%, 100%) translateZ(0)"]):{left:"0%",top:"100%"},r.css(d)):(d=a.useTranslate?o.appendStyleProperties({},["transform"],["translate(100%, 0%) translateZ(0)"]):{left:"100%",top:"0%"},r.css(d)):"backward"===c&&("vertical"===u?(d=a.useTranslate?o.appendStyleProperties({},["transform"],["translate(0%, -100%) translateZ(0)"]):{left:"0%",top:"-100%"},r.css(d)):(d=a.useTranslate?o.appendStyleProperties({},["transform"],["translate(-100%, 0%) translateZ(0)"]):{left:"-100%",top:"0%"},r.css(d))),i.bindLink(r,s),a.useHardwareAccel&&e.capabilities.canTransition&&i.bindBounce(r,s),r=null,s=null}),e.capabilities.canFlip3d&&a.useHardwareAccel&&(n.addClass("ha"),u.$listTiles.addClass("ha"));break;case"flip-list":u.$listTiles=n.find(a.tileSelector).not(".tile-title"),u.$listTiles.each(function(n,d){var u=l(d).addClass("tile-"+(n+1)),h=u.find(a.tileFaceSelector).filter(":first").addClass("flip-front").css({margin:"0px"});1===u.find(a.tileFaceSelector).length&&1==a.appendBack&&u.append("<div></div>");var f=u.find(a.tileFaceSelector).filter(":last").addClass("flip-back").css({margin:"0px"});a.listData[n].faces.$front=h,a.listData[n].faces.$back=f,u.data("metrojs.tile",{animating:!1,count:1,completeTimeout:null,flCompleteTimeout:null,index:n});var p=u.data("metrojs.tile");e.capabilities.canFlip3d&&a.useHardwareAccel?(u.addClass("ha"),h.addClass("ha"),f.addClass("ha"),r="vertical"===a.listData[n].direction?"rotateX(180deg)":"rotateY(180deg)",c=o.appendStyleProperties({},["transform"],[r]),f.css(c)):(s="vertical"===a.listData[n].direction?{height:"100%",width:"100%",marginTop:"0px",opacity:"1"}:{height:"100%",width:"100%",marginLeft:"0px",opacity:"1"},c="vertical"===a.listData[n].direction?{height:"0px",width:"100%",marginTop:a.listData[n].margin+"px",opacity:a.noHAflipOpacity}:{height:"100%",width:"0px",marginLeft:a.listData[n].margin+"px",opacity:a.noHAflipOpacity},h.css(s),f.css(c));var m=function(){p.count++,p.count>=t&&(p.count=1)};if(a.flipListOnHover){var g=a.flipListOnHoverEvent+".liveTile";h.bind(g,function(){i.flip(u,p.count,a,m)}),f.bind(g,function(){i.flip(u,p.count,a,m)})}a.listData[n].link.length>0&&u.css({cursor:"pointer"}).bind("click.liveTile",function(){a.listData[n].newWindow?window.open(a.listData[n].link):window.location=a.listData[n].link})});break;case"flip":u.$tileFaces=n.find(a.tileFaceSelector).not(".tile-title"),u.$front=null!=a.faces.$front&&a.faces.$front.length>0?a.faces.$front.addClass("flip-front"):u.$tileFaces.filter(":first").addClass("flip-front"),u.$back=null!=a.faces.$back&&a.faces.$back.length>0?a.faces.$back.addClass("flip-back"):u.$tileFaces.length>1?u.$tileFaces.filter(":last").addClass("flip-back"):a.appendBack?l('<div class="flip-back"></div>').appendTo(n):l("<div></div>"),n.data("metrojs.tile",{animating:!1}),e.capabilities.canFlip3d&&a.useHardwareAccel?(n.addClass("ha"),u.$front.addClass("ha"),u.$back.addClass("ha"),r="vertical"===a.direction?"rotateX(180deg)":"rotateY(180deg)",c=o.appendStyleProperties({},["transform"],[r]),u.$back.css(c)):(s="vertical"===a.direction?{height:"100%",width:"100%",marginTop:"0px",opacity:"1"}:{height:"100%",width:"100%",marginLeft:"0px",opacity:"1"},c="vertical"===a.direction?{height:"0%",width:"100%",marginTop:a.margin+"px",opacity:"0"}:{height:"100%",width:"0%",marginLeft:a.margin+"px",opacity:"0"},u.$front.css(s),u.$back.css(c))}return u},bindPauseOnHover:function(t){!function(){var n=t.data("LiveTile"),i=!1,o=!1,a="both"==n.pauseOnHoverEvent||"mouseover"==n.pauseOnHoverEvent||"mouseenter"==n.pauseOnHoverEvent,r="both"==n.pauseOnHoverEvent||"mouseout"==n.pauseOnHoverEvent||"mouseleave"==n.pauseOnHoverEvent;n.pOnHoverMethods={pause:function(){n.timer.pause(),"flip-list"===n.mode&&n.faces.$listTiles.each(function(e,t){window.clearTimeout(l(t).data("metrojs.tile").completeTimeout)})},over:function(){i||o||n.runEvents&&(o=!0,n.eventTimeout=window.setTimeout(function(){o=!1,r&&(i=!0),n.pOnHoverMethods.pause()},n.onHoverDelay))},out:function(){if(o)return window.clearTimeout(n.eventTimeout),o=!1,void 0;if(a){if(!i&&!o)return;n.runEvents&&n.timer.start(n.hasRun?n.delay:n.initDelay)}else n.pOnHoverMethods.pause();i=!1}},e.capabilities.canTouch?window.navigator.msPointerEnabled?(a&&t[0].addEventListener("MSPointerOver",n.pOnHoverMethods.over,!1),r&&t[0].addEventListener("MSPointerOut",n.pOnHoverMethods.out,!1)):(a&&t.bind("touchstart.liveTile",n.pOnHoverMethods.over),r&&t.bind("touchend.liveTile",n.pOnHoverMethods.out)):(a&&t.bind("mouseover.liveTile",n.pOnHoverMethods.over),r&&t.bind("mouseout.liveTile",n.pOnHoverMethods.out))}()},unbindMsPauseOnHover:function(l,e){"undefined"!=typeof e.pOnHoverMethods&&window.navigator.msPointerEnabled&&(l[0].removeEventListener("MSPointerOver",e.pOnHoverMethods.over,!1),l[0].removeEventListener("MSPointerOut",e.pOnHoverMethods.out,!1))},bindPlayOnHover:function(l,t){!function(){var i=!1,o=!1,a="both"==t.playOnHoverEvent||"mouseover"==t.playOnHoverEvent||"mouseenter"==t.playOnHoverEvent,r="both"==t.playOnHoverEvent||"mouseout"==t.playOnHoverEvent||"mouseleave"==t.playOnHoverEvent;t.onHoverMethods={over:function(){if(!(i||o||t.bounce&&"no"!=t.bounceMethods.down)){var e="flip"==t.mode||(t.startNow?!t.isReversed:t.isReversed);window.clearTimeout(t.eventTimeout),(t.runEvents&&e||!t.hasRun)&&(o=!0,t.eventTimeout=window.setTimeout(function(){o=!1,r&&(i=!0),n.play.apply(l[0],[0])},t.onHoverDelay))}},out:function(){return o?(window.clearTimeout(t.eventTimeout),o=!1,void 0):((!a||i||o)&&(window.clearTimeout(t.eventTimeout),t.eventTimeout=window.setTimeout(function(){var e="flip"==t.mode||(t.startNow?t.isReversed:!t.isReversed);t.runEvents&&e&&n.play.apply(l[0],[0]),i=!1},t.speed+200)),void 0)}},e.capabilities.canTouch?window.navigator.msPointerEnabled?(a&&l[0].addEventListener("MSPointerDown",t.onHoverMethods.over,!1),r&&l.bind("mouseleave.liveTile",t.onHoverMethods.out)):(a&&l.bind("touchstart.liveTile",t.onHoverMethods.over),r&&l.bind("touchend.liveTile",t.onHoverMethods.out)):(a&&l.bind("mouseenter.liveTile",t.onHoverMethods.over),r&&l.bind("mouseleave.liveTile",t.onHoverMethods.out))}()},unbindMsPlayOnHover:function(l,e){"undefined"!=typeof e.onHoverMethods&&window.navigator.msPointerEnabled&&l[0].removeEventListener("MSPointerDown",e.onHoverMethods.over,!1)},bindBounce:function(t,n){n.bounce&&(t.addClass("bounce"),t.addClass("noselect"),function(){n.bounceMethods={down:"no",threshold:30,zeroPos:{x:0,y:0},eventPos:{x:0,y:0},inTilePos:{x:0,y:0},pointPos:{x:0,y:0},regions:{c:[0,0],tl:[-1,-1],tr:[1,-1],bl:[-1,1],br:[1,1],t:[null,-1],r:[1,null],b:[null,1],l:[-1,null]},targets:{all:["c","t","r","b","l","tl","tr","bl","br"],edges:["c","t","r","b","l"],corners:["c","tl","tr","bl","br"]},hitTest:function(t,i,o,a){var r=n.bounceMethods.regions,s=n.bounceMethods.targets[o],c=0,d=null,u=null,h={hit:[0,0],name:"c"};if(e.capabilities.isOldAndroid||!e.capabilities.canTransition)return h;"undefined"==typeof s&&("string"==typeof o&&(s=o.split(",")),l.isArray(s)&&-1==l.inArray("c")&&(a=0,h=null));for(var f=t.width(),p=t.height(),m=[f*a,p*a],g=i.x-.5*f,v=i.y-.5*p,b=[g>0?Math.abs(g)<=m[0]?0:1:Math.abs(g)<=m[0]?0:-1,v>0?Math.abs(v)<=m[1]?0:1:Math.abs(v)<=m[1]?0:-1];c<s.length;c++){if(null!=d)return d;var T=s[c],y=r[T];if("*"==T)return T=s[c+1],{region:r[T],name:T};b[0]==y[0]&&b[1]==y[1]?d={hit:y,name:T}:b[0]!=y[0]&&null!=y[0]||b[1]!=y[1]&&null!=y[1]||(u={hit:y,name:T})}return null!=d?d:null!=u?u:h},bounceDown:function(e){if("A"!=e.target.tagName||l(e).is(".bounce")){var i=e.originalEvent&&e.originalEvent.touches?e.originalEvent.touches[0]:e,a=t.offset();window.pageXOffset,window.pageYOffset,n.bounceMethods.pointPos={x:i.pageX,y:i.pageY},n.bounceMethods.inTilePos={x:i.pageX-a.left,y:i.pageY-a.top},n.$tileParent||(n.$tileParent=t.parent());var r=n.$tileParent.offset();n.bounceMethods.eventPos={x:a.left-r.left+t.width()/2,y:a.top-r.top+t.height()/2};var s=n.bounceMethods.hitTest(t,n.bounceMethods.inTilePos,n.bounceDirections,.25);if(null==s)n.bounceMethods.down="no";else{window.navigator.msPointerEnabled?(document.addEventListener("MSPointerUp",n.bounceMethods.bounceUp,!1),t[0].addEventListener("MSPointerUp",n.bounceMethods.bounceUp,!1),document.addEventListener("MSPointerCancel",n.bounceMethods.bounceUp,!1),n.bounceFollowsMove&&t[0].addEventListener("MSPointerMove",n.bounceMethods.bounceMove,!1)):(l(document).bind("mouseup.liveTile, touchend.liveTile, touchcancel.liveTile, dragstart.liveTile",n.bounceMethods.bounceUp),n.bounceFollowsMove&&(t.bind("touchmove.liveTile",n.bounceMethods.bounceMove),t.bind("mousemove.liveTile",n.bounceMethods.bounceMove)));var c="bounce-"+s.name;t.addClass(c),n.bounceMethods.down=c,n.bounceMethods.downPcss=o.appendStyleProperties({},["perspective-origin"],[n.bounceMethods.eventPos.x+"px "+n.bounceMethods.eventPos.y+"px"]),n.$tileParent.css(n.bounceMethods.downPcss)}}},bounceUp:function(){"no"!=n.bounceMethods.down&&(n.bounceMethods.unBounce(),window.navigator.msPointerEnabled?(document.removeEventListener("MSPointerUp",n.bounceMethods.bounceUp,!1),t[0].removeEventListener("MSPointerUp",n.bounceMethods.bounceUp,!1),document.removeEventListener("MSPointerCancel",n.bounceMethods.bounceUp,!1),n.bounceFollowsMove&&t[0].removeEventListener("MSPointerMove",n.bounceMethods.bounceMove,!1)):l(document).unbind("mouseup.liveTile, touchend.liveTile, touchcancel.liveTile, dragstart.liveTile",n.bounceMethods.bounceUp),n.bounceFollowsMove&&(t.unbind("touchmove.liveTile",n.bounceMethods.bounceMove),t.unbind("mousemove.liveTile",n.bounceMethods.bounceMove)))},bounceMove:function(l){if("no"!=n.bounceMethods.down){var e=l.originalEvent&&l.originalEvent.touches?l.originalEvent.touches[0]:l,i=Math.abs(e.pageX-n.bounceMethods.pointPos.x),o=Math.abs(e.pageY-n.bounceMethods.pointPos.y);if(i>n.bounceMethods.threshold||o>n.bounceMethods.threshold){var a=n.bounceMethods.down;n.bounceMethods.bounceDown(l),a!=n.bounceMethods.down&&t.removeClass(a)}}},unBounce:function(){if(t.removeClass(n.bounceMethods.down),"object"==typeof n.bounceMethods.downPcss){var l=["perspective-origin","perspective-origin-x","perspective-origin-y"],e=["","",""];n.bounceMethods.downPcss=o.appendStyleProperties({},l,e),window.setTimeout(function(){n.$tileParent.css(n.bounceMethods.downPcss)},200)}n.bounceMethods.down="no",n.bounceMethods.inTilePos=n.bounceMethods.zeroPos,n.bounceMethods.eventPos=n.bounceMethods.zeroPos}},window.navigator.msPointerEnabled?t[0].addEventListener("MSPointerDown",n.bounceMethods.bounceDown,!1):e.capabilities.canTouch?t.bind("touchstart.liveTile",n.bounceMethods.bounceDown):t.bind("mousedown.liveTile",n.bounceMethods.bounceDown)}())},unbindMsBounce:function(l,e){e.bounce&&window.navigator.msPointerEnabled&&(l[0].removeEventListener("MSPointerDown",e.bounceMethods.bounceDown,!1),l[0].removeEventListener("MSPointerCancel",e.bounceMethods.bounceUp,!1),l[0].removeEventListener("MSPointerOut",e.bounceMethods.bounceUp,!1))},bindLink:function(e,t){t.link.length>0&&e.css({cursor:"pointer"}).bind("click.liveTile",function(e){("A"!=e.target.tagName||l(e).is(".live-tile,.slide,.flip"))&&(t.newWindow?window.open(t.link):window.location=t.link)})},runContenModules:function(l,e,t,n){for(var i=0;i<l.contentModules.length;i++){var o=l.contentModules[i];"function"==typeof o.action&&o.action(l,e,t,n)}},fade:function(l,e,t){var n="object"==typeof t?t:l.data("LiveTile"),o=function(){(n.timer.repeatCount>0||-1==n.timer.repeatCount)&&n.timer.count!=n.timer.repeatCount&&n.timer.start(n.delay)};if(!n.faces.$front.is(":animated")){n.timer.pause();var a=n.loopCount+1;n.isReversed=0===a%2;var r=n.animationStarting.call(l[0],n,n.faces.$front,n.faces.$back);if("undefined"!=typeof r&&0==r)return o(),void 0;n.loopCount=a;var s=function(){o(),i.runContenModules(n,n.faces.$front,n.faces.$back),n.animationComplete.call(l[0],n,n.faces.$front,n.faces.$back)};n.isReversed?n.faces.$front.fadeIn(n.speed,n.noHaTransFunc,s):n.faces.$front.fadeOut(n.speed,n.noHaTransFunc,s)}},slide:function(t,n,a,r,s){var c="object"==typeof a?a:t.data("LiveTile"),d=t.data("metrojs.tile");if(1==d.animating||t.is(":animated"))return c=null,d=null,void 0;var u=function(){(c.timer.repeatCount>0||-1==c.timer.repeatCount)&&c.timer.count!=c.timer.repeatCount&&c.timer.start(c.delay)};if("carousel"!==c.mode){c.isReversed=0!==c.currentIndex%2,c.timer.pause();var h=c.animationStarting.call(t[0],c,c.faces.$front,c.faces.$back);if("undefined"!=typeof h&&0==h)return u(),void 0;c.loopCount=c.loopCount+1}else c.isReversed=!0;var f;f="string"==typeof c.tempValues.direction&&c.tempValues.direction.length>0?c.tempValues.direction:c.direction,c.tempValues.direction=null;var p={},m={},g="undefined"==typeof r?c.currentIndex:r,v=l.trim(c.stops[Math.min(g,c.stops.length-1)]),b=v.indexOf("px"),T=0,y=0,C="vertical"===f?c.height:c.width,E="vertical"===f?"top":"left",D=1==c.stack,I=function(){"undefined"==typeof s?(c.currentIndex=c.currentIndex+1,c.currentIndex>c.stops.length-1&&(c.currentIndex=0)):s(),"carousel"!=c.mode&&u(),i.runContenModules(c,c.faces.$front,c.faces.$back,c.currentIndex),c.animationComplete.call(t[0],c,c.faces.$front,c.faces.$back),c=null,d=null};if(b>0?(y=parseInt(v.substring(0,b),10),T=y-C+"px"):(y=parseInt(v.replace("%",""),10),T=y-100+"%"),e.capabilities.canTransition&&c.useHardwareAccel){if("undefined"!=typeof d.animating&&1==d.animating)return;d.animating=!0;var w=["transition-property","transition-duration","transition-timing-function"],S=[c.useTranslate?"transform":E,c.speed+"ms",c.haTransFunc];S[o.browserPrefix+"transition-property"]=o.browserPrefix+"transform",p=o.appendStyleProperties(p,w,S),m=o.appendStyleProperties(m,w,S);var _,O="vertical"===f,k=O?"top":"left";c.useTranslate?(_=O?"translate(0%, "+v+")":"translate("+v+", 0%)",p=o.appendStyleProperties(p,["transform"],[_+"translateZ(0)"]),D&&(_=O?"translate(0%, "+T+")":"translate("+T+", 0%)",m=o.appendStyleProperties(m,["transform"],[_+"translateZ(0)"]))):(p[k]=v,D&&(m[k]=T)),c.faces.$front.css(p),D&&c.faces.$back.css(m),window.clearTimeout(c.completeTimeout),c.completeTimeout=window.setTimeout(function(){d.animating=!1,I()},c.speed)}else{p[E]=v,m[E]=T,d.animating=!0;var R=c.faces.$front.stop(),x=c.faces.$back.stop();R.animate(p,c.speed,c.noHaTransFunc,function(){d.animating=!1,I()}),D&&x.animate(m,c.speed,c.noHaTransFunc,function(){})}},carousel:function(l,t){var n=l.data("LiveTile"),a=l.data("metrojs.tile");if(1==a.animating||n.faces.$listTiles.length<=1)return a=null,void 0;var r=function(){(n.timer.repeatCount>0||-1==n.timer.repeatCount)&&n.timer.count!=n.timer.repeatCount&&n.timer.start(n.delay)};n.timer.pause();var s=n.faces.$listTiles.filter(".active"),c=n.faces.$listTiles.index(s),d=n.currentIndex,u=d!=c?d:c,h=u+1>=n.faces.$listTiles.length?0:u+1,f=n.listData[h];if(c==h)return a=null,s=null,void 0;var p;p="string"==typeof n.tempValues.animationDirection&&n.tempValues.animationDirection.length>0?n.tempValues.animationDirection:"string"==typeof f.animationDirection&&f.animationDirection.length>0?f.animationDirection:n.animationDirection,n.tempValues.animationDirection=null;var m;"string"==typeof n.tempValues.direction&&n.tempValues.direction.length>0?m=n.tempValues.direction:"string"==typeof f.direction&&f.direction.length>0?(m=f.direction,n.tempValues.direction=m):m=n.direction;var g=n.faces.$listTiles.eq(h),v=n.animationStarting.call(l[0],n,s,g);if("undefined"!=typeof v&&0==v)return r(),void 0;n.loopCount=n.loopCount+1;var b,T=o.appendStyleProperties({},["transition-duration"],["0s"]),y="vertical"===m;"backward"===p?(n.useTranslate&&e.capabilities.canTransition?(b=y?"translate(0%, -100%)":"translate(-100%, 0%)",T=o.appendStyleProperties(T,["transform"],[b+" translateZ(0)"]),n.stops=["100%"]):(y?(T.top="-100%",T.left="0%"):(T.top="0%",T.left="-100%"),n.stops=["100%"]),n.faces.$front=s,n.faces.$back=g):(n.useTranslate&&e.capabilities.canTransition?(b=y?"translate(0%, 100%)":"translate(100%, 0%)",T=o.appendStyleProperties(T,["transform"],[b+" translateZ(0)"])):y?(T.top="100%",T.left="0%"):(T.top="0%",T.left="100%"),n.faces.$front=g,n.faces.$back=s,n.stops=["0%"]),g.css(T),window.setTimeout(function(){s.removeClass("active"),g.addClass("active"),i.slide(l,t,n,0,function(){n.currentIndex=h,a=null,s=null,g=null,r()})},150)},flip:function(l,t,n,a){var r=l.data("metrojs.tile");if(1==r.animating)return r=null,void 0;var s,c,d,u,h,f,p,m="object"==typeof n?n:l.data("LiveTile"),g="undefined"==typeof a,v=0,b=function(){(m.timer.repeatCount>0||-1==m.timer.repeatCount)&&m.timer.count!=m.timer.repeatCount&&m.timer.start(m.delay)};if(g){m.timer.pause();var T=m.loopCount+1;p=0===T%2,m.isReversed=p,s=m.faces.$front,c=m.faces.$back;var y=p?[m,c,s]:[m,s,c],C=m.animationStarting.apply(l[0],y);if("undefined"!=typeof C&&0==C)return b(),void 0;d=m.direction,height=m.height,width=m.width,margin=m.margin,m.loopCount=T}else p=0===t%2,v=r.index,s=m.listData[v].faces.$front,c=m.listData[v].faces.$back,m.listData[v].isReversed=p,d=m.listData[v].direction,height=m.listData[v].height,width=m.listData[v].width,margin=m.listData[v].margin;if(e.capabilities.canFlip3d&&m.useHardwareAccel){u=p?"360deg":"180deg",h="vertical"===d?"rotateX("+u+")":"rotateY("+u+")",f=o.appendStyleProperties({},["transform","transition"],[h,"all "+m.speed+"ms "+m.haTransFunc+" 0s"]);var E=p?"540deg":"360deg",D="vertical"===d?"rotateX("+E+")":"rotateY("+E+")",I=o.appendStyleProperties({},["transform","transition"],[D,"all "+m.speed+"ms "+m.haTransFunc+" 0s"]);
    s.css(f),c.css(I);var w=function(){r.animating=!1;var e,t;p?(e="vertical"===d?"rotateX(0deg)":"rotateY(0deg)",t=o.appendStyleProperties({},["transform","transition"],[e,"all 0s "+m.haTransFunc+" 0s"]),s.css(t),i.runContenModules(m,s,c,v),g?(b(),m.animationComplete.call(l[0],m,s,c)):a(m,s,c),s=null,c=null,m=null,r=null):(i.runContenModules(m,c,s,v),g?(b(),m.animationComplete.call(l[0],m,c,s)):a(m,c,s))};"flip-list"===m.mode?(window.clearTimeout(m.listData[v].completeTimeout),m.listData[v].completeTimeout=window.setTimeout(w,m.speed)):(window.clearTimeout(m.completeTimeout),m.completeTimeout=window.setTimeout(w,m.speed))}else{var S,_=m.speed/2,O="vertical"===d?{height:"0px",width:"100%",marginTop:margin+"px",opacity:m.noHAflipOpacity}:{height:"100%",width:"0px",marginLeft:margin+"px",opacity:m.noHAflipOpacity},k="vertical"===d?{height:"100%",width:"100%",marginTop:"0px",opacity:"1"}:{height:"100%",width:"100%",marginLeft:"0px",opacity:"1"};p?(r.animating=!0,c.stop().animate(O,{duration:_}),S=function(){r.animating=!1,s.stop().animate(k,{duration:_,complete:function(){i.runContenModules(m,s,c,v),g?(b(),m.animationComplete.call(l[0],m,s,c)):a(m,s,c),r=null,s=null,c=null}})},"flip-list"===m.mode?(window.clearTimeout(m.listData[r.index].completeTimeout),m.listData[r.index].completeTimeout=window.setTimeout(S,_)):(window.clearTimeout(m.completeTimeout),m.completeTimeout=window.setTimeout(S,_))):(r.animating=!0,s.stop().animate(O,{duration:_}),S=function(){r.animating=!1,c.stop().animate(k,{duration:_,complete:function(){i.runContenModules(m,c,s,v),g?(b(),m.animationComplete.call(l[0],m,c,s)):a(m,c,s),s=null,c=null,m=null,r=null}})},"flip-list"===m.mode?(window.clearTimeout(m.listData[r.index].completeTimeout),m.listData[r.index].completeTimeout=window.setTimeout(S,_)):(window.clearTimeout(m.completeTimeout),m.completeTimeout=window.setTimeout(S,_)))}},flipList:function(e){var n=e.data("LiveTile"),o=n.speed,a=!1,r=function(){(n.timer.repeatCount>0||-1==n.timer.repeatCount)&&n.timer.count!=n.timer.repeatCount&&n.timer.start(n.delay)};n.timer.pause();var s=n.animationStarting.call(e[0],n,null,null);return"undefined"!=typeof s&&0==s?(r(),void 0):(n.loopCount=n.loopCount+1,n.faces.$listTiles.each(function(e,r){var s=l(r),c=s.data("metrojs.tile"),d=n.triggerDelay(e),u=n.speed+Math.max(d,0),h=n.alwaysTrigger;h||(h=351*Math.random()>150?!0:!1),h&&(a=!0,o=Math.max(u+n.speed,o),window.clearTimeout(c.flCompleteTimeout),c.flCompleteTimeout=window.setTimeout(function(){i.flip(s,c.count,n,function(){c.count++,c.count>=t&&(c.count=1),s=null,c=null})},u))}),a&&(window.clearTimeout(n.flCompleteTimeout),n.flCompleteTimeout=window.setTimeout(function(){i.runContenModules(n,null,null,-1),n.animationComplete.call(e[0],n,null,null),r()},o+n.speed)),void 0)}},o={stylePrefixes:"Webkit Moz O ms Khtml ".split(" "),domPrefixes:"-webkit- -moz- -o- -ms- -khtml- ".split(" "),browserPrefix:null,appendStyleProperties:function(e,t,n){for(var i=0;i<=t.length-1;i++)e[l.trim(this.browserPrefix+t[i])]=n[i],e[l.trim(t[i])]=n[i];return e},applyStyleValue:function(e,t,n){return e[l.trim(this.browserPrefix+t)]=n,e[t]=n,e},getBrowserPrefix:function(){if(null==this.browserPrefix){for(var l="",e=0;e<=this.domPrefixes.length-1;e++)"undefined"!=typeof document.body.style[this.domPrefixes[e]+"transform"]&&(l=this.domPrefixes[e]);return this.browserPrefix=l}return this.browserPrefix},shuffleArray:function(l){for(var e=[];l.length;)e.push(l.splice(Math.random()*l.length,1));for(;e.length;)l.push(e.pop());return l}},a={moduleName:"custom",customSwap:{data:{customDoSwapFront:function(){return!1},customDoSwapBack:function(){return!1},customGetContent:function(){return null}},initData:function(e){var t={};t.doSwapFront=l.inArray("custom",e.swapFront)>-1&&e.customDoSwapFront(),t.doSwapBack=l.inArray("custom",e.swapBack)>-1&&e.customDoSwapBack(),e.customSwap="undefined"!=typeof e.customSwap?l.extend(t,e.customSwap):t},action:function(){}},htmlSwap:{moduleName:"html",data:{frontContent:[],frontIsRandom:!0,frontIsInGrid:!1,backContent:[],backIsRandom:!0,backIsInGrid:!1},initData:function(e,t){var n={backBag:[],backIndex:0,backStaticIndex:0,backStaticRndm:-1,prevBackIndex:-1,frontBag:[],frontIndex:0,frontStaticIndex:0,frontStaticRndm:-1,prevFrontIndex:-1};e.ignoreDataAttributes?(n.frontIsRandom=e.frontIsRandom,n.frontIsInGrid=e.frontIsInGrid,n.backIsRandom=e.backIsRandom,n.backIsInGrid=e.backIsInGrid):(n.frontIsRandom=i.getDataOrDefault(t,"front-israndom",e.frontIsRandom),n.frontIsInGrid=i.getDataOrDefault(t,"front-isingrid",e.frontIsInGrid),n.backIsRandom=i.getDataOrDefault(t,"back-israndom",e.backIsRandom),n.backIsInGrid=i.getDataOrDefault(t,"back-isingrid",e.backIsInGrid)),n.doSwapFront=l.inArray("html",e.swapFront)>-1&&e.frontContent instanceof Array&&e.frontContent.length>0,n.doSwapBack=l.inArray("html",e.swapBack)>-1&&e.backContent instanceof Array&&e.backContent.length>0,e.htmlSwap="undefined"!=typeof e.htmlSwap?l.extend(n,e.htmlSwap):n,e.htmlSwap.doSwapFront&&(e.htmlSwap.frontBag=this.prepBag(e.htmlSwap.frontBag,e.frontContent,e.htmlSwap.prevFrontIndex),e.htmlSwap.frontStaticRndm=e.htmlSwap.frontBag.pop()),e.htmlSwap.doSwapBack&&(e.htmlSwap.backBag=this.prepBag(e.htmlSwap.backBag,e.backContent,e.htmlSwap.prevBackIndex),e.htmlSwap.backStaticRndm=e.htmlSwap.backBag.pop())},prepBag:function(l,e,t){l=l||[];for(var n=0,i=0;i<e.length;i++)(i!=t||1===l.length)&&(l[n]=i,n++);return o.shuffleArray(l)},getFrontSwapIndex:function(l){var e=0;return l.htmlSwap.frontIsRandom?(0===l.htmlSwap.frontBag.length&&(l.htmlSwap.frontBag=this.prepBag(l.htmlSwap.frontBag,l.frontContent,l.htmlSwap.prevFrontIndex)),e=l.htmlSwap.frontIsInGrid?l.htmlSwap.frontStaticRndm:l.htmlSwap.frontBag.pop()):e=l.htmlSwap.frontIsInGrid?l.htmlSwap.frontStaticIndex:l.htmlSwap.frontIndex,e},getBackSwapIndex:function(l){var e=0;return l.htmlSwap.backIsRandom?(0===l.htmlSwap.backBag.length&&(l.htmlSwap.backBag=this.prepBag(l.htmlSwap.backBag,l.backContent,l.htmlSwap.prevBackIndex)),e=l.htmlSwap.backIsInGrid?l.htmlSwap.backStaticRndm:l.htmlSwap.backBag.pop()):e=l.htmlSwap.backIsInGrid?l.htmlSwap.backStaticIndex:l.htmlSwap.backIndex,e},action:function(l,e,t,n){if(l.htmlSwap.doSwapFront||l.htmlSwap.doSwapBack){var i="flip-list"===l.mode,o=0,a=i?l.listData[Math.max(n,0)].isReversed:l.isReversed;if(i&&-1==n)return a?l.htmlSwap.doSwapBack&&(0===l.htmlSwap.backBag.length&&(l.htmlSwap.backBag=this.prepBag(l.htmlSwap.backBag,l.backContent,l.htmlSwap.backStaticRndm)),l.htmlSwap.backStaticRndm=l.htmlSwap.backBag.pop(),l.htmlSwap.backStaticIndex++,l.htmlSwap.backStaticIndex>=l.backContent.length&&(l.htmlSwap.backStaticIndex=0)):l.htmlSwap.doSwapFront&&(0===l.htmlSwap.frontBag.length&&(l.htmlSwap.frontBag=this.prepBag(l.htmlSwap.frontBag,l.frontContent,l.htmlSwap.frontStaticRndm)),l.htmlSwap.frontStaticRndm=l.htmlSwap.frontBag.pop(),l.htmlSwap.frontStaticIndex++,l.htmlSwap.frontStaticIndex>=l.frontContent.length&&(l.htmlSwap.frontStaticIndex=0)),void 0;if(a){if(!l.htmlSwap.doSwapBack)return;o=this.getBackSwapIndex(l),l.htmlSwap.prevBackIndex=o,t.html(l.backContent[l.htmlSwap.backIndex]),l.htmlSwap.backIndex++,l.htmlSwap.backIndex>=l.backContent.length&&(l.htmlSwap.backIndex=0),i||(l.htmlSwap.backStaticIndex++,l.htmlSwap.backStaticIndex>=l.backContent.length&&(l.htmlSwap.backStaticIndex=0))}else{if(!l.htmlSwap.doSwapFront)return;o=this.getFrontSwapIndex(l),l.htmlSwap.prevFrontIndex=o,"slide"===l.mode?l.startNow?t.html(l.frontContent[o]):e.html(l.frontContent[o]):t.html(l.frontContent[o]),l.htmlSwap.frontIndex++,l.htmlSwap.frontIndex>=l.frontContent.length&&(l.htmlSwap.frontIndex=0),i||(l.htmlSwap.frontStaticIndex++,l.htmlSwap.frontStaticIndex>=l.frontContent.length&&(l.htmlSwap.frontStaticIndex=0))}}}},imageSwap:{moduleName:"image",data:{preloadImages:!1,imageCssSelector:">img,>a>img",fadeSwap:!1,frontImages:[],frontIsRandom:!0,frontIsBackgroundImage:!1,frontIsInGrid:!1,backImages:null,backIsRandom:!0,backIsBackgroundImage:!1,backIsInGrid:!1},initData:function(e,t){var n={backBag:[],backIndex:0,backStaticIndex:0,backStaticRndm:-1,frontBag:[],frontIndex:0,frontStaticIndex:0,frontStaticRndm:-1,prevBackIndex:-1,prevFrontIndex:-1},o=e.ignoreDataAttributes;o?(n.imageCssSelector=i.getDataOrDefault(t,"image-css",e.imageCssSelector),n.fadeSwap=i.getDataOrDefault(t,"fadeswap",e.fadeSwap),n.frontIsRandom=i.getDataOrDefault(t,"front-israndom",e.frontIsRandom),n.frontIsInGrid=i.getDataOrDefault(t,"front-isingrid",e.frontIsInGrid),n.frontIsBackgroundImage=i.getDataOrDefault(t,"front-isbg",e.frontIsBackgroundImage),n.backIsRandom=i.getDataOrDefault(t,"back-israndom",e.backIsRandom),n.backIsInGrid=i.getDataOrDefault(t,"back-isingrid",e.backIsInGrid),n.backIsBackgroundImage=i.getDataOrDefault(t,"back-isbg",e.backIsBackgroundImage),n.doSwapFront=l.inArray("image",e.swapFront)>-1&&e.frontImages instanceof Array&&e.frontImages.length>0,n.doSwapBack=l.inArray("image",e.swapBack)>-1&&e.backImages instanceof Array&&e.backImages.length>0,n.alwaysSwapFront=i.getDataOrDefault(t,"front-alwaysswap",e.alwaysSwapFront),n.alwaysSwapBack=i.getDataOrDefault(t,"back-alwaysswap",e.alwaysSwapBack)):(n.imageCssSelector=e.imageCssSelector,n.fadeSwap=e.fadeSwap,n.frontIsRandom=e.frontIsRandom,n.frontIsInGrid=e.frontIsInGrid,n.frontIsBackgroundImage=e.frontIsBackgroundImage,n.backIsRandom=e.backIsRandom,n.backIsInGrid=e.backIsInGrid,n.backIsBackgroundImage=e.backIsBackgroundImage,n.doSwapFront=l.inArray("image",e.swapFront)>-1&&e.frontImages instanceof Array&&e.frontImages.length>0,n.doSwapBack=l.inArray("image",e.swapBack)>-1&&e.backImages instanceof Array&&e.backImages.length>0,n.alwaysSwapFront=e.alwaysSwapFront,n.alwaysSwapBack=e.alwaysSwapBack),e.imgSwap="undefined"!=typeof e.imgSwap?l.extend(n,e.imgSwap):n,e.imgSwap.doSwapFront&&(e.imgSwap.frontBag=this.prepBag(e.imgSwap.frontBag,e.frontImages,e.imgSwap.prevFrontIndex),e.imgSwap.frontStaticRndm=e.imgSwap.frontBag.pop(),e.preloadImages&&l(e.frontImages).metrojs.preloadImages(function(){})),e.imgSwap.doSwapBack&&(e.imgSwap.backBag=this.prepBag(e.imgSwap.backBag,e.backImages,e.imgSwap.prevBackIndex),e.imgSwap.backStaticRndm=e.imgSwap.backBag.pop(),e.preloadImages&&l(e.backImages).metrojs.preloadImages(function(){}))},prepBag:function(l,e,t){l=l||[];for(var n=0,i=0;i<e.length;i++)(i!=t||1===e.length)&&(l[n]=i,n++);return o.shuffleArray(l)},getFrontSwapIndex:function(l){var e=0;return l.imgSwap.frontIsRandom?(0===l.imgSwap.frontBag.length&&(l.imgSwap.frontBag=this.prepBag(l.imgSwap.frontBag,l.frontImages,l.imgSwap.prevFrontIndex)),e=l.imgSwap.frontIsInGrid?l.imgSwap.frontStaticRndm:l.imgSwap.frontBag.pop()):e=l.imgSwap.frontIsInGrid?l.imgSwap.frontStaticIndex:l.imgSwap.frontIndex,e},getBackSwapIndex:function(l){var e=0;return l.imgSwap.backIsRandom?(0===l.imgSwap.backBag.length&&(l.imgSwap.backBag=this.prepBag(l.imgSwap.backBag,l.backImages,l.imgSwap.prevBackIndex)),e=l.imgSwap.backIsInGrid?l.imgSwap.backStaticRndm:l.imgSwap.backBag.pop()):e=l.imgSwap.backIsInGrid?l.imgSwap.backStaticIndex:l.imgSwap.backIndex,e},setImageProperties:function(e,t,n){var i={},o={};"undefined"!=typeof t.src&&(n?i.backgroundImage="url('"+t.src+"')":o.src=t.src),"undefined"!=typeof t.alt&&(o.alt=t.alt),"object"==typeof t.css?e.css(l.extend(i,t.css)):e.css(i),"object"==typeof t.attr?e.attr(l.extend(o,t.attr)):e.attr(o)},action:function(l,e,t,n){if(l.imgSwap.doSwapFront||l.imgSwap.doSwapBack){var i="flip-list"===l.mode,o=("slide"==l.mode,0),r=i?l.listData[Math.max(n,0)].isReversed:l.isReversed;if(i&&-1==n)return(l.alwaysSwapFront||!r)&&l.imgSwap.doSwapFront&&(0===l.imgSwap.frontBag.length&&(l.imgSwap.frontBag=this.prepBag(l.imgSwap.frontBag,l.frontImages,l.imgSwap.frontStaticRndm)),l.imgSwap.frontStaticRndm=l.imgSwap.frontBag.pop(),l.imgSwap.frontStaticIndex++,l.imgSwap.frontStaticIndex>=l.frontImages.length&&(l.imgSwap.frontStaticIndex=0)),(l.alwaysSwapBack||r)&&l.imgSwap.doSwapBack&&(0===l.imgSwap.backBag.length&&(l.imgSwap.backBag=this.prepBag(l.imgSwap.backBag,l.backImages,l.imgSwap.backStaticRndm)),l.imgSwap.backStaticRndm=l.imgSwap.backBag.pop(),l.imgSwap.backStaticIndex++,l.imgSwap.backStaticIndex>=l.backImages.length&&(l.imgSwap.backStaticIndex=0)),void 0;var s,c,d,u;if(l.alwaysSwapFront||!r){if(!l.imgSwap.doSwapFront)return;o=this.getFrontSwapIndex(l),l.imgSwap.prevFrontIndex=o,s="slide"===l.mode?e:t,c=s.find(l.imgSwap.imageCssSelector),d="object"==typeof l.frontImages[o]?l.frontImages[o]:{src:l.frontImages[o]},u=function(e){var t=l.imgSwap.frontIsBackgroundImage;"function"==typeof e&&(t?window.setTimeout(e,100):c[0].onload=e),a.imageSwap.setImageProperties(c,d,t)},l.fadeSwap?c.fadeOut(function(){u(function(){c.fadeIn()})}):u(),l.imgSwap.frontIndex++,l.imgSwap.frontIndex>=l.frontImages.length&&(l.imgSwap.frontIndex=0),i||(l.imgSwap.frontStaticIndex++,l.imgSwap.frontStaticIndex>=l.frontImages.length&&(l.imgSwap.frontStaticIndex=0))}if(l.alwaysSwapBack||r){if(!l.imgSwap.doSwapBack)return;o=this.getBackSwapIndex(l),l.imgSwap.prevBackIndex=o,s=t,c=s.find(l.imgSwap.imageCssSelector),d="object"==typeof l.backImages[o]?l.backImages[o]:{src:l.backImages[o]},u=function(){a.imageSwap.setImageProperties(c,d,l.imgSwap.backIsBackgroundImage)},l.fadeSwap?c.fadeOut(function(){u(function(){c.fadeIn()})}):u(),l.imgSwap.backIndex++,l.imgSwap.backIndex>=l.backImages.length&&(l.imgSwap.backIndex=0),i||(l.imgSwap.backStaticIndex++,l.imgSwap.backStaticIndex>=l.backImages.length&&(l.imgSwap.backStaticIndex=0))}}}}};l.fn.metrojs.TileTimer=function(l,e,n){this.timerId=null,this.interval=l,this.action=e,this.count=0,this.repeatCount="undefined"==typeof n?0:n,this.start=function(e){window.clearTimeout(this.timerId);var t=this;this.timerId=window.setTimeout(function(){t.tick.call(t,l)},e)},this.tick=function(l){this.action(this.count+1),this.count++,this.count>=t&&(this.count=0),(this.repeatCount>0||-1==this.repeatCount)&&(this.count!=this.repeatCount?this.start(l):this.stop())},this.stop=function(){this.timerId=window.clearTimeout(this.timerId),this.reset()},this.resume=function(){(this.repeatCount>0||-1==this.repeatCount)&&this.count!=this.repeatCount&&this.start(l)},this.pause=function(){this.timerId=window.clearTimeout(this.timerId)},this.reset=function(){this.count=0},this.restart=function(l){this.stop(),this.start(l)}},jQuery.fn.metrojs.theme={loadDefaultTheme:function(l){if("undefined"==typeof l||null==l)l=jQuery.fn.metrojs.theme.defaults;else{var e=jQuery.fn.metrojs.theme.defaults;jQuery.extend(e,l),l=e}var t="undefined"!=typeof window.localStorage,n=function(l){return"undefined"!=typeof window.localStorage[l]&&null!=window.localStorage[l]};!t||n("Metro.JS.AccentColor")&&n("Metro.JS.BaseAccentColor")?t?(l.accentColor=window.localStorage["Metro.JS.AccentColor"],l.baseTheme=window.localStorage["Metro.JS.BaseAccentColor"],jQuery(l.accentCssSelector).addClass(l.accentColor).data("accent",l.accentColor),jQuery(l.baseThemeCssSelector).addClass(l.baseTheme),"function"==typeof l.loaded&&l.loaded(l.baseTheme,l.accentColor)):(jQuery(l.accentCssSelector).addClass(l.accentColor).data("accent",l.accentColor),jQuery(l.baseThemeCssSelector).addClass(l.baseTheme),"function"==typeof l.loaded&&l.loaded(l.baseTheme,l.accentColor),"undefined"!=typeof l.preloadAltBaseTheme&&l.preloadAltBaseTheme&&jQuery(["dark"==l.baseTheme?l.metroLightUrl:l.metroDarkUrl]).metrojs.preloadImages(function(){})):(window.localStorage["Metro.JS.AccentColor"]=l.accentColor,window.localStorage["Metro.JS.BaseAccentColor"]=l.baseTheme,jQuery(l.accentCssSelector).addClass(l.accentColor).data("accent",l.accentColor),jQuery(l.baseThemeCssSelector).addClass(l.baseTheme),"function"==typeof l.loaded&&l.loaded(l.baseTheme,l.accentColor),"undefined"!=typeof l.preloadAltBaseTheme&&l.preloadAltBaseTheme&&jQuery(["dark"==l.baseTheme?l.metroLightUrl:l.metroDarkUrl]).metrojs.preloadImages(function(){}))},applyTheme:function(l,e,t){if("undefined"==typeof t||null==t)t=jQuery.fn.metrojs.theme.defaults;else{var n=jQuery.fn.metrojs.theme.defaults;t=jQuery.extend({},n,t)}if("undefined"!=typeof l&&null!=l){"undefined"!=typeof window.localStorage&&(window.localStorage["Metro.JS.BaseAccentColor"]=l);var i=jQuery(t.baseThemeCssSelector);i.length>0&&("dark"==l?i.addClass("dark").removeClass("light"):"light"==l&&i.addClass("light").removeClass("dark"))}if("undefined"!=typeof e&&null!=e){"undefined"!=typeof window.localStorage&&(window.localStorage["Metro.JS.AccentColor"]=e);var o=jQuery(t.accentCssSelector);if(o.length>0){var a=!1;o.each(function(){jQuery(this).addClass(e);var l=jQuery(this).data("accent");if(l!=e){var t=jQuery(this).attr("class").replace(l,"");t=t.replace(/(\s)+/," "),jQuery(this).attr("class",t),jQuery(this).data("accent",e),a=!0}}),a&&"function"==typeof t.accentPicked&&t.accentPicked(e)}}},appendAccentColors:function(e){if("undefined"==typeof e||null==e)e=jQuery.fn.metrojs.theme.defaults;else{var t=jQuery.fn.metrojs.theme.defaults;e=jQuery.extend({},t,e)}for(var n="",i=e.accentColors,o=e.accentListTemplate,a=0;a<i.length;a++)n+=o.replace(/\{0\}/g,i[a]);l(n).appendTo(e.accentListContainer)},appendBaseThemes:function(e){if("undefined"==typeof e||null==e)e=jQuery.fn.metrojs.theme.defaults;else{var t=jQuery.fn.metrojs.theme.defaults;e=jQuery.extend({},t,e)}for(var n="",i=e.baseThemes,o=e.baseThemeListTemplate,a=0;a<i.length;a++)n+=o.replace(/\{0\}/g,i[a]);l(n).appendTo(e.baseThemeListContainer)},defaults:{baseThemeCssSelector:"body",accentCssSelector:".tiles",accentColor:"blue",baseTheme:"dark",accentColors:["amber","blue","brown","cobalt","crimson","cyan","magenta","lime","indigo","green","emerald","mango","mauve","olive","orange","pink","red","sienna","steel","teal","violet","yellow"],baseThemes:["light","dark"],accentListTemplate:"<li><a href='javascript:;' title='{0}' class='accent {0}'></a></li>",accentListContainer:"ul.theme-options,.theme-options>ul",baseThemeListTemplate:"<li><a href='javascript:;' title='{0}' class='accent {0}'></a></li>",baseThemeListContainer:"ul.base-theme-options,.base-theme-options>ul"}},jQuery.fn.applicationBar=function(e){var t="undefined"!=typeof jQuery.fn.metrojs.theme?jQuery.fn.metrojs.theme.defaults:{};if(jQuery.extend(t,jQuery.fn.applicationBar.defaults,e),"undefined"!=typeof jQuery.fn.metrojs.theme){var n=jQuery.fn.metrojs.theme;t.shouldApplyTheme&&n.loadDefaultTheme(t);var i=t.accentListContainer.replace(","," a,")+" a",o=function(){var l=jQuery(this).attr("class").replace("accent","").replace(" ","");n.applyTheme(null,l,t),"function"==typeof t.accentPicked&&t.accentPicked(l)},a=t.baseThemeListContainer.replace(","," a,")+" a",r=function(){var l=jQuery(this).attr("class").replace("accent","").replace(" ","");n.applyTheme(l,null,t),"function"==typeof t.themePicked&&t.themePicked(l)};"function"==typeof l.fn.on?(l(this).on("click.appBar",i,o),l(this).on("click.appBar",a,r)):(l(i).live("click.appBar",o),l(a).live("click.appBar",r))}return l(this).each(function(e,n){var i=l(n),o=l.extend({},t);"auto"==o.collapseHeight&&(o.collapseHeight=l(this).height()),navigator.userAgent.match(/(Android|webOS|iPhone|iPod|BlackBerry|PIE|IEMobile)/i)&&(navigator.userAgent.match(/(IEMobile\/1)/i)||navigator.userAgent.match(/(iPhone OS [56789])/i)||i.css({position:"absolute",bottom:"0px"})),o.slideOpen=function(){i.hasClass("expanded")||o.animateAppBar(!1)},o.slideClosed=function(){i.hasClass("expanded")&&o.animateAppBar(!0)},o.animateAppBar=function(l){var e=l?o.collapseHeight:o.expandHeight;l?i.removeClass("expanded"):i.hasClass("expanded")||i.addClass("expanded"),i.stop().animate({height:e},{duration:o.duration})},i.data("ApplicationBar",o),i.find(t.handleSelector).click(function(){o.animateAppBar(i.hasClass("expanded"))}),1==o.bindKeyboard&&jQuery(document.documentElement).keyup(function(l){38==l.keyCode?l.target&&null==l.target.tagName.match(/INPUT|TEXTAREA|SELECT/i)&&(i.hasClass("expanded")||o.animateAppBar(!1)):40==l.keyCode&&l.target&&null==l.target.tagName.match(/INPUT|TEXTAREA|SELECT/i)&&i.hasClass("expanded")&&o.animateAppBar(!0)})})},jQuery.fn.applicationBar.defaults={applyTheme:!0,themePicked:function(){},accentPicked:function(){},loaded:function(){},duration:300,expandHeight:"320px",collapseHeight:"auto",bindKeyboard:!0,handleSelector:"a.etc",metroLightUrl:"images/metroIcons_light.jpg",metroDarkUrl:"images/metroIcons.jpg",preloadAltBaseTheme:!1},l.fn.metrojs.preloadImages=function(e){var t=l(this).toArray(),n=l("<img style='display:none;' />").appendTo("body");l(this).each(function(){var l=this;"object"==typeof this&&(l=this.src),n.attr({src:l}).load(function(){for(var l=0;l<t.length;l++)t[l]==element&&t.splice(l,1);0==t.length&&e()})}),n.remove()},l.fn.metrojs.MetroModernizr=function(e){if("undefined"==typeof e&&(e={useHardwareAccel:!0,useModernizr:"undefined"!=typeof window.Modernizr}),this.isOldJQuery=/^1\.[0123]/.test(l.fn.jquery),this.isOldAndroid=function(){try{var e=navigator.userAgent;if(e.indexOf("Android")>=0){var t=parseFloat(e.slice(e.indexOf("Android")+8));if(2.3>t)return!0}}catch(n){l.error(n)}return!1}(),this.canTransform=!1,this.canTransition=!1,this.canTransform3d=!1,this.canAnimate=!1,this.canTouch=!1,this.canFlip3d=e.useHardwareAccel,1==e.useHardwareAccel)if(0==e.useModernizr)if("undefined"!=typeof window.MetroModernizr)this.canTransform=window.MetroModernizr.canTransform,this.canTransition=window.MetroModernizr.canTransition,this.canTransform3d=window.MetroModernizr.canTransform3d,this.canAnimate=window.MetroModernizr.canAnimate,this.canTouch=window.MetroModernizr.canTouch;else{window.MetroModernizr={};var t="metromodernizr",n=document.documentElement,i=document.head||document.getElementsByTagName("head")[0],o=document.createElement(t),a=o.style,r=" -webkit- -moz- -o- -ms- ".split(" "),s="Webkit Moz O ms Khtml".split(" "),c=function(l,e){for(var t in l)if(void 0!==a[l[t]]&&(!e||e(l[t],o)))return!0},d=function(l,e){var t=l.charAt(0).toUpperCase()+l.substr(1),n=(l+" "+s.join(t+" ")+t).split(" ");return!!c(n,e)},u=function(){var l=!!c(["perspectiveProperty","WebkitPerspective","MozPerspective","OPerspective","msPerspective"]);return l&&"webkitPerspective"in n.style&&(l=h(["@media (",r.join("transform-3d),("),t,")","{#metromodernizr{left:9px;position:absolute;height:3px;}}"].join(""),function(l){return 3===l.offsetHeight&&9===l.offsetLeft})),l},h=function(l,e){var o,a=document.createElement("style"),r=document.createElement("div");return a.textContent=l,i.appendChild(a),r.id=t,n.appendChild(r),o=e(r),a.parentNode.removeChild(a),r.parentNode.removeChild(r),!!o},f=function(){return canTouch="ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch||"undefined"!=typeof window.navigator.msMaxTouchPoints&&window.navigator.msMaxTouchPoints>0||h(["@media (",r.join("touch-enabled),("),t,")","{#metromodernizr{top:9px;position:absolute}}"].join(""),function(l){return 9===l.offsetTop})};this.canTransform=!!c(["transformProperty","WebkitTransform","MozTransform","OTransform","msTransform"]),this.canTransition=d("transitionProperty"),this.canTransform3d=u(),this.canAnimate=d("animationName"),this.canTouch=f(),window.MetroModernizr.canTransform=this.canTransform,window.MetroModernizr.canTransition=this.canTransition,window.MetroModernizr.canTransform3d=this.canTransform3d,window.MetroModernizr.canAnimate=this.canAnimate,window.MetroModernizr.canTouch=this.canTouch,n=null,i=null,o=null,a=null}else this.canTransform=l("html").hasClass("csstransforms"),this.canTransition=l("html").hasClass("csstransitions"),this.canTransform3d=l("html").hasClass("csstransforms3d"),this.canAnimate=l("html").hasClass("cssanimations"),this.canTouch=l("html").hasClass("touch")||"undefined"!=typeof window.navigator.msMaxTouchPoints&&window.navigator.msMaxTouchPoints>0;this.canFlip3d=this.canFlip3d&&this.canAnimate&&this.canTransform&&this.canTransform3d}}(jQuery);

!function(e,t,l){!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):jQuery&&!jQuery.fn.sparkline&&e(jQuery)}(function(n){"";"use strict";var i,a,o,r,s,c,u,d,h,f,p,m,g,v,y,b,C,T,E,w,D,_,S,I,x,k,O,R,M,N,A,K,L={},P=0;i=function(){return{common:{type:"line",lineColor:"#00f",fillColor:"#cdf",defaultPixelsPerValue:3,width:"auto",height:"auto",composite:!1,tagValuesAttribute:"values",tagOptionsPrefix:"spark",enableTagOptions:!1,enableHighlight:!0,highlightLighten:1.4,tooltipSkipNull:!0,tooltipPrefix:"",tooltipSuffix:"",disableHiddenCheck:!1,numberFormatter:!1,numberDigitGroupCount:3,numberDigitGroupSep:",",numberDecimalMark:".",disableTooltips:!1,disableInteraction:!1},line:{spotColor:"#f80",highlightSpotColor:"#5f5",highlightLineColor:"#f22",spotRadius:1.5,minSpotColor:"#f80",maxSpotColor:"#f80",lineWidth:1,normalRangeMin:l,normalRangeMax:l,normalRangeColor:"#ccc",drawNormalOnTop:!1,chartRangeMin:l,chartRangeMax:l,chartRangeMinX:l,chartRangeMaxX:l,tooltipFormat:new o('<span style="color: {{color}}">&#9679;</span> {{prefix}}{{y}}{{suffix}}')},bar:{barColor:"#3366cc",negBarColor:"#f44",stackedBarColor:["#3366cc","#dc3912","#ff9900","#109618","#66aa00","#dd4477","#0099c6","#990099"],zeroColor:l,nullColor:l,zeroAxis:!0,barWidth:4,barSpacing:1,chartRangeMax:l,chartRangeMin:l,chartRangeClip:!1,colorMap:l,tooltipFormat:new o('<span style="color: {{color}}">&#9679;</span> {{prefix}}{{value}}{{suffix}}')},tristate:{barWidth:4,barSpacing:1,posBarColor:"#6f6",negBarColor:"#f44",zeroBarColor:"#999",colorMap:{},tooltipFormat:new o('<span style="color: {{color}}">&#9679;</span> {{value:map}}'),tooltipValueLookups:{map:{"-1":"Loss",0:"Draw",1:"Win"}}},discrete:{lineHeight:"auto",thresholdColor:l,thresholdValue:0,chartRangeMax:l,chartRangeMin:l,chartRangeClip:!1,tooltipFormat:new o("{{prefix}}{{value}}{{suffix}}")},bullet:{targetColor:"#f33",targetWidth:3,performanceColor:"#33f",rangeColors:["#d3dafe","#a8b6ff","#7f94ff"],base:l,tooltipFormat:new o("{{fieldkey:fields}} - {{value}}"),tooltipValueLookups:{fields:{r:"Range",p:"Performance",t:"Target"}}},pie:{offset:0,sliceColors:["#3366cc","#dc3912","#ff9900","#109618","#66aa00","#dd4477","#0099c6","#990099"],borderWidth:0,borderColor:"#000",tooltipFormat:new o('<span style="color: {{color}}">&#9679;</span> {{value}} ({{percent.1}}%)')},box:{raw:!1,boxLineColor:"#000",boxFillColor:"#cdf",whiskerColor:"#000",outlierLineColor:"#333",outlierFillColor:"#fff",medianColor:"#f00",showOutliers:!0,outlierIQR:1.5,spotRadius:1.5,target:l,targetColor:"#4a2",chartRangeMax:l,chartRangeMin:l,tooltipFormat:new o("{{field:fields}}: {{value}}"),tooltipFormatFieldlistKey:"field",tooltipValueLookups:{fields:{lq:"Lower Quartile",med:"Median",uq:"Upper Quartile",lo:"Left Outlier",ro:"Right Outlier",lw:"Left Whisker",rw:"Right Whisker"}}}}},k='.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}',a=function(){var e,t;return e=function(){this.init.apply(this,arguments)},arguments.length>1?(arguments[0]?(e.prototype=n.extend(new arguments[0],arguments[arguments.length-1]),e._super=arguments[0].prototype):e.prototype=arguments[arguments.length-1],arguments.length>2&&(t=Array.prototype.slice.call(arguments,1,-1),t.unshift(e.prototype),n.extend.apply(n,t))):e.prototype=arguments[0],e.prototype.cls=e,e},n.SPFormatClass=o=a({fre:/\{\{([\w.]+?)(:(.+?))?\}\}/g,precre:/(\w+)\.(\d+)/,init:function(e,t){this.format=e,this.fclass=t},render:function(e,t,n){var i,a,o,r,s,c=this,u=e;return this.format.replace(this.fre,function(){var e;return a=arguments[1],o=arguments[3],i=c.precre.exec(a),i?(s=i[2],a=i[1]):s=!1,r=u[a],r===l?"":o&&t&&t[o]?(e=t[o],e.get?t[o].get(r)||r:t[o][r]||r):(h(r)&&(r=n.get("numberFormatter")?n.get("numberFormatter")(r):v(r,s,n.get("numberDigitGroupCount"),n.get("numberDigitGroupSep"),n.get("numberDecimalMark"))),r)})}}),n.spformat=function(e,t){return new o(e,t)},r=function(e,t,l){return t>e?t:e>l?l:e},s=function(e,l){var n;return 2===l?(n=t.floor(e.length/2),e.length%2?e[n]:(e[n-1]+e[n])/2):e.length%2?(n=(e.length*l+l)/4,n%1?(e[t.floor(n)]+e[t.floor(n)-1])/2:e[n-1]):(n=(e.length*l+2)/4,n%1?(e[t.floor(n)]+e[t.floor(n)-1])/2:e[n-1])},c=function(e){var t;switch(e){case"undefined":e=l;break;case"null":e=null;break;case"true":e=!0;break;case"false":e=!1;break;default:t=parseFloat(e),e==t&&(e=t)}return e},u=function(e){var t,l=[];for(t=e.length;t--;)l[t]=c(e[t]);return l},d=function(e,t){var l,n,i=[];for(l=0,n=e.length;n>l;l++)e[l]!==t&&i.push(e[l]);return i},h=function(e){return!isNaN(parseFloat(e))&&isFinite(e)},v=function(e,t,l,i,a){var o,r;for(e=(t===!1?parseFloat(e).toString():e.toFixed(t)).split(""),o=(o=n.inArray(".",e))<0?e.length:o,o<e.length&&(e[o]=a),r=o-l;r>0;r-=l)e.splice(r,0,i);return e.join("")},f=function(e,t,l){var n;for(n=t.length;n--;)if((!l||null!==t[n])&&t[n]!==e)return!1;return!0},p=function(e){var t,l=0;for(t=e.length;t--;)l+="number"==typeof e[t]?e[t]:0;return l},g=function(e){return n.isArray(e)?e:[e]},m=function(t){var l;e.createStyleSheet?e.createStyleSheet().cssText=t:(l=e.createElement("style"),l.type="text/css",e.getElementsByTagName("head")[0].appendChild(l),l["string"==typeof e.body.style.WebkitAppearance?"innerText":"innerHTML"]=t)},n.fn.simpledraw=function(t,i,a,o){var r,s;if(a&&(r=this.data("_jqs_vcanvas")))return r;if(n.fn.sparkline.canvas===!1)return!1;if(n.fn.sparkline.canvas===l){var c=e.createElement("canvas");if(c.getContext&&c.getContext("2d"))n.fn.sparkline.canvas=function(e,t,l,n){return new N(e,t,l,n)};else{if(!e.namespaces||e.namespaces.v)return n.fn.sparkline.canvas=!1,!1;e.namespaces.add("v","urn:schemas-microsoft-com:vml","#default#VML"),n.fn.sparkline.canvas=function(e,t,l){return new A(e,t,l)}}}return t===l&&(t=n(this).innerWidth()),i===l&&(i=n(this).innerHeight()),r=n.fn.sparkline.canvas(t,i,this,o),s=n(this).data("_jqs_mhandler"),s&&s.registerCanvas(r),r},n.fn.cleardraw=function(){var e=this.data("_jqs_vcanvas");e&&e.reset()},n.RangeMapClass=y=a({init:function(e){var t,l,n=[];for(t in e)e.hasOwnProperty(t)&&"string"==typeof t&&t.indexOf(":")>-1&&(l=t.split(":"),l[0]=0===l[0].length?-1/0:parseFloat(l[0]),l[1]=0===l[1].length?1/0:parseFloat(l[1]),l[2]=e[t],n.push(l));this.map=e,this.rangelist=n||!1},get:function(e){var t,n,i,a=this.rangelist;if((i=this.map[e])!==l)return i;if(a)for(t=a.length;t--;)if(n=a[t],n[0]<=e&&n[1]>=e)return n[2];return l}}),n.range_map=function(e){return new y(e)},b=a({init:function(e,t){var l=n(e);this.$el=l,this.options=t,this.currentPageX=0,this.currentPageY=0,this.el=e,this.splist=[],this.tooltip=null,this.over=!1,this.displayTooltips=!t.get("disableTooltips"),this.highlightEnabled=!t.get("disableHighlight")},registerSparkline:function(e){this.splist.push(e),this.over&&this.updateDisplay()},registerCanvas:function(e){var t=n(e.canvas);this.canvas=e,this.$canvas=t,t.mouseenter(n.proxy(this.mouseenter,this)),t.mouseleave(n.proxy(this.mouseleave,this)),t.click(n.proxy(this.mouseclick,this))},reset:function(e){this.splist=[],this.tooltip&&e&&(this.tooltip.remove(),this.tooltip=l)},mouseclick:function(e){var t=n.Event("sparklineClick");t.originalEvent=e,t.sparklines=this.splist,this.$el.trigger(t)},mouseenter:function(t){n(e.body).unbind("mousemove.jqs"),n(e.body).bind("mousemove.jqs",n.proxy(this.mousemove,this)),this.over=!0,this.currentPageX=t.pageX,this.currentPageY=t.pageY,this.currentEl=t.target,!this.tooltip&&this.displayTooltips&&(this.tooltip=new C(this.options),this.tooltip.updatePosition(t.pageX,t.pageY)),this.updateDisplay()},mouseleave:function(){n(e.body).unbind("mousemove.jqs");var t,l,i=this.splist,a=i.length,o=!1;for(this.over=!1,this.currentEl=null,this.tooltip&&(this.tooltip.remove(),this.tooltip=null),l=0;a>l;l++)t=i[l],t.clearRegionHighlight()&&(o=!0);o&&this.canvas.render()},mousemove:function(e){this.currentPageX=e.pageX,this.currentPageY=e.pageY,this.currentEl=e.target,this.tooltip&&this.tooltip.updatePosition(e.pageX,e.pageY),this.updateDisplay()},updateDisplay:function(){var e,t,l,i,a,o=this.splist,r=o.length,s=!1,c=this.$canvas.offset(),u=this.currentPageX-c.left,d=this.currentPageY-c.top;if(this.over){for(l=0;r>l;l++)t=o[l],i=t.setRegionHighlight(this.currentEl,u,d),i&&(s=!0);if(s){if(a=n.Event("sparklineRegionChange"),a.sparklines=this.splist,this.$el.trigger(a),this.tooltip){for(e="",l=0;r>l;l++)t=o[l],e+=t.getCurrentRegionTooltip();this.tooltip.setContent(e)}this.disableHighlight||this.canvas.render()}null===i&&this.mouseleave()}}}),C=a({sizeStyle:"position: static !important;display: block !important;visibility: hidden !important;float: left !important;padding: 5px 5px 15px 5px;min-height: 30px;min-width: 30px;",init:function(t){var l,i=t.get("tooltipClassname","jqstooltip"),a=this.sizeStyle;this.container=t.get("tooltipContainer")||e.body,this.tooltipOffsetX=t.get("tooltipOffsetX",10),this.tooltipOffsetY=t.get("tooltipOffsetY",12),n("#jqssizetip").remove(),n("#jqstooltip").remove(),this.sizetip=n("<div/>",{id:"jqssizetip",style:a,"class":i}),this.tooltip=n("<div/>",{id:"jqstooltip","class":i}).appendTo(this.container),l=this.tooltip.offset(),this.offsetLeft=l.left,this.offsetTop=l.top,this.hidden=!0,n(window).unbind("resize.jqs scroll.jqs"),n(window).bind("resize.jqs scroll.jqs",n.proxy(this.updateWindowDims,this)),this.updateWindowDims()},updateWindowDims:function(){this.scrollTop=n(window).scrollTop(),this.scrollLeft=n(window).scrollLeft(),this.scrollRight=this.scrollLeft+n(window).width(),this.updatePosition()},getSize:function(e){this.sizetip.html(e).appendTo(this.container),this.width=this.sizetip.width()+12,this.height=this.sizetip.height()+12,this.sizetip.remove()},setContent:function(e){return e?(this.getSize(e),this.tooltip.html(e).css({width:this.width,height:this.height,visibility:"visible"}),this.hidden&&(this.hidden=!1,this.updatePosition()),void 0):(this.tooltip.css("visibility","hidden"),this.hidden=!0,void 0)},updatePosition:function(e,t){if(e===l){if(this.mousex===l)return;e=this.mousex-this.offsetLeft,t=this.mousey-this.offsetTop}else this.mousex=e-=this.offsetLeft,this.mousey=t-=this.offsetTop;this.height&&this.width&&!this.hidden&&(t-=this.height+this.tooltipOffsetY,e+=this.tooltipOffsetX,t<this.scrollTop&&(t=this.scrollTop),e<this.scrollLeft?e=this.scrollLeft:e+this.width>this.scrollRight&&(e=this.scrollRight-this.width),this.tooltip.css({left:e,top:t}))},remove:function(){this.tooltip.remove(),this.sizetip.remove(),this.sizetip=this.tooltip=l,n(window).unbind("resize.jqs scroll.jqs")}}),O=function(){m(k)},n(O),K=[],n.fn.sparkline=function(t,i){return this.each(function(){var a,o,r=new n.fn.sparkline.options(this,i),s=n(this);if(a=function(){var i,a,o,c,u,d,h;return"html"===t||t===l?(h=this.getAttribute(r.get("tagValuesAttribute")),(h===l||null===h)&&(h=s.html()),i=h.replace(/(^\s*<!--)|(-->\s*$)|\s+/g,"").split(",")):i=t,a="auto"===r.get("width")?i.length*r.get("defaultPixelsPerValue"):r.get("width"),"auto"===r.get("height")?r.get("composite")&&n.data(this,"_jqs_vcanvas")||(c=e.createElement("span"),c.innerHTML="a",s.html(c),o=n(c).innerHeight()||n(c).height(),n(c).remove(),c=null):o=r.get("height"),r.get("disableInteraction")?u=!1:(u=n.data(this,"_jqs_mhandler"),u?r.get("composite")||u.reset():(u=new b(this,r),n.data(this,"_jqs_mhandler",u))),r.get("composite")&&!n.data(this,"_jqs_vcanvas")?(n.data(this,"_jqs_errnotify")||(alert("Attempted to attach a composite sparkline to an element with no existing sparkline"),n.data(this,"_jqs_errnotify",!0)),void 0):(d=new(n.fn.sparkline[r.get("type")])(this,i,r,a,o),d.render(),u&&u.registerSparkline(d),void 0)},n(this).html()&&!r.get("disableHiddenCheck")&&n(this).is(":hidden")||!n(this).parents("body").length){if(!r.get("composite")&&n.data(this,"_jqs_pending"))for(o=K.length;o;o--)K[o-1][0]==this&&K.splice(o-1,1);K.push([this,a]),n.data(this,"_jqs_pending",!0)}else a.call(this)})},n.fn.sparkline.defaults=i(),n.sparkline_display_visible=function(){var e,t,l,i=[];for(t=0,l=K.length;l>t;t++)e=K[t][0],n(e).is(":visible")&&!n(e).parents().is(":hidden")?(K[t][1].call(e),n.data(K[t][0],"_jqs_pending",!1),i.push(t)):!n(e).closest("html").length&&!n.data(e,"_jqs_pending")&&(n.data(K[t][0],"_jqs_pending",!1),i.push(t));for(t=i.length;t;t--)K.splice(i[t-1],1)},n.fn.sparkline.options=a({init:function(e,t){var l,i,a,o;this.userOptions=t=t||{},this.tag=e,this.tagValCache={},i=n.fn.sparkline.defaults,a=i.common,this.tagOptionsPrefix=t.enableTagOptions&&(t.tagOptionsPrefix||a.tagOptionsPrefix),o=this.getTagSetting("type"),l=o===L?i[t.type||a.type]:i[o],this.mergedOptions=n.extend({},a,l,t)},getTagSetting:function(e){var t,n,i,a,o=this.tagOptionsPrefix;if(o===!1||o===l)return L;if(this.tagValCache.hasOwnProperty(e))t=this.tagValCache.key;else{if(t=this.tag.getAttribute(o+e),t===l||null===t)t=L;else if("["===t.substr(0,1))for(t=t.substr(1,t.length-2).split(","),n=t.length;n--;)t[n]=c(t[n].replace(/(^\s*)|(\s*$)/g,""));else if("{"===t.substr(0,1))for(i=t.substr(1,t.length-2).split(","),t={},n=i.length;n--;)a=i[n].split(":",2),t[a[0].replace(/(^\s*)|(\s*$)/g,"")]=c(a[1].replace(/(^\s*)|(\s*$)/g,""));else t=c(t);this.tagValCache.key=t}return t},get:function(e,t){var n,i=this.getTagSetting(e);return i!==L?i:(n=this.mergedOptions[e])===l?t:n}}),n.fn.sparkline._base=a({disabled:!1,init:function(e,t,i,a,o){this.el=e,this.$el=n(e),this.values=t,this.options=i,this.width=a,this.height=o,this.currentRegion=l},initTarget:function(){var e=!this.options.get("disableInteraction");(this.target=this.$el.simpledraw(this.width,this.height,this.options.get("composite"),e))?(this.canvasWidth=this.target.pixelWidth,this.canvasHeight=this.target.pixelHeight):this.disabled=!0},render:function(){return this.disabled?(this.el.innerHTML="",!1):!0},getRegion:function(){},setRegionHighlight:function(e,t,n){var i,a=this.currentRegion,o=!this.options.get("disableHighlight");return t>this.canvasWidth||n>this.canvasHeight||0>t||0>n?null:(i=this.getRegion(e,t,n),a!==i?(a!==l&&o&&this.removeHighlight(),this.currentRegion=i,i!==l&&o&&this.renderHighlight(),!0):!1)},clearRegionHighlight:function(){return this.currentRegion!==l?(this.removeHighlight(),this.currentRegion=l,!0):!1},renderHighlight:function(){this.changeHighlight(!0)},removeHighlight:function(){this.changeHighlight(!1)},changeHighlight:function(){},getCurrentRegionTooltip:function(){var e,t,i,a,r,s,c,u,d,h,f,p,m,g,v=this.options,y="",b=[];if(this.currentRegion===l)return"";if(e=this.getCurrentRegionFields(),f=v.get("tooltipFormatter"))return f(this,v,e);if(v.get("tooltipChartTitle")&&(y+='<div class="jqs jqstitle">'+v.get("tooltipChartTitle")+"</div>\n"),t=this.options.get("tooltipFormat"),!t)return"";if(n.isArray(t)||(t=[t]),n.isArray(e)||(e=[e]),c=this.options.get("tooltipFormatFieldlist"),u=this.options.get("tooltipFormatFieldlistKey"),c&&u){for(d=[],s=e.length;s--;)h=e[s][u],-1!=(g=n.inArray(h,c))&&(d[g]=e[s]);e=d}for(i=t.length,m=e.length,s=0;i>s;s++)for(p=t[s],"string"==typeof p&&(p=new o(p)),a=p.fclass||"jqsfield",g=0;m>g;g++)e[g].isNull&&v.get("tooltipSkipNull")||(n.extend(e[g],{prefix:v.get("tooltipPrefix"),suffix:v.get("tooltipSuffix")}),r=p.render(e[g],v.get("tooltipValueLookups"),v),b.push('<div class="'+a+'">'+r+"</div>"));return b.length?y+b.join("\n"):""},getCurrentRegionFields:function(){},calcHighlightColor:function(e,l){var n,i,a,o,s=l.get("highlightColor"),c=l.get("highlightLighten");if(s)return s;if(c&&(n=/^#([0-9a-f])([0-9a-f])([0-9a-f])$/i.exec(e)||/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i.exec(e))){for(a=[],i=4===e.length?16:1,o=0;3>o;o++)a[o]=r(t.round(parseInt(n[o+1],16)*i*c),0,255);return"rgb("+a.join(",")+")"}return e}}),T={changeHighlight:function(e){var t,l=this.currentRegion,i=this.target,a=this.regionShapes[l];a&&(t=this.renderRegion(l,e),n.isArray(t)||n.isArray(a)?(i.replaceWithShapes(a,t),this.regionShapes[l]=n.map(t,function(e){return e.id})):(i.replaceWithShape(a,t),this.regionShapes[l]=t.id))},render:function(){var e,t,l,i,a=this.values,o=this.target,r=this.regionShapes;if(this.cls._super.render.call(this)){for(l=a.length;l--;)if(e=this.renderRegion(l))if(n.isArray(e)){for(t=[],i=e.length;i--;)e[i].append(),t.push(e[i].id);r[l]=t}else e.append(),r[l]=e.id;else r[l]=null;o.render()}}},n.fn.sparkline.line=E=a(n.fn.sparkline._base,{type:"line",init:function(e,t,l,n,i){E._super.init.call(this,e,t,l,n,i),this.vertices=[],this.regionMap=[],this.xvalues=[],this.yvalues=[],this.yminmax=[],this.hightlightSpotId=null,this.lastShapeId=null,this.initTarget()},getRegion:function(e,t){var n,i=this.regionMap;for(n=i.length;n--;)if(null!==i[n]&&t>=i[n][0]&&t<=i[n][1])return i[n][2];return l},getCurrentRegionFields:function(){var e=this.currentRegion;return{isNull:null===this.yvalues[e],x:this.xvalues[e],y:this.yvalues[e],color:this.options.get("lineColor"),fillColor:this.options.get("fillColor"),offset:e}},renderHighlight:function(){var e,t,n=this.currentRegion,i=this.target,a=this.vertices[n],o=this.options,r=o.get("spotRadius"),s=o.get("highlightSpotColor"),c=o.get("highlightLineColor");a&&(r&&s&&(e=i.drawCircle(a[0],a[1],r,l,s),this.highlightSpotId=e.id,i.insertAfterShape(this.lastShapeId,e)),c&&(t=i.drawLine(a[0],this.canvasTop,a[0],this.canvasTop+this.canvasHeight,c),this.highlightLineId=t.id,i.insertAfterShape(this.lastShapeId,t)))},removeHighlight:function(){var e=this.target;this.highlightSpotId&&(e.removeShapeId(this.highlightSpotId),this.highlightSpotId=null),this.highlightLineId&&(e.removeShapeId(this.highlightLineId),this.highlightLineId=null)},scanValues:function(){var e,l,n,i,a,o=this.values,r=o.length,s=this.xvalues,c=this.yvalues,u=this.yminmax;for(e=0;r>e;e++)l=o[e],n="string"==typeof o[e],i="object"==typeof o[e]&&o[e]instanceof Array,a=n&&o[e].split(":"),n&&2===a.length?(s.push(Number(a[0])),c.push(Number(a[1])),u.push(Number(a[1]))):i?(s.push(l[0]),c.push(l[1]),u.push(l[1])):(s.push(e),null===o[e]||"null"===o[e]?c.push(null):(c.push(Number(l)),u.push(Number(l))));this.options.get("xvalues")&&(s=this.options.get("xvalues")),this.maxy=this.maxyorg=t.max.apply(t,u),this.miny=this.minyorg=t.min.apply(t,u),this.maxx=t.max.apply(t,s),this.minx=t.min.apply(t,s),this.xvalues=s,this.yvalues=c,this.yminmax=u},processRangeOptions:function(){var e=this.options,t=e.get("normalRangeMin"),n=e.get("normalRangeMax");t!==l&&(t<this.miny&&(this.miny=t),n>this.maxy&&(this.maxy=n)),e.get("chartRangeMin")!==l&&(e.get("chartRangeClip")||e.get("chartRangeMin")<this.miny)&&(this.miny=e.get("chartRangeMin")),e.get("chartRangeMax")!==l&&(e.get("chartRangeClip")||e.get("chartRangeMax")>this.maxy)&&(this.maxy=e.get("chartRangeMax")),e.get("chartRangeMinX")!==l&&(e.get("chartRangeClipX")||e.get("chartRangeMinX")<this.minx)&&(this.minx=e.get("chartRangeMinX")),e.get("chartRangeMaxX")!==l&&(e.get("chartRangeClipX")||e.get("chartRangeMaxX")>this.maxx)&&(this.maxx=e.get("chartRangeMaxX"))},drawNormalRange:function(e,n,i,a,o){var r=this.options.get("normalRangeMin"),s=this.options.get("normalRangeMax"),c=n+t.round(i-i*((s-this.miny)/o)),u=t.round(i*(s-r)/o);this.target.drawRect(e,c,a,u,l,this.options.get("normalRangeColor")).append()},render:function(){var e,i,a,o,r,s,c,u,d,h,f,p,m,g,v,b,C,T,w,D,_,S,I,x,k,O=this.options,R=this.target,M=this.canvasWidth,N=this.canvasHeight,A=this.vertices,K=O.get("spotRadius"),L=this.regionMap;if(E._super.render.call(this)&&(this.scanValues(),this.processRangeOptions(),I=this.xvalues,x=this.yvalues,this.yminmax.length&&!(this.yvalues.length<2))){for(o=r=0,e=0===this.maxx-this.minx?1:this.maxx-this.minx,i=0===this.maxy-this.miny?1:this.maxy-this.miny,a=this.yvalues.length-1,K&&(4*K>M||4*K>N)&&(K=0),K&&(_=O.get("highlightSpotColor")&&!O.get("disableInteraction"),(_||O.get("minSpotColor")||O.get("spotColor")&&x[a]===this.miny)&&(N-=t.ceil(K)),(_||O.get("maxSpotColor")||O.get("spotColor")&&x[a]===this.maxy)&&(N-=t.ceil(K),o+=t.ceil(K)),(_||(O.get("minSpotColor")||O.get("maxSpotColor"))&&(x[0]===this.miny||x[0]===this.maxy))&&(r+=t.ceil(K),M-=t.ceil(K)),(_||O.get("spotColor")||O.get("minSpotColor")||O.get("maxSpotColor")&&(x[a]===this.miny||x[a]===this.maxy))&&(M-=t.ceil(K))),N--,O.get("normalRangeMin")!==l&&!O.get("drawNormalOnTop")&&this.drawNormalRange(r,o,N,M,i),c=[],u=[c],g=v=null,b=x.length,k=0;b>k;k++)d=I[k],f=I[k+1],h=x[k],p=r+t.round((d-this.minx)*(M/e)),m=b-1>k?r+t.round((f-this.minx)*(M/e)):M,v=p+(m-p)/2,L[k]=[g||0,v,k],g=v,null===h?k&&(null!==x[k-1]&&(c=[],u.push(c)),A.push(null)):(h<this.miny&&(h=this.miny),h>this.maxy&&(h=this.maxy),c.length||c.push([p,o+N]),s=[p,o+t.round(N-N*((h-this.miny)/i))],c.push(s),A.push(s));for(C=[],T=[],w=u.length,k=0;w>k;k++)c=u[k],c.length&&(O.get("fillColor")&&(c.push([c[c.length-1][0],o+N]),T.push(c.slice(0)),c.pop()),c.length>2&&(c[0]=[c[0][0],c[1][1]]),C.push(c));for(w=T.length,k=0;w>k;k++)R.drawShape(T[k],O.get("fillColor"),O.get("fillColor")).append();for(O.get("normalRangeMin")!==l&&O.get("drawNormalOnTop")&&this.drawNormalRange(r,o,N,M,i),w=C.length,k=0;w>k;k++)R.drawShape(C[k],O.get("lineColor"),l,O.get("lineWidth")).append();if(K&&O.get("valueSpots"))for(D=O.get("valueSpots"),D.get===l&&(D=new y(D)),k=0;b>k;k++)S=D.get(x[k]),S&&R.drawCircle(r+t.round((I[k]-this.minx)*(M/e)),o+t.round(N-N*((x[k]-this.miny)/i)),K,l,S).append();K&&O.get("spotColor")&&null!==x[a]&&R.drawCircle(r+t.round((I[I.length-1]-this.minx)*(M/e)),o+t.round(N-N*((x[a]-this.miny)/i)),K,l,O.get("spotColor")).append(),this.maxy!==this.minyorg&&(K&&O.get("minSpotColor")&&(d=I[n.inArray(this.minyorg,x)],R.drawCircle(r+t.round((d-this.minx)*(M/e)),o+t.round(N-N*((this.minyorg-this.miny)/i)),K,l,O.get("minSpotColor")).append()),K&&O.get("maxSpotColor")&&(d=I[n.inArray(this.maxyorg,x)],R.drawCircle(r+t.round((d-this.minx)*(M/e)),o+t.round(N-N*((this.maxyorg-this.miny)/i)),K,l,O.get("maxSpotColor")).append())),this.lastShapeId=R.getLastShapeId(),this.canvasTop=o,R.render()}}}),n.fn.sparkline.bar=w=a(n.fn.sparkline._base,T,{type:"bar",init:function(e,i,a,o,s){var h,f,p,m,g,v,b,C,T,E,D,_,S,I,x,k,O,R,M,N,A,K,L=parseInt(a.get("barWidth"),10),P=parseInt(a.get("barSpacing"),10),F=a.get("chartRangeMin"),B=a.get("chartRangeMax"),$=a.get("chartRangeClip"),H=1/0,Z=-1/0;for(w._super.init.call(this,e,i,a,o,s),v=0,b=i.length;b>v;v++)N=i[v],h="string"==typeof N&&N.indexOf(":")>-1,(h||n.isArray(N))&&(x=!0,h&&(N=i[v]=u(N.split(":"))),N=d(N,null),f=t.min.apply(t,N),p=t.max.apply(t,N),H>f&&(H=f),p>Z&&(Z=p));this.stacked=x,this.regionShapes={},this.barWidth=L,this.barSpacing=P,this.totalBarWidth=L+P,this.width=o=i.length*L+(i.length-1)*P,this.initTarget(),$&&(S=F===l?-1/0:F,I=B===l?1/0:B),g=[],m=x?[]:g;var z=[],U=[];for(v=0,b=i.length;b>v;v++)if(x)for(k=i[v],i[v]=M=[],z[v]=0,m[v]=U[v]=0,O=0,R=k.length;R>O;O++)N=M[O]=$?r(k[O],S,I):k[O],null!==N&&(N>0&&(z[v]+=N),0>H&&Z>0?0>N?U[v]+=t.abs(N):m[v]+=N:m[v]+=t.abs(N-(0>N?Z:H)),g.push(N));else N=$?r(i[v],S,I):i[v],N=i[v]=c(N),null!==N&&g.push(N);this.max=_=t.max.apply(t,g),this.min=D=t.min.apply(t,g),this.stackMax=Z=x?t.max.apply(t,z):_,this.stackMin=H=x?t.min.apply(t,g):D,a.get("chartRangeMin")!==l&&(a.get("chartRangeClip")||a.get("chartRangeMin")<D)&&(D=a.get("chartRangeMin")),a.get("chartRangeMax")!==l&&(a.get("chartRangeClip")||a.get("chartRangeMax")>_)&&(_=a.get("chartRangeMax")),this.zeroAxis=T=a.get("zeroAxis",!0),E=0>=D&&_>=0&&T?0:0==T?D:D>0?D:_,this.xaxisOffset=E,C=x?t.max.apply(t,m)+t.max.apply(t,U):_-D,this.canvasHeightEf=T&&0>D?this.canvasHeight-2:this.canvasHeight-1,E>D?(K=x&&_>=0?Z:_,A=(K-E)/C*this.canvasHeight,A!==t.ceil(A)&&(this.canvasHeightEf-=2,A=t.ceil(A))):A=this.canvasHeight,this.yoffset=A,n.isArray(a.get("colorMap"))?(this.colorMapByIndex=a.get("colorMap"),this.colorMapByValue=null):(this.colorMapByIndex=null,this.colorMapByValue=a.get("colorMap"),this.colorMapByValue&&this.colorMapByValue.get===l&&(this.colorMapByValue=new y(this.colorMapByValue))),this.range=C},getRegion:function(e,n){var i=t.floor(n/this.totalBarWidth);return 0>i||i>=this.values.length?l:i},getCurrentRegionFields:function(){var e,t,l=this.currentRegion,n=g(this.values[l]),i=[];for(t=n.length;t--;)e=n[t],i.push({isNull:null===e,value:e,color:this.calcColor(t,e,l),offset:l});return i},calcColor:function(e,t,i){var a,o,r=this.colorMapByIndex,s=this.colorMapByValue,c=this.options;return a=this.stacked?c.get("stackedBarColor"):0>t?c.get("negBarColor"):c.get("barColor"),0===t&&c.get("zeroColor")!==l&&(a=c.get("zeroColor")),s&&(o=s.get(t))?a=o:r&&r.length>i&&(a=r[i]),n.isArray(a)?a[e%a.length]:a},renderRegion:function(e,i){var a,o,r,s,c,u,d,h,p,m,g=this.values[e],v=this.options,y=this.xaxisOffset,b=[],C=this.range,T=this.stacked,E=this.target,w=e*this.totalBarWidth,D=this.canvasHeightEf,_=this.yoffset;if(g=n.isArray(g)?g:[g],d=g.length,h=g[0],s=f(null,g),m=f(y,g,!0),s)return v.get("nullColor")?(r=i?v.get("nullColor"):this.calcHighlightColor(v.get("nullColor"),v),a=_>0?_-1:_,E.drawRect(w,a,this.barWidth-1,0,r,r)):l;for(c=_,u=0;d>u;u++){if(h=g[u],T&&h===y){if(!m||p)continue;p=!0}o=C>0?t.floor(D*(t.abs(h-y)/C))+1:1,y>h||h===y&&0===_?(a=c,c+=o):(a=_-o,_-=o),r=this.calcColor(u,h,e),i&&(r=this.calcHighlightColor(r,v)),b.push(E.drawRect(w,a,this.barWidth-1,o-1,r,r))}return 1===b.length?b[0]:b}}),n.fn.sparkline.tristate=D=a(n.fn.sparkline._base,T,{type:"tristate",init:function(e,t,i,a,o){var r=parseInt(i.get("barWidth"),10),s=parseInt(i.get("barSpacing"),10);D._super.init.call(this,e,t,i,a,o),this.regionShapes={},this.barWidth=r,this.barSpacing=s,this.totalBarWidth=r+s,this.values=n.map(t,Number),this.width=a=t.length*r+(t.length-1)*s,n.isArray(i.get("colorMap"))?(this.colorMapByIndex=i.get("colorMap"),this.colorMapByValue=null):(this.colorMapByIndex=null,this.colorMapByValue=i.get("colorMap"),this.colorMapByValue&&this.colorMapByValue.get===l&&(this.colorMapByValue=new y(this.colorMapByValue))),this.initTarget()},getRegion:function(e,l){return t.floor(l/this.totalBarWidth)},getCurrentRegionFields:function(){var e=this.currentRegion;return{isNull:this.values[e]===l,value:this.values[e],color:this.calcColor(this.values[e],e),offset:e}},calcColor:function(e,t){var l,n,i=this.values,a=this.options,o=this.colorMapByIndex,r=this.colorMapByValue;return l=r&&(n=r.get(e))?n:o&&o.length>t?o[t]:i[t]<0?a.get("negBarColor"):i[t]>0?a.get("posBarColor"):a.get("zeroBarColor")},renderRegion:function(e,l){var n,i,a,o,r,s,c=this.values,u=this.options,d=this.target;return n=d.pixelHeight,a=t.round(n/2),o=e*this.totalBarWidth,c[e]<0?(r=a,i=a-1):c[e]>0?(r=0,i=a-1):(r=a-1,i=2),s=this.calcColor(c[e],e),null!==s?(l&&(s=this.calcHighlightColor(s,u)),d.drawRect(o,r,this.barWidth-1,i-1,s,s)):void 0}}),n.fn.sparkline.discrete=_=a(n.fn.sparkline._base,T,{type:"discrete",init:function(e,i,a,o,r){_._super.init.call(this,e,i,a,o,r),this.regionShapes={},this.values=i=n.map(i,Number),this.min=t.min.apply(t,i),this.max=t.max.apply(t,i),this.range=this.max-this.min,this.width=o="auto"===a.get("width")?2*i.length:this.width,this.interval=t.floor(o/i.length),this.itemWidth=o/i.length,a.get("chartRangeMin")!==l&&(a.get("chartRangeClip")||a.get("chartRangeMin")<this.min)&&(this.min=a.get("chartRangeMin")),a.get("chartRangeMax")!==l&&(a.get("chartRangeClip")||a.get("chartRangeMax")>this.max)&&(this.max=a.get("chartRangeMax")),this.initTarget(),this.target&&(this.lineHeight="auto"===a.get("lineHeight")?t.round(.3*this.canvasHeight):a.get("lineHeight"))},getRegion:function(e,l){return t.floor(l/this.itemWidth)},getCurrentRegionFields:function(){var e=this.currentRegion;return{isNull:this.values[e]===l,value:this.values[e],offset:e}},renderRegion:function(e,l){var n,i,a,o,s=this.values,c=this.options,u=this.min,d=this.max,h=this.range,f=this.interval,p=this.target,m=this.canvasHeight,g=this.lineHeight,v=m-g;return i=r(s[e],u,d),o=e*f,n=t.round(v-v*((i-u)/h)),a=c.get("thresholdColor")&&i<c.get("thresholdValue")?c.get("thresholdColor"):c.get("lineColor"),l&&(a=this.calcHighlightColor(a,c)),p.drawLine(o,n,o,n+g,a)}}),n.fn.sparkline.bullet=S=a(n.fn.sparkline._base,{type:"bullet",init:function(e,n,i,a,o){var r,s,c;S._super.init.call(this,e,n,i,a,o),this.values=n=u(n),c=n.slice(),c[0]=null===c[0]?c[2]:c[0],c[1]=null===n[1]?c[2]:c[1],r=t.min.apply(t,n),s=t.max.apply(t,n),r=i.get("base")===l?0>r?r:0:i.get("base"),this.min=r,this.max=s,this.range=s-r,this.shapes={},this.valueShapes={},this.regiondata={},this.width=a="auto"===i.get("width")?"4.0em":a,this.target=this.$el.simpledraw(a,o,i.get("composite")),n.length||(this.disabled=!0),this.initTarget()},getRegion:function(e,t,n){var i=this.target.getShapeAt(e,t,n);return i!==l&&this.shapes[i]!==l?this.shapes[i]:l},getCurrentRegionFields:function(){var e=this.currentRegion;return{fieldkey:e.substr(0,1),value:this.values[e.substr(1)],region:e}},changeHighlight:function(e){var t,l=this.currentRegion,n=this.valueShapes[l];switch(delete this.shapes[n],l.substr(0,1)){case"r":t=this.renderRange(l.substr(1),e);break;case"p":t=this.renderPerformance(e);break;case"t":t=this.renderTarget(e)}this.valueShapes[l]=t.id,this.shapes[t.id]=l,this.target.replaceWithShape(n,t)},renderRange:function(e,l){var n=this.values[e],i=t.round(this.canvasWidth*((n-this.min)/this.range)),a=this.options.get("rangeColors")[e-2];return l&&(a=this.calcHighlightColor(a,this.options)),this.target.drawRect(0,0,i-1,this.canvasHeight-1,a,a)},renderPerformance:function(e){var l=this.values[1],n=t.round(this.canvasWidth*((l-this.min)/this.range)),i=this.options.get("performanceColor");return e&&(i=this.calcHighlightColor(i,this.options)),this.target.drawRect(0,t.round(.3*this.canvasHeight),n-1,t.round(.4*this.canvasHeight)-1,i,i)},renderTarget:function(e){var l=this.values[0],n=t.round(this.canvasWidth*((l-this.min)/this.range)-this.options.get("targetWidth")/2),i=t.round(.1*this.canvasHeight),a=this.canvasHeight-2*i,o=this.options.get("targetColor");return e&&(o=this.calcHighlightColor(o,this.options)),this.target.drawRect(n,i,this.options.get("targetWidth")-1,a-1,o,o)},render:function(){var e,t,l=this.values.length,n=this.target;if(S._super.render.call(this)){for(e=2;l>e;e++)t=this.renderRange(e).append(),this.shapes[t.id]="r"+e,this.valueShapes["r"+e]=t.id;null!==this.values[1]&&(t=this.renderPerformance().append(),this.shapes[t.id]="p1",this.valueShapes.p1=t.id),null!==this.values[0]&&(t=this.renderTarget().append(),this.shapes[t.id]="t0",this.valueShapes.t0=t.id),n.render()}}}),n.fn.sparkline.pie=I=a(n.fn.sparkline._base,{type:"pie",init:function(e,l,i,a,o){var r,s=0;if(I._super.init.call(this,e,l,i,a,o),this.shapes={},this.valueShapes={},this.values=l=n.map(l,Number),"auto"===i.get("width")&&(this.width=this.height),l.length>0)for(r=l.length;r--;)s+=l[r];this.total=s,this.initTarget(),this.radius=t.floor(t.min(this.canvasWidth,this.canvasHeight)/2)},getRegion:function(e,t,n){var i=this.target.getShapeAt(e,t,n);return i!==l&&this.shapes[i]!==l?this.shapes[i]:l},getCurrentRegionFields:function(){var e=this.currentRegion;return{isNull:this.values[e]===l,value:this.values[e],percent:100*(this.values[e]/this.total),color:this.options.get("sliceColors")[e%this.options.get("sliceColors").length],offset:e}},changeHighlight:function(e){var t=this.currentRegion,l=this.renderSlice(t,e),n=this.valueShapes[t];delete this.shapes[n],this.target.replaceWithShape(n,l),this.valueShapes[t]=l.id,this.shapes[l.id]=t},renderSlice:function(e,n){var i,a,o,r,s,c=this.target,u=this.options,d=this.radius,h=u.get("borderWidth"),f=u.get("offset"),p=2*t.PI,m=this.values,g=this.total,v=f?2*t.PI*(f/360):0;for(r=m.length,o=0;r>o;o++){if(i=v,a=v,g>0&&(a=v+p*(m[o]/g)),e===o)return s=u.get("sliceColors")[o%u.get("sliceColors").length],n&&(s=this.calcHighlightColor(s,u)),c.drawPieSlice(d,d,d-h,i,a,l,s);v=a}},render:function(){var e,n,i=this.target,a=this.values,o=this.options,r=this.radius,s=o.get("borderWidth");
    if(I._super.render.call(this)){for(s&&i.drawCircle(r,r,t.floor(r-s/2),o.get("borderColor"),l,s).append(),n=a.length;n--;)a[n]&&(e=this.renderSlice(n).append(),this.valueShapes[n]=e.id,this.shapes[e.id]=n);i.render()}}}),n.fn.sparkline.box=x=a(n.fn.sparkline._base,{type:"box",init:function(e,t,l,i,a){x._super.init.call(this,e,t,l,i,a),this.values=n.map(t,Number),this.width="auto"===l.get("width")?"4.0em":i,this.initTarget(),this.values.length||(this.disabled=1)},getRegion:function(){return 1},getCurrentRegionFields:function(){var e=[{field:"lq",value:this.quartiles[0]},{field:"med",value:this.quartiles[1]},{field:"uq",value:this.quartiles[2]}];return this.loutlier!==l&&e.push({field:"lo",value:this.loutlier}),this.routlier!==l&&e.push({field:"ro",value:this.routlier}),this.lwhisker!==l&&e.push({field:"lw",value:this.lwhisker}),this.rwhisker!==l&&e.push({field:"rw",value:this.rwhisker}),e},render:function(){var e,n,i,a,o,r,c,u,d,h,f,p=this.target,m=this.values,g=m.length,v=this.options,y=this.canvasWidth,b=this.canvasHeight,C=v.get("chartRangeMin")===l?t.min.apply(t,m):v.get("chartRangeMin"),T=v.get("chartRangeMax")===l?t.max.apply(t,m):v.get("chartRangeMax"),E=0;if(x._super.render.call(this)){if(v.get("raw"))v.get("showOutliers")&&m.length>5?(n=m[0],e=m[1],a=m[2],o=m[3],r=m[4],c=m[5],u=m[6]):(e=m[0],a=m[1],o=m[2],r=m[3],c=m[4]);else if(m.sort(function(e,t){return e-t}),a=s(m,1),o=s(m,2),r=s(m,3),i=r-a,v.get("showOutliers")){for(e=c=l,d=0;g>d;d++)e===l&&m[d]>a-i*v.get("outlierIQR")&&(e=m[d]),m[d]<r+i*v.get("outlierIQR")&&(c=m[d]);n=m[0],u=m[g-1]}else e=m[0],c=m[g-1];this.quartiles=[a,o,r],this.lwhisker=e,this.rwhisker=c,this.loutlier=n,this.routlier=u,f=y/(T-C+1),v.get("showOutliers")&&(E=t.ceil(v.get("spotRadius")),y-=2*t.ceil(v.get("spotRadius")),f=y/(T-C+1),e>n&&p.drawCircle((n-C)*f+E,b/2,v.get("spotRadius"),v.get("outlierLineColor"),v.get("outlierFillColor")).append(),u>c&&p.drawCircle((u-C)*f+E,b/2,v.get("spotRadius"),v.get("outlierLineColor"),v.get("outlierFillColor")).append()),p.drawRect(t.round((a-C)*f+E),t.round(.1*b),t.round((r-a)*f),t.round(.8*b),v.get("boxLineColor"),v.get("boxFillColor")).append(),p.drawLine(t.round((e-C)*f+E),t.round(b/2),t.round((a-C)*f+E),t.round(b/2),v.get("lineColor")).append(),p.drawLine(t.round((e-C)*f+E),t.round(b/4),t.round((e-C)*f+E),t.round(b-b/4),v.get("whiskerColor")).append(),p.drawLine(t.round((c-C)*f+E),t.round(b/2),t.round((r-C)*f+E),t.round(b/2),v.get("lineColor")).append(),p.drawLine(t.round((c-C)*f+E),t.round(b/4),t.round((c-C)*f+E),t.round(b-b/4),v.get("whiskerColor")).append(),p.drawLine(t.round((o-C)*f+E),t.round(.1*b),t.round((o-C)*f+E),t.round(.9*b),v.get("medianColor")).append(),v.get("target")&&(h=t.ceil(v.get("spotRadius")),p.drawLine(t.round((v.get("target")-C)*f+E),t.round(b/2-h),t.round((v.get("target")-C)*f+E),t.round(b/2+h),v.get("targetColor")).append(),p.drawLine(t.round((v.get("target")-C)*f+E-h),t.round(b/2),t.round((v.get("target")-C)*f+E+h),t.round(b/2),v.get("targetColor")).append()),p.render()}}}),R=a({init:function(e,t,l,n){this.target=e,this.id=t,this.type=l,this.args=n},append:function(){return this.target.appendShape(this),this}}),M=a({_pxregex:/(\d+)(px)?\s*$/i,init:function(e,t,l){e&&(this.width=e,this.height=t,this.target=l,this.lastShapeId=null,l[0]&&(l=l[0]),n.data(l,"_jqs_vcanvas",this))},drawLine:function(e,t,l,n,i,a){return this.drawShape([[e,t],[l,n]],i,a)},drawShape:function(e,t,l,n){return this._genShape("Shape",[e,t,l,n])},drawCircle:function(e,t,l,n,i,a){return this._genShape("Circle",[e,t,l,n,i,a])},drawPieSlice:function(e,t,l,n,i,a,o){return this._genShape("PieSlice",[e,t,l,n,i,a,o])},drawRect:function(e,t,l,n,i,a){return this._genShape("Rect",[e,t,l,n,i,a])},getElement:function(){return this.canvas},getLastShapeId:function(){return this.lastShapeId},reset:function(){alert("reset not implemented")},_insert:function(e,t){n(t).html(e)},_calculatePixelDims:function(e,t,l){var i;i=this._pxregex.exec(t),this.pixelHeight=i?i[1]:n(l).height(),i=this._pxregex.exec(e),this.pixelWidth=i?i[1]:n(l).width()},_genShape:function(e,t){var l=P++;return t.unshift(l),new R(this,l,e,t)},appendShape:function(){alert("appendShape not implemented")},replaceWithShape:function(){alert("replaceWithShape not implemented")},insertAfterShape:function(){alert("insertAfterShape not implemented")},removeShapeId:function(){alert("removeShapeId not implemented")},getShapeAt:function(){alert("getShapeAt not implemented")},render:function(){alert("render not implemented")}}),N=a(M,{init:function(t,i,a,o){N._super.init.call(this,t,i,a),this.canvas=e.createElement("canvas"),a[0]&&(a=a[0]),n.data(a,"_jqs_vcanvas",this),n(this.canvas).css({display:"inline-block",width:t,height:i,verticalAlign:"top"}),this._insert(this.canvas,a),this._calculatePixelDims(t,i,this.canvas),this.canvas.width=this.pixelWidth,this.canvas.height=this.pixelHeight,this.interact=o,this.shapes={},this.shapeseq=[],this.currentTargetShapeId=l,n(this.canvas).css({width:this.pixelWidth,height:this.pixelHeight})},_getContext:function(e,t,n){var i=this.canvas.getContext("2d");return e!==l&&(i.strokeStyle=e),i.lineWidth=n===l?1:n,t!==l&&(i.fillStyle=t),i},reset:function(){var e=this._getContext();e.clearRect(0,0,this.pixelWidth,this.pixelHeight),this.shapes={},this.shapeseq=[],this.currentTargetShapeId=l},_drawShape:function(e,t,n,i,a){var o,r,s=this._getContext(n,i,a);for(s.beginPath(),s.moveTo(t[0][0]+.5,t[0][1]+.5),o=1,r=t.length;r>o;o++)s.lineTo(t[o][0]+.5,t[o][1]+.5);n!==l&&s.stroke(),i!==l&&s.fill(),this.targetX!==l&&this.targetY!==l&&s.isPointInPath(this.targetX,this.targetY)&&(this.currentTargetShapeId=e)},_drawCircle:function(e,n,i,a,o,r,s){var c=this._getContext(o,r,s);c.beginPath(),c.arc(n,i,a,0,2*t.PI,!1),this.targetX!==l&&this.targetY!==l&&c.isPointInPath(this.targetX,this.targetY)&&(this.currentTargetShapeId=e),o!==l&&c.stroke(),r!==l&&c.fill()},_drawPieSlice:function(e,t,n,i,a,o,r,s){var c=this._getContext(r,s);c.beginPath(),c.moveTo(t,n),c.arc(t,n,i,a,o,!1),c.lineTo(t,n),c.closePath(),r!==l&&c.stroke(),s&&c.fill(),this.targetX!==l&&this.targetY!==l&&c.isPointInPath(this.targetX,this.targetY)&&(this.currentTargetShapeId=e)},_drawRect:function(e,t,l,n,i,a,o){return this._drawShape(e,[[t,l],[t+n,l],[t+n,l+i],[t,l+i],[t,l]],a,o)},appendShape:function(e){return this.shapes[e.id]=e,this.shapeseq.push(e.id),this.lastShapeId=e.id,e.id},replaceWithShape:function(e,t){var l,n=this.shapeseq;for(this.shapes[t.id]=t,l=n.length;l--;)n[l]==e&&(n[l]=t.id);delete this.shapes[e]},replaceWithShapes:function(e,t){var l,n,i,a=this.shapeseq,o={};for(n=e.length;n--;)o[e[n]]=!0;for(n=a.length;n--;)l=a[n],o[l]&&(a.splice(n,1),delete this.shapes[l],i=n);for(n=t.length;n--;)a.splice(i,0,t[n].id),this.shapes[t[n].id]=t[n]},insertAfterShape:function(e,t){var l,n=this.shapeseq;for(l=n.length;l--;)if(n[l]===e)return n.splice(l+1,0,t.id),this.shapes[t.id]=t,void 0},removeShapeId:function(e){var t,l=this.shapeseq;for(t=l.length;t--;)if(l[t]===e){l.splice(t,1);break}delete this.shapes[e]},getShapeAt:function(e,t,l){return this.targetX=t,this.targetY=l,this.render(),this.currentTargetShapeId},render:function(){var e,t,l,n=this.shapeseq,i=this.shapes,a=n.length,o=this._getContext();for(o.clearRect(0,0,this.pixelWidth,this.pixelHeight),l=0;a>l;l++)e=n[l],t=i[e],this["_draw"+t.type].apply(this,t.args);this.interact||(this.shapes={},this.shapeseq=[])}}),A=a(M,{init:function(t,l,i){var a;A._super.init.call(this,t,l,i),i[0]&&(i=i[0]),n.data(i,"_jqs_vcanvas",this),this.canvas=e.createElement("span"),n(this.canvas).css({display:"inline-block",position:"relative",overflow:"hidden",width:t,height:l,margin:"0px",padding:"0px",verticalAlign:"top"}),this._insert(this.canvas,i),this._calculatePixelDims(t,l,this.canvas),this.canvas.width=this.pixelWidth,this.canvas.height=this.pixelHeight,a='<v:group coordorigin="0 0" coordsize="'+this.pixelWidth+" "+this.pixelHeight+'"'+' style="position:absolute;top:0;left:0;width:'+this.pixelWidth+"px;height="+this.pixelHeight+'px;"></v:group>',this.canvas.insertAdjacentHTML("beforeEnd",a),this.group=n(this.canvas).children()[0],this.rendered=!1,this.prerender=""},_drawShape:function(e,t,n,i,a){var o,r,s,c,u,d,h,f=[];for(h=0,d=t.length;d>h;h++)f[h]=""+t[h][0]+","+t[h][1];return o=f.splice(0,1),a=a===l?1:a,r=n===l?' stroked="false" ':' strokeWeight="'+a+'px" strokeColor="'+n+'" ',s=i===l?' filled="false"':' fillColor="'+i+'" filled="true" ',c=f[0]===f[f.length-1]?"x ":"",u='<v:shape coordorigin="0 0" coordsize="'+this.pixelWidth+" "+this.pixelHeight+'" '+' id="jqsshape'+e+'" '+r+s+' style="position:absolute;left:0px;top:0px;height:'+this.pixelHeight+"px;width:"+this.pixelWidth+'px;padding:0px;margin:0px;" '+' path="m '+o+" l "+f.join(", ")+" "+c+'e">'+" </v:shape>"},_drawCircle:function(e,t,n,i,a,o,r){var s,c,u;return t-=i,n-=i,s=a===l?' stroked="false" ':' strokeWeight="'+r+'px" strokeColor="'+a+'" ',c=o===l?' filled="false"':' fillColor="'+o+'" filled="true" ',u='<v:oval  id="jqsshape'+e+'" '+s+c+' style="position:absolute;top:'+n+"px; left:"+t+"px; width:"+2*i+"px; height:"+2*i+'px"></v:oval>'},_drawPieSlice:function(e,n,i,a,o,r,s,c){var u,d,h,f,p,m,g,v;if(o===r)return"";if(r-o===2*t.PI&&(o=0,r=2*t.PI),d=n+t.round(t.cos(o)*a),h=i+t.round(t.sin(o)*a),f=n+t.round(t.cos(r)*a),p=i+t.round(t.sin(r)*a),d===f&&h===p){if(r-o<t.PI)return"";d=f=n+a,h=p=i}return d===f&&h===p&&r-o<t.PI?"":(u=[n-a,i-a,n+a,i+a,d,h,f,p],m=s===l?' stroked="false" ':' strokeWeight="1px" strokeColor="'+s+'" ',g=c===l?' filled="false"':' fillColor="'+c+'" filled="true" ',v='<v:shape coordorigin="0 0" coordsize="'+this.pixelWidth+" "+this.pixelHeight+'" '+' id="jqsshape'+e+'" '+m+g+' style="position:absolute;left:0px;top:0px;height:'+this.pixelHeight+"px;width:"+this.pixelWidth+'px;padding:0px;margin:0px;" '+' path="m '+n+","+i+" wa "+u.join(", ")+' x e">'+" </v:shape>")},_drawRect:function(e,t,l,n,i,a,o){return this._drawShape(e,[[t,l],[t,l+i],[t+n,l+i],[t+n,l],[t,l]],a,o)},reset:function(){this.group.innerHTML=""},appendShape:function(e){var t=this["_draw"+e.type].apply(this,e.args);return this.rendered?this.group.insertAdjacentHTML("beforeEnd",t):this.prerender+=t,this.lastShapeId=e.id,e.id},replaceWithShape:function(e,t){var l=n("#jqsshape"+e),i=this["_draw"+t.type].apply(this,t.args);l[0].outerHTML=i},replaceWithShapes:function(e,t){var l,i=n("#jqsshape"+e[0]),a="",o=t.length;for(l=0;o>l;l++)a+=this["_draw"+t[l].type].apply(this,t[l].args);for(i[0].outerHTML=a,l=1;l<e.length;l++)n("#jqsshape"+e[l]).remove()},insertAfterShape:function(e,t){var l=n("#jqsshape"+e),i=this["_draw"+t.type].apply(this,t.args);l[0].insertAdjacentHTML("afterEnd",i)},removeShapeId:function(e){var t=n("#jqsshape"+e);this.group.removeChild(t[0])},getShapeAt:function(e){var t=e.id.substr(8);return t},render:function(){this.rendered||(this.group.innerHTML=this.prerender,this.rendered=!0)}})})}(document,Math);



(function(global) {
    "";"use strict";

    /* Set up a RequestAnimationFrame shim so we can animate efficiently FOR
     * GREAT JUSTICE. */
    var requestInterval, cancelInterval;

    (function() {
        var raf = global.requestAnimationFrame       ||
                global.webkitRequestAnimationFrame ||
                global.mozRequestAnimationFrame    ||
                global.oRequestAnimationFrame      ||
                global.msRequestAnimationFrame     ,
            caf = global.cancelAnimationFrame        ||
                global.webkitCancelAnimationFrame  ||
                global.mozCancelAnimationFrame     ||
                global.oCancelAnimationFrame       ||
                global.msCancelAnimationFrame      ;

        if(raf && caf) {
            requestInterval = function(fn, delay) {
                var handle = {value: null};

                function loop() {
                    handle.value = raf(loop);
                    fn();
                }

                loop();
                return handle;
            };

            cancelInterval = function(handle) {
                caf(handle.value);
            };
        }

        else {
            requestInterval = setInterval;
            cancelInterval = clearInterval;
        }
    }());

    /* Catmull-rom spline stuffs. */
    /*
     function upsample(n, spline) {
     var polyline = [],
     len = spline.length,
     bx  = spline[0],
     by  = spline[1],
     cx  = spline[2],
     cy  = spline[3],
     dx  = spline[4],
     dy  = spline[5],
     i, j, ax, ay, px, qx, rx, sx, py, qy, ry, sy, t;

     for(i = 6; i !== spline.length; i += 2) {
     ax = bx;
     bx = cx;
     cx = dx;
     dx = spline[i    ];
     px = -0.5 * ax + 1.5 * bx - 1.5 * cx + 0.5 * dx;
     qx =        ax - 2.5 * bx + 2.0 * cx - 0.5 * dx;
     rx = -0.5 * ax            + 0.5 * cx           ;
     sx =                   bx                      ;

     ay = by;
     by = cy;
     cy = dy;
     dy = spline[i + 1];
     py = -0.5 * ay + 1.5 * by - 1.5 * cy + 0.5 * dy;
     qy =        ay - 2.5 * by + 2.0 * cy - 0.5 * dy;
     ry = -0.5 * ay            + 0.5 * cy           ;
     sy =                   by                      ;

     for(j = 0; j !== n; ++j) {
     t = j / n;

     polyline.push(
     ((px * t + qx) * t + rx) * t + sx,
     ((py * t + qy) * t + ry) * t + sy
     );
     }
     }

     polyline.push(
     px + qx + rx + sx,
     py + qy + ry + sy
     );

     return polyline;
     }

     function downsample(n, polyline) {
     var len = 0,
     i, dx, dy;

     for(i = 2; i !== polyline.length; i += 2) {
     dx = polyline[i    ] - polyline[i - 2];
     dy = polyline[i + 1] - polyline[i - 1];
     len += Math.sqrt(dx * dx + dy * dy);
     }

     len /= n;

     var small = [],
     target = len,
     min = 0,
     max, t;

     small.push(polyline[0], polyline[1]);

     for(i = 2; i !== polyline.length; i += 2) {
     dx = polyline[i    ] - polyline[i - 2];
     dy = polyline[i + 1] - polyline[i - 1];
     max = min + Math.sqrt(dx * dx + dy * dy);

     if(max > target) {
     t = (target - min) / (max - min);

     small.push(
     polyline[i - 2] + dx * t,
     polyline[i - 1] + dy * t
     );

     target += len;
     }

     min = max;
     }

     small.push(polyline[polyline.length - 2], polyline[polyline.length - 1]);

     return small;
     }
     */

    /* Define skycon things. */
    /* FIXME: I'm *really really* sorry that this code is so gross. Really, I am.
     * I'll try to clean it up eventually! Promise! */
    var KEYFRAME = 500,
        STROKE = 0.08,
        TAU = 2.0 * Math.PI,
        TWO_OVER_SQRT_2 = 2.0 / Math.sqrt(2);

    function circle(ctx, x, y, r) {
        ctx.beginPath();
        ctx.arc(x, y, r, 0, TAU, false);
        ctx.fill();
    }

    function line(ctx, ax, ay, bx, by) {
        ctx.beginPath();
        ctx.moveTo(ax, ay);
        ctx.lineTo(bx, by);
        ctx.stroke();
    }

    function puff(ctx, t, cx, cy, rx, ry, rmin, rmax) {
        var c = Math.cos(t * TAU),
            s = Math.sin(t * TAU);

        rmax -= rmin;

        circle(
            ctx,
            cx - s * rx,
            cy + c * ry + rmax * 0.5,
            rmin + (1 - c * 0.5) * rmax
        );
    }

    function puffs(ctx, t, cx, cy, rx, ry, rmin, rmax) {
        var i;

        for(i = 5; i--; )
            puff(ctx, t + i / 5, cx, cy, rx, ry, rmin, rmax);
    }

    function cloud(ctx, t, cx, cy, cw, s, color) {
        t /= 30000;

        var a = cw * 0.21,
            b = cw * 0.12,
            c = cw * 0.24,
            d = cw * 0.28;

        ctx.fillStyle = color;
        puffs(ctx, t, cx, cy, a, b, c, d);

        ctx.globalCompositeOperation = 'destination-out';
        puffs(ctx, t, cx, cy, a, b, c - s, d - s);
        ctx.globalCompositeOperation = 'source-over';
    }

    function sun(ctx, t, cx, cy, cw, s, color) {
        t /= 120000;

        var a = cw * 0.25 - s * 0.5,
            b = cw * 0.32 + s * 0.5,
            c = cw * 0.50 - s * 0.5,
            i, p, cos, sin;

        ctx.strokeStyle = color;
        ctx.lineWidth = s;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        ctx.beginPath();
        ctx.arc(cx, cy, a, 0, TAU, false);
        ctx.stroke();

        for(i = 8; i--; ) {
            p = (t + i / 8) * TAU;
            cos = Math.cos(p);
            sin = Math.sin(p);
            line(ctx, cx + cos * b, cy + sin * b, cx + cos * c, cy + sin * c);
        }
    }

    function moon(ctx, t, cx, cy, cw, s, color) {
        t /= 15000;

        var a = cw * 0.29 - s * 0.5,
            b = cw * 0.05,
            c = Math.cos(t * TAU),
            p = c * TAU / -16;

        ctx.strokeStyle = color;
        ctx.lineWidth = s;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        cx += c * b;

        ctx.beginPath();
        ctx.arc(cx, cy, a, p + TAU / 8, p + TAU * 7 / 8, false);
        ctx.arc(cx + Math.cos(p) * a * TWO_OVER_SQRT_2, cy + Math.sin(p) * a * TWO_OVER_SQRT_2, a, p + TAU * 5 / 8, p + TAU * 3 / 8, true);
        ctx.closePath();
        ctx.stroke();
    }

    function rain(ctx, t, cx, cy, cw, s, color) {
        t /= 1350;

        var a = cw * 0.16,
            b = TAU * 11 / 12,
            c = TAU *  7 / 12,
            i, p, x, y;

        ctx.fillStyle = color;

        for(i = 4; i--; ) {
            p = (t + i / 4) % 1;
            x = cx + ((i - 1.5) / 1.5) * (i === 1 || i === 2 ? -1 : 1) * a;
            y = cy + p * p * cw;
            ctx.beginPath();
            ctx.moveTo(x, y - s * 1.5);
            ctx.arc(x, y, s * 0.75, b, c, false);
            ctx.fill();
        }
    }

    function sleet(ctx, t, cx, cy, cw, s, color) {
        t /= 750;

        var a = cw * 0.1875,
            b = TAU * 11 / 12,
            c = TAU *  7 / 12,
            i, p, x, y;

        ctx.strokeStyle = color;
        ctx.lineWidth = s * 0.5;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        for(i = 4; i--; ) {
            p = (t + i / 4) % 1;
            x = Math.floor(cx + ((i - 1.5) / 1.5) * (i === 1 || i === 2 ? -1 : 1) * a) + 0.5;
            y = cy + p * cw;
            line(ctx, x, y - s * 1.5, x, y + s * 1.5);
        }
    }

    function snow(ctx, t, cx, cy, cw, s, color) {
        t /= 3000;

        var a  = cw * 0.16,
            b  = s * 0.75,
            u  = t * TAU * 0.7,
            ux = Math.cos(u) * b,
            uy = Math.sin(u) * b,
            v  = u + TAU / 3,
            vx = Math.cos(v) * b,
            vy = Math.sin(v) * b,
            w  = u + TAU * 2 / 3,
            wx = Math.cos(w) * b,
            wy = Math.sin(w) * b,
            i, p, x, y;

        ctx.strokeStyle = color;
        ctx.lineWidth = s * 0.5;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        for(i = 4; i--; ) {
            p = (t + i / 4) % 1;
            x = cx + Math.sin((p + i / 4) * TAU) * a;
            y = cy + p * cw;

            line(ctx, x - ux, y - uy, x + ux, y + uy);
            line(ctx, x - vx, y - vy, x + vx, y + vy);
            line(ctx, x - wx, y - wy, x + wx, y + wy);
        }
    }

    function fogbank(ctx, t, cx, cy, cw, s, color) {
        t /= 30000;

        var a = cw * 0.21,
            b = cw * 0.06,
            c = cw * 0.21,
            d = cw * 0.28;

        ctx.fillStyle = color;
        puffs(ctx, t, cx, cy, a, b, c, d);

        ctx.globalCompositeOperation = 'destination-out';
        puffs(ctx, t, cx, cy, a, b, c - s, d - s);
        ctx.globalCompositeOperation = 'source-over';
    }

    /*
     var WIND_PATHS = [
     downsample(63, upsample(8, [
     -1.00, -0.28,
     -0.75, -0.18,
     -0.50,  0.12,
     -0.20,  0.12,
     -0.04, -0.04,
     -0.07, -0.18,
     -0.19, -0.18,
     -0.23, -0.05,
     -0.12,  0.11,
     0.02,  0.16,
     0.20,  0.15,
     0.50,  0.07,
     0.75,  0.18,
     1.00,  0.28
     ])),
     downsample(31, upsample(16, [
     -1.00, -0.10,
     -0.75,  0.00,
     -0.50,  0.10,
     -0.25,  0.14,
     0.00,  0.10,
     0.25,  0.00,
     0.50, -0.10,
     0.75, -0.14,
     1.00, -0.10
     ]))
     ];
     */

    var WIND_PATHS = [
            [
                -0.7500, -0.1800, -0.7219, -0.1527, -0.6971, -0.1225,
                -0.6739, -0.0910, -0.6516, -0.0588, -0.6298, -0.0262,
                -0.6083,  0.0065, -0.5868,  0.0396, -0.5643,  0.0731,
                -0.5372,  0.1041, -0.5033,  0.1259, -0.4662,  0.1406,
                -0.4275,  0.1493, -0.3881,  0.1530, -0.3487,  0.1526,
                -0.3095,  0.1488, -0.2708,  0.1421, -0.2319,  0.1342,
                -0.1943,  0.1217, -0.1600,  0.1025, -0.1290,  0.0785,
                -0.1012,  0.0509, -0.0764,  0.0206, -0.0547, -0.0120,
                -0.0378, -0.0472, -0.0324, -0.0857, -0.0389, -0.1241,
                -0.0546, -0.1599, -0.0814, -0.1876, -0.1193, -0.1964,
                -0.1582, -0.1935, -0.1931, -0.1769, -0.2157, -0.1453,
                -0.2290, -0.1085, -0.2327, -0.0697, -0.2240, -0.0317,
                -0.2064,  0.0033, -0.1853,  0.0362, -0.1613,  0.0672,
                -0.1350,  0.0961, -0.1051,  0.1213, -0.0706,  0.1397,
                -0.0332,  0.1512,  0.0053,  0.1580,  0.0442,  0.1624,
                0.0833,  0.1636,  0.1224,  0.1615,  0.1613,  0.1565,
                0.1999,  0.1500,  0.2378,  0.1402,  0.2749,  0.1279,
                0.3118,  0.1147,  0.3487,  0.1015,  0.3858,  0.0892,
                0.4236,  0.0787,  0.4621,  0.0715,  0.5012,  0.0702,
                0.5398,  0.0766,  0.5768,  0.0890,  0.6123,  0.1055,
                0.6466,  0.1244,  0.6805,  0.1440,  0.7147,  0.1630,
                0.7500,  0.1800
            ],
            [
                -0.7500,  0.0000, -0.7033,  0.0195, -0.6569,  0.0399,
                -0.6104,  0.0600, -0.5634,  0.0789, -0.5155,  0.0954,
                -0.4667,  0.1089, -0.4174,  0.1206, -0.3676,  0.1299,
                -0.3174,  0.1365, -0.2669,  0.1398, -0.2162,  0.1391,
                -0.1658,  0.1347, -0.1157,  0.1271, -0.0661,  0.1169,
                -0.0170,  0.1046,  0.0316,  0.0903,  0.0791,  0.0728,
                0.1259,  0.0534,  0.1723,  0.0331,  0.2188,  0.0129,
                0.2656, -0.0064,  0.3122, -0.0263,  0.3586, -0.0466,
                0.4052, -0.0665,  0.4525, -0.0847,  0.5007, -0.1002,
                0.5497, -0.1130,  0.5991, -0.1240,  0.6491, -0.1325,
                0.6994, -0.1380,  0.7500, -0.1400
            ]
        ],
        WIND_OFFSETS = [
            {start: 0.36, end: 0.11},
            {start: 0.56, end: 0.16}
        ];

    function leaf(ctx, t, x, y, cw, s, color) {
        var a = cw / 8,
            b = a / 3,
            c = 2 * b,
            d = (t % 1) * TAU,
            e = Math.cos(d),
            f = Math.sin(d);

        ctx.fillStyle = color;
        ctx.strokeStyle = color;
        ctx.lineWidth = s;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        ctx.beginPath();
        ctx.arc(x        , y        , a, d          , d + Math.PI, false);
        ctx.arc(x - b * e, y - b * f, c, d + Math.PI, d          , false);
        ctx.arc(x + c * e, y + c * f, b, d + Math.PI, d          , true );
        ctx.globalCompositeOperation = 'destination-out';
        ctx.fill();
        ctx.globalCompositeOperation = 'source-over';
        ctx.stroke();
    }

    function swoosh(ctx, t, cx, cy, cw, s, index, total, color) {
        t /= 2500;

        var path = WIND_PATHS[index],
            a = (t + index - WIND_OFFSETS[index].start) % total,
            c = (t + index - WIND_OFFSETS[index].end  ) % total,
            e = (t + index                            ) % total,
            b, d, f, i;

        ctx.strokeStyle = color;
        ctx.lineWidth = s;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        if(a < 1) {
            ctx.beginPath();

            a *= path.length / 2 - 1;
            b  = Math.floor(a);
            a -= b;
            b *= 2;
            b += 2;

            ctx.moveTo(
                cx + (path[b - 2] * (1 - a) + path[b    ] * a) * cw,
                cy + (path[b - 1] * (1 - a) + path[b + 1] * a) * cw
            );

            if(c < 1) {
                c *= path.length / 2 - 1;
                d  = Math.floor(c);
                c -= d;
                d *= 2;
                d += 2;

                for(i = b; i !== d; i += 2)
                    ctx.lineTo(cx + path[i] * cw, cy + path[i + 1] * cw);

                ctx.lineTo(
                    cx + (path[d - 2] * (1 - c) + path[d    ] * c) * cw,
                    cy + (path[d - 1] * (1 - c) + path[d + 1] * c) * cw
                );
            }

            else
                for(i = b; i !== path.length; i += 2)
                    ctx.lineTo(cx + path[i] * cw, cy + path[i + 1] * cw);

            ctx.stroke();
        }

        else if(c < 1) {
            ctx.beginPath();

            c *= path.length / 2 - 1;
            d  = Math.floor(c);
            c -= d;
            d *= 2;
            d += 2;

            ctx.moveTo(cx + path[0] * cw, cy + path[1] * cw);

            for(i = 2; i !== d; i += 2)
                ctx.lineTo(cx + path[i] * cw, cy + path[i + 1] * cw);

            ctx.lineTo(
                cx + (path[d - 2] * (1 - c) + path[d    ] * c) * cw,
                cy + (path[d - 1] * (1 - c) + path[d + 1] * c) * cw
            );

            ctx.stroke();
        }

        if(e < 1) {
            e *= path.length / 2 - 1;
            f  = Math.floor(e);
            e -= f;
            f *= 2;
            f += 2;

            leaf(
                ctx,
                t,
                cx + (path[f - 2] * (1 - e) + path[f    ] * e) * cw,
                cy + (path[f - 1] * (1 - e) + path[f + 1] * e) * cw,
                cw,
                s,
                color
            );
        }
    }

    var Skycons = function(opts) {
        this.list        = [];
        this.interval    = null;
        this.color       = opts && opts.color ? opts.color : "black";
        this.resizeClear = !!(opts && opts.resizeClear);
    };

    Skycons.CLEAR_DAY = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        sun(ctx, t, w * 0.5, h * 0.5, s, s * STROKE, color);
    };

    Skycons.CLEAR_NIGHT = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        moon(ctx, t, w * 0.5, h * 0.5, s, s * STROKE, color);
    };

    Skycons.PARTLY_CLOUDY_DAY = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        sun(ctx, t, w * 0.625, h * 0.375, s * 0.75, s * STROKE, color);
        cloud(ctx, t, w * 0.375, h * 0.625, s * 0.75, s * STROKE, color);
    };

    Skycons.PARTLY_CLOUDY_NIGHT = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        moon(ctx, t, w * 0.667, h * 0.375, s * 0.75, s * STROKE, color);
        cloud(ctx, t, w * 0.375, h * 0.625, s * 0.75, s * STROKE, color);
    };

    Skycons.CLOUDY = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        cloud(ctx, t, w * 0.5, h * 0.5, s, s * STROKE, color);
    };

    Skycons.RAIN = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        rain(ctx, t, w * 0.5, h * 0.37, s * 0.9, s * STROKE, color);
        cloud(ctx, t, w * 0.5, h * 0.37, s * 0.9, s * STROKE, color);
    };

    Skycons.SLEET = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        sleet(ctx, t, w * 0.5, h * 0.37, s * 0.9, s * STROKE, color);
        cloud(ctx, t, w * 0.5, h * 0.37, s * 0.9, s * STROKE, color);
    };

    Skycons.SNOW = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        snow(ctx, t, w * 0.5, h * 0.37, s * 0.9, s * STROKE, color);
        cloud(ctx, t, w * 0.5, h * 0.37, s * 0.9, s * STROKE, color);
    };

    Skycons.WIND = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h);

        swoosh(ctx, t, w * 0.5, h * 0.5, s, s * STROKE, 0, 2, color);
        swoosh(ctx, t, w * 0.5, h * 0.5, s, s * STROKE, 1, 2, color);
    };

    Skycons.FOG = function(ctx, t, color) {
        var w = ctx.canvas.width,
            h = ctx.canvas.height,
            s = Math.min(w, h),
            k = s * STROKE;

        fogbank(ctx, t, w * 0.5, h * 0.32, s * 0.75, k, color);

        t /= 5000;

        var a = Math.cos((t       ) * TAU) * s * 0.02,
            b = Math.cos((t + 0.25) * TAU) * s * 0.02,
            c = Math.cos((t + 0.50) * TAU) * s * 0.02,
            d = Math.cos((t + 0.75) * TAU) * s * 0.02,
            n = h * 0.936,
            e = Math.floor(n - k * 0.5) + 0.5,
            f = Math.floor(n - k * 2.5) + 0.5;

        ctx.strokeStyle = color;
        ctx.lineWidth = k;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        line(ctx, a + w * 0.2 + k * 0.5, e, b + w * 0.8 - k * 0.5, e);
        line(ctx, c + w * 0.2 + k * 0.5, f, d + w * 0.8 - k * 0.5, f);
    };

    Skycons.prototype = {
        _determineDrawingFunction: function(draw) {
            if(typeof draw === "string")
                draw = Skycons[draw.toUpperCase().replace(/-/g, "_")] || null;

            return draw;
        },
        add: function(el, draw) {
            var obj;

            if(typeof el === "string")
                el = document.getElementById(el);

            // Does nothing if canvas name doesn't exists
            if(el === null)
                return;

            draw = this._determineDrawingFunction(draw);

            // Does nothing if the draw function isn't actually a function
            if(typeof draw !== "function")
                return;

            obj = {
                element: el,
                context: el.getContext("2d"),
                drawing: draw
            };

            this.list.push(obj);
            this.draw(obj, KEYFRAME);
        },
        set: function(el, draw) {
            var i;

            if(typeof el === "string")
                el = document.getElementById(el);

            for(i = this.list.length; i--; )
                if(this.list[i].element === el) {
                    this.list[i].drawing = this._determineDrawingFunction(draw);
                    this.draw(this.list[i], KEYFRAME);
                    return;
                }

            this.add(el, draw);
        },
        remove: function(el) {
            var i;

            if(typeof el === "string")
                el = document.getElementById(el);

            for(i = this.list.length; i--; )
                if(this.list[i].element === el) {
                    this.list.splice(i, 1);
                    return;
                }
        },
        draw: function(obj, time) {
            var canvas = obj.context.canvas;

            if(this.resizeClear)
                canvas.width = canvas.width;

            else
                obj.context.clearRect(0, 0, canvas.width, canvas.height);

            obj.drawing(obj.context, time, this.color);
        },
        play: function() {
            var self = this;

            this.pause();
            this.interval = requestInterval(function() {
                var now = Date.now(),
                    i;

                for(i = self.list.length; i--; )
                    self.draw(self.list[i], now);
            }, 1000 / 60);
        },
        pause: function() {
            var i;

            if(this.interval) {
                cancelInterval(this.interval);
                this.interval = null;
            }
        }
    };

    global.Skycons = Skycons;
}(this));
