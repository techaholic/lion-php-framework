var __WindowComponent = Class.create({

    initialize: function(name, className) {
        this.name      = name;
        this.className = className;
        this.width     = 150;
        this.height    = 150;
        this.title     = null;
        this.url       = null;
        this.content   = null;
    },
    
    show: function(data) {
        if(window[this.name] == null) {
            window[this.name] = new Window({className: this.className});
            window[this.name].setDestroyOnClose();
            window[this.name].showCenter();
            window[this.name].setTitle(this.title);
            if(this.url != null) {
                window[this.name].setUrl(this.url);
            }
            else if(this.content != null) {
                window[this.name].setHTMLContent(this.content);
            }
            window[this.name].refresh();
            window[this.name].setSize(this.width, this.height); 
            window[this.name].toFront();
        }
    },
    
    setTitle: function(title) {
        if(window[this.name] != null) {
            window[this.name].setTitle(title);
        }
        this.title = title;  
    },
    
    getTitle: function() {
        return this.title;
    },
    
    setWidth: function(width) {
        if(window[this.name] != null) {
            window[this.name].setSize(width, this.height);
        }
        this.width = width;
    },

    getWidth: function() {
        return this.width;
    },
    
    setHeidht: function(height) {
        if(window[this.name] != null) {
            window[this.name].setSize(this.width, height);
        }
        this.height = height;
    },
    
    getHeight: function() {
        return this.height;
    },
    
    setUrl: function(url) {
        if(window[this.name] != null) {
            window[this.name].setURL(url);
            window[this.name].refresh();
        }
       this.url = url;
    },
    
    getUrl: function() {
        return this.url;  
    },

    setContent: function(content) {
        if(window[this.name] != null) {
            window[this.name].setHTMLContent(content);
            window[this.name].refresh();
        }
        this.content = content;
    },
    
    getContent: function() {
        return this.content;  
    }
    
});
