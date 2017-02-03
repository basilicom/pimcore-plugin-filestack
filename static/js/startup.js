pimcore.registerNS("pimcore.plugin.filestack");

function loadScript(url, callback)
{
    // Adding the script tag to the head as suggested before
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    // Then bind the event to the callback function.
    // There are several events for cross browser compatibility.
    script.onreadystatechange = callback;
    script.onload = callback;

    // Fire the loading
    head.appendChild(script);
}

pimcore.plugin.filestack = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.filestack";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
        loadScript("https://api.filestackapi.com/filestack.js", function(){
            //alert("wait");
        });

    },
 
    pimcoreReady: function (params,broker){

        Ext.Ajax.request({
            url: "/plugin/Filestack/admin/getapikey",
            success: function(data) {
                var response = JSON.parse(data.responseText);

                if (response.success == false) {
                    alert(response.message);
                } else {
                    filepicker.setKey(response.apiKey);
                }

            }
        });

    },
    
    postOpenAsset: function(asset) {
        
        //alert('post open asset!');

        var self = this;
        if (asset.type == 'folder') {
            index = 8;

            asset.toolbar.insert(index, {
                text: '',
                itemId: 'filestack',
                iconCls: "filestack_icon_upload",
                tooltip: 'Upload via Filestack',
                scale: 'medium',
                handler: function(button) {

                    pimcore.helpers.loadingShow();

                        filepicker.pickMultiple(
                          {
                            mimetype: '*/*',
                            maxFiles: 100,
                            container: 'window',
                            services: ['COMPUTER', 'FACEBOOK', 'INSTAGRAM', 'GOOGLE_DRIVE', 'DROPBOX', 'PICASA', 'WEBCAM', 'FLICKR', 'IMAGE_SEARCH']
                          },
                          function(Blob){

                            var addChildProgressBar = new Ext.ProgressBar({
                                text: t('initializing')
                            });
                    
                            this.addChildWindow = new Ext.Window({
                                layout:'fit',
                                width:500,
                                bodyStyle: "padding: 10px;",
                                closable:false,
                                plain: true,
                                modal: true,
                                items: [addChildProgressBar]
                            });
                    
                            pimcore.helpers.loadingHide();
                            this.addChildWindow.show();
                            
                            var currentStep = 0;
                            var steps = Blob.length;

                            Ext.Array.each(Blob, function(record) {

                                currentStep++;

                                 var status = currentStep / steps;

                                addChildProgressBar.updateProgress(status, status + "%");

                                Ext.Ajax.request({
                                    url: "/plugin/Filestack/admin/upload",
                                    success: function(data) {
                                        var response = JSON.parse(data.responseText);
                                        
                                        if (response.success == false) {
                                            alert(response.message);
                                        }
                
                                    },
                                    params: {
                                        assetFolderId: asset.id,
                                        url: record.url,
                                        filename: record.filename
                                    }
                                });
                            });
                            this.addChildWindow.close();

                            asset.reload();
                            
                            
                          },
                          function(FPError){
                            pimcore.helpers.loadingHide();
                            alert(FPError.toString());
                    });
                    
                }

            });
        }
    }
    
});

var filestackPlugin = new pimcore.plugin.filestack();

