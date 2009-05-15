/*
 * Tine 2.0
 * 
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Philipp Schuele <p.schuele@metaways.de>
 * @copyright   Copyright (c) 2009 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id$
 *
 */
Ext.namespace('Tine.widgets', 'Tine.widgets.tree');

/**
 * returns generic tree context menu with
 * - create/add
 * - rename
 * - delete
 * - edit grants
 * 
 * ctxNode class var is required in calling class
 */
Tine.widgets.tree.ContextMenu = {
	
    /**
     * create new Ext.menu.Menu with actions
     * 
     * @param {} config has the node name, actions, etc.
     * @return {}
     */
	getMenu: function(config) {
        
        /***************** define action handlers *****************/
        var handler = {
            /**
             * create
             */
            addNode: function() {
                //console.log('add');
                //console.log(this.ctxNode);
                Ext.MessageBox.prompt(String.format(config.il8n._('New {0}'), config.nodeName), String.format(config.il8n._('Please enter the name of the new {0}:'), config.nodeName), function(_btn, _text) {
                    if( this.ctxNode && _btn == 'ok') {
                        if (! _text) {
                            Ext.Msg.alert(String.format(config.il8n._('No {0} added'), config.nodeName), String.format(config.il8n._('You have to supply a {0} name!'), config.nodeName));
                            return;
                        }
                        Ext.MessageBox.wait(config.il8n._('Please wait'), String.format(config.il8n._('Creating {0}...' ), config.nodeName));
                        var parentNode = this.ctxNode;
                        
                        var params = {
                            method: config.backend + '.add' + config.backendModel,
                            name: _text
                        };
                        
                        // TODO try to generalize this
                        if (config.backendModel == 'Container') {
                            params.application = this.appName;
                            params.containerType = parentNode.attributes.containerType;
                            
                        } else if (config.backendModel == 'Folder') {
                            params.parent = parentNode.attributes.globalname;
                            params.accountId = node.attributes.account_id;
                        }
                        
                        Ext.Ajax.request({
                            params: params,
                            scope: this,
                            success: function(_result, _request){
                                var nodeData = Ext.util.JSON.decode(_result.responseText);
                                var newNode = this.loader.createNode(nodeData);
                                parentNode.appendChild(newNode);
                                if (config.backendModel == 'Container') {
                                    this.fireEvent('containeradd', nodeData);
                                }
                                Ext.MessageBox.hide();
                            }
                        });
                        
                    }
                }, this);
            },
            
            /**
             * delete
             */
            deleteNode: function() {
                if (this.ctxNode) {
                    var node = this.ctxNode;
                    Ext.MessageBox.confirm(config.il8n._('Confirm'), String.format(config.il8n._('Do you really want to delete the {0} "{1}"?'), config.nodeName, node.text), function(_btn){
                        if ( _btn == 'yes') {
                            Ext.MessageBox.wait(config.il8n._('Please wait'), String.format(config.il8n._('Deleting {0} "{1}"' ), config.nodeName , node.text));
                            
                            Ext.Ajax.request({
                                params: {
                                    method: config.backend + '.delete' + config.backendModel,
                                    containerId: node.attributes.container.id
                                },
                                scope: this,
                                success: function(_result, _request){
                                    if(node.isSelected()) {
                                        this.getSelectionModel().select(node.parentNode);
                                        this.fireEvent('click', node.parentNode);
                                    }
                                    node.remove();
                                    if (config.backendModel == 'Container') {
                                        this.fireEvent('containerdelete', node.attributes.container);
                                    }
                                    Ext.MessageBox.hide();
                                }
                            });
                        }
                    }, this);
                }
            },
            
            /**
             * rename
             */
            renameNode: function() {
                if (this.ctxNode) {
                    var node = this.ctxNode;
                    Ext.MessageBox.show({
                        title: 'Rename ' + config.nodeName,
                        msg: String.format(config.il8n._('Please enter the new name of the {0}:'), config.nodeName),
                        buttons: Ext.MessageBox.OKCANCEL,
                        value: node.text,
                        fn: function(_btn, _text){
                            if (_btn == 'ok') {
                                if (! _text) {
                                    Ext.Msg.alert(String.format(config.il8n._('Not renamed {0}'), config.nodeName), String.format(config.il8n._('You have to supply a {0} name!'), config.nodeName));
                                    return;
                                }
                                Ext.MessageBox.wait(config.il8n._('Please wait'), String.format(config.il8n._('Updating {0} "{1}"'), config.nodeName, node.text));
                                
                                var params = {
                                    method: config.backend + '.rename' + config.backendModel,
                                    newName: _text
                                };
                                
                                if (config.backendModel == 'Container') {
                                    params.containerId = node.attributes.container.id;
                                }
                                
                                Ext.Ajax.request({
                                    params: params,
                                    scope: this,
                                    success: function(_result, _request){
                                        var container = Ext.util.JSON.decode(_result.responseText);
                                        node.setText(_text);
                                        if (config.backendModel == 'Container') {
                                            this.fireEvent('containerrename', container);
                                        }
                                        Ext.MessageBox.hide();
                                    }
                                });
                            }
                        },
                        scope: this,
                        prompt: true,
                        icon: Ext.MessageBox.QUESTION
                    });
                }
            },
            
            // TODO  generalize that?
            managePermissions: function() {
                if (this.ctxNode) {
                    var node = this.ctxNode;
                    var window = new Ext.ux.PopupWindow({
                        url: 'index.php',
                        name: 'TinebaseManageContainerGrants' + node.attributes.container.id,
                        layout: 'fit',
                        modal: true,
                        width: 700,
                        height: 450,
                        title: String.format(_('Manage Permissions for {0} "{1}"'), config.nodeName, Ext.util.Format.htmlEncode(node.attributes.container.name)),
                        contentPanelConstructor: 'Tine.widgets.container.grantDialog',
                        contentPanelConstructorConfig: {
                            containerName: config.nodeName,
                            grantContainer: node.attributes.container
                        }
                    });
                }
            }
        }
        
        /****************** create ITEMS array ****************/
        
        var items = [];
        for (var i=0; i < config.actions.length; i++) {
            switch(config.actions[i]) {
                case 'add':
                    items.push(new Ext.Action({
                        text: String.format(config.il8n._('Add {0}'), config.nodeName),
                        iconCls: 'action_add',
                        handler: handler.addNode,
                        scope: config.scope
                    }));
                    break;
                case 'delete':
                    items.push(new Ext.Action({
                        text: String.format(config.il8n._('Delete {0}'), config.nodeName),
                        iconCls: 'action_delete',
                        handler: handler.deleteNode,
                        scope: config.scope
                    }));
                    break;
                case 'rename':
                    items.push(new Ext.Action({
                        text: String.format(config.il8n._('Rename {0}'), config.nodeName),
                        iconCls: 'action_rename',
                        handler: handler.renameNode,
                        scope: config.scope
                    }));
                    break;
                case 'grants':
                    items.push(new Ext.Action({
                        text: config.il8n._('Manage permissions'),
                        iconCls: 'action_managePermissions',
                        handler: handler.managePermissions,
                        scope: config.scope
                    }));
                    break;
                default:
                    // TODO add custom actions
            }
        }

        /******************* return menu **********************/
        
        return new Ext.menu.Menu({
		    items: items
		});
	}
};
