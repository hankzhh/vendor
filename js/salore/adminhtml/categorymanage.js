var CategoryManagement = Class.create();
CategoryManagement.prototype = {
		itemId: null,
		editUrl: null,
		deleteUrl: null,
		messageDelete: null,
		okLabel: 'Yes',
		cancelLabel: 'No',
		eleUpdate: null,
		initialize: function( editUrl , deleteUrl , messageDelete , okLabel , cancelLabel)
		{
			me = this;
			me.editUrl = editUrl;
			me.deleteUrl = deleteUrl;
			me.messageDelete = messageDelete;
			me.okLabel = okLabel;
			me.cancelLabel = cancelLabel;
		$$('a[href="#salon-category-edit"]').each(function(item){
			Event.observe(item, 'click', me.onClick.bind(me, item));
		});
		$$('a[href="#salon-category-delete"]').each(function(item){
			Event.observe(item, 'click', me.onClick.bind(me, item));
		});
		
	},
	onClick: function(obj)
	{
		if(obj.tagName == 'TR')
		{
			this.itemId= obj.title;
			 this.eleUpdate = obj;
		}
		else if(obj.tagName == 'A')
		{
			this.itemId = obj.up(1).title;
			 this.eleUpdate = obj.up(1);
		}
		if(obj.hasClassName('salon-category-edit'))
		{
			this.edit(obj);
		}
		if(obj.hasClassName('salon-category-delete'))
		{
			this.showDeleteDialog();
		}
		return;
	},
	edit: function(obj)
	{
		window.location.href = this.editUrl + 'id/' + this.itemId;
	},
	showDeleteDialog: function()
	{
		me = this;
		Dialog.confirm(
				me.messageDelete,
				{
					className:'alphacube', 
					width: 250, 
					height: 100, 
					closable:true,
					okLabel: 'Yes',
					cancelLabel: 'No',
					onOk: function()
					{
						me.ajaxdelete();
						this.close();
					}
				}
		);
	},
	ajaxdelete: function(){
		me = this;
		new Ajax.Request(me.deleteUrl, {
			  method:'post',
			  evalJSON:'force',
			  parameters: {'id': me.itemId},
			  onSuccess: function(transport){
				  var json = transport.responseJSON;
				  if(json.status == 'SUCCESS')
				  {
					  me.deleteOnComplete();
				  }
				 else
				  {
					  alert(json.message);
					  return;
				  }
			   }
			});
	},
	deleteOnComplete: function(){
		this.eleUpdate.remove();
	},
}