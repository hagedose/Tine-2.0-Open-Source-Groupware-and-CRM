/**
 * @class Ext.ux.Printer.BaseRenderer
 * @extends Object
 * @author Ed Spencer
 * Abstract base renderer class. Don't use this directly, use a subclass instead
 */
Ext.ux.Printer.BaseRenderer = Ext.extend(Object, {
  /**
   * @cfg {String} printStrategy window or iframe
   */
  printStrategy: 'iframe',

  /**
   * @cfg {Boll} useHtml2Canvas
   */
  useHtml2Canvas: true,
  
  debug: false,
  
  constructor: function(config) {
    Ext.apply(this, config);

    Ext.ux.Printer.BaseRenderer.superclass.constructor.call(this, config);
  },

  /**
   * template method to intercept when document is ready
   *
   * @param {Document} document
   * @pram {Ext.Component} component
   */
  onBeforePrint: Ext.emptyFn,

  /**
   * Prints the component
   * @param {Ext.Component} component The component to print
   */
  print: function(component) {
    this.mask = new Ext.LoadMask(Ext.getBody(), {msg: i18n._("Preparing print, please wait...")});
    this.mask.show();

    return this[this.printStrategy + 'Print'](component);
  },
  
  /**
   * Prints the component using the new window strategy
   * @param {Ext.Component} component The component to print
   */
  windowPrint: function(component) {
    var name = component && component.getXType
             ? String.format("print_{0}_{1}", String(component.getXType()).replace(/(\.|-)/g, '_'), component.id.replace(/(\.|-)/g, '_'))
             : "print";

    var win = window.open('', name);
    
    win.document.write(this.generateHTML(component));
    win.document.close();

    // gecko looses its document after document.close(). but fortunally waits with printing till css is loaded itself
    return this.doPrint(win);
    
    this.doPrintOnStylesheetLoad.defer(10, this, [win, component]);
  },
  
  /**
   * Prints the component using the hidden iframe strategy
   * @param {Ext.Component} component The component to print
   */
  iframePrint: function(component) {
    var id = Ext.id(),
    doc = document,
    frame = doc.createElement('iframe'),
    style = {
      position: 'absolute',
      'background-color': '#FFFFFF',
      width: '210mm',
      height: '297mm',
      top: '-10000px',
      left: '-10000px'
    };

    if (this.debug) {
      Ext.apply(style, {
        top: '0px',
        left: '0px',
        'z-index': 10000000
      });
    }

    Ext.fly(frame).set({
      id: id,
      name: id,
      style: style
    });
    
    doc.body.appendChild(frame);

    Ext.fly(frame).set({
      src : Ext.SSL_SECURE_URL
    });

    var doc = frame.contentWindow.document || frame.contentDocument || WINDOW.frames[id].document;
        
    doc.open();
    doc.write(this.generateHTML(component));
    doc.close();

    this.doPrintOnStylesheetLoad.defer(10, this, [frame.contentWindow, component]);
  },
  
  /**
   * check if style is loaded and do print afterwards
   * 
   * @param {window} win
   */
  doPrintOnStylesheetLoad: function(win, component) {
    var el = win.document.getElementById('csscheck'),
        comp = el.currentStyle || getComputedStyle(el, null);
    if (comp.display !== "none") {
      return this.doPrintOnStylesheetLoad.defer(10, this, [win, component]);
    }

    this.onBeforePrint(win.document, component);

    this.doPrint(win);
  },

  doPrint: function(win) {
    if (this.useHtml2Canvas) {
      var me = this;

      var canvas = win.document.createElement("canvas");
      canvas.width = win.innerWidth;
      canvas.height = win.innerHeight;

      me.setDPI(canvas, 300);

      html2canvas(win.document.body, {
        canvas: canvas,
        grabMouse: false,
        onrendered: function (canvas) {
          var screenshot = canvas.toDataURL();
          me.useHtml2Canvas = false;
          win.document.body.innerHTML = '<img style="display: block; width: 100%" />';
          win.document.body.firstChild.onload = me.doPrint.createDelegate(me, [win]);
          win.document.body.firstChild.src = screenshot;
        }
      });
    } else {
      win.print();
      this.mask.hide();
      if (!this.debug) {
        win.close();
      }
    }
  },

  setDPI: function (canvas, dpi) {
    // Set up CSS size if it's not set up already
    if (!canvas.style.width)
      canvas.style.width = canvas.width + 'px';
    if (!canvas.style.height)
      canvas.style.height = canvas.height + 'px';

    var scaleFactor = dpi / 96;
    canvas.width = Math.ceil(canvas.width * scaleFactor);
    canvas.height = Math.ceil(canvas.height * scaleFactor);
    var ctx = canvas.getContext('2d');
    ctx.scale(scaleFactor, scaleFactor);
  },

  /**
   * Generates the HTML Markup which wraps whatever this.generateBody produces
   * @param {Ext.Component} component The component to generate HTML for
   * @return {String} An HTML fragment to be placed inside the print window
   */
  generateHTML: function(component) {
    return new Ext.XTemplate(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
      '<html>',
        '<head>',
          '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />',
          this.getAdditionalHeaders(),
          '<link href="' + this.stylesheetPath + '?' + new Date().getTime() + '" rel="stylesheet" type="text/css" media="screen,print" />',
          '<title>' + this.getTitle(component) + '</title>',
        '</head>',
        '<body>',
          '<div id="csscheck"></div>',
          this.generateBody(component),
        '</body>',
      '</html>'
    ).apply(this.prepareData(component));
  },
  
  /**
   * Returns the HTML that will be placed into the <head> element of th print window.
   * @param {Ext.Component} component The component to render
   * @return {String} The HTML fragment to place inside the print window's <head> element
   */
  getAdditionalHeaders: function(component) {
    return '';
  },
  /**
   * Returns the HTML that will be placed into the print window. This should produce HTML to go inside the
   * <body> element only, as <head> is generated in the print function
   * @param {Ext.Component} component The component to render
   * @return {String} The HTML fragment to place inside the print window's <body> element
   */
  generateBody: Ext.emptyFn,
  
  /**
   * Prepares data suitable for use in an XTemplate from the component 
   * @param {Ext.Component} component The component to acquire data from
   * @return {Array} An empty array (override this to prepare your own data)
   */
  prepareData: function(component) {
    return component;
  },
  
  /**
   * Returns the title to give to the print window
   * @param {Ext.Component} component The component to be printed
   * @return {String} The window title
   */
  getTitle: function(component) {
    return typeof component.getTitle == 'function' ? component.getTitle() : (component.title || "Printing");
  },
  
  /**
   * @property stylesheetPath
   * @type String
   * The path at which the print stylesheet can be found (defaults to 'stylesheets/print.css')
   */
  stylesheetPath: 'stylesheets/print.css'
});
