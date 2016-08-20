var SalonManagement = Class.create();
SalonManagement.prototype = {
	deleteUrl: null,
	approveUrl: null,
	messageDelete: null,
	messageApprove: null,
	okLabel: null,
	cancelLabel: null,
	salonId: null,
	eleUpdate: null,
	initialize: function( deleteUrl, approveUrl, messageDelete, messageApprove, okLabel, cancelLabel){
		me = this;
		me.deleteUrl = deleteUrl;
		me.approveUrl = approveUrl;
		me.messageDelete = messageDelete;
		me.messageApprove = messageApprove;
		$$('a[href="#salore-salon-approve"]').each(function(item){
			Event.observe(item, 'click', me.onClick.bind(me, item));
		});
		$$('a[href="#salore-salon-delete"]').each(function(item){
			Event.observe(item, 'click', me.onClick.bind(me, item));
		});
	},
	onClick: function(obj)
	{
		if(obj.tagName == 'TR')
		{
			 this.salonId = obj.title;
			 this.eleUpdate = obj;
		}
		else if(obj.tagName == 'A')
		{
			this.salonId = obj.up(1).title;
			this.eleUpdate = obj.up(1);
		}
		if(obj.hasClassName('salore-salon-approve'))
		{
			this.showApproveDialog(obj);
		}
		else
		{
			this.showDeleteDialog();
		}
		return;
	},
	showApproveDialog: function(){
		me = this;
		Dialog.confirm(
			me.messageApprove,
			{
				className:'alphacube', 
				width: 250, 
				height: 100, 
				closable:true,
				okLabel: 'Yes',
				cancelLabel: 'No',
				onOk: function(){
					me.ajaxApprove();
					this.close();
				}
			}
		);
	},
	showDeleteDialog: function(obj)
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
					onOk: function(){
						me.ajaxDelete();
						this.close();
					}
				}
		);
	},
	ajaxDelete: function(){
		me = this;
		new Ajax.Request(me.deleteUrl, {
			  method:'post',
			  evalJSON:'force',
			  parameters:{salonId: me.salonId},
			  onSuccess: function(transport){
				  var json = transport.responseJSON;
				  if(json.status == 'SUCCESS')
				  {
					  alert(json.message);
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
	ajaxApprove: function()
	{
		me = this;
		new Ajax.Request(me.approveUrl, {
			  method:'post',
			  evalJSON:'force',
			  parameters:{salonId: me.salonId},
			  onSuccess: function(transport){
				  var json = transport.responseJSON;
				  if(json.status == 'APPROVE')
				  {
					  me.updateOnComplete('Unapprove');
					  alert(json.message);
				  }
				  else if(json.status =='UNAPPROVE')
				 {
					  me.updateOnComplete('Approve');
					  alert(json.message);
				 }
				  else
				  {
					  alert(json.message);
					  return;
				  }
			   }
			});
	},
	updateOnComplete: function(textA)
	{
		this.eleUpdate.setStyle({
			backgroundColor: '#ccc'
		});
		var childTds = me.eleUpdate.childElements('td');
		childTds.each(function(e){
			var element = $(e);
			if( element.down() && element.down().hasClassName('salore-salon-approve') )
			{
				element.down().update(textA);
			}
		});
	}
}