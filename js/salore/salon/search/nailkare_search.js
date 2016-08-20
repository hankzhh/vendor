var NailkareSearch = Class.create();
NailkareSearch.prototype = {
	  solrData : ["Saab", "Volvo", "BMW"],
	  element: null,
	  inputVal: null,
      initialize: function(){
    	 var me = this;
    	 me.element = $('dealerlocator_postcode');
    	 me.element.setAttribute('autocomplete','off');
    	 Event.observe(this.element, 'click', this.load.bind(me));
    	 Event.observe(this.element, 'keyup', this.load.bind(me));
    	 this.killerFn = function(e) {
		      if (!$(Event.element(e)).up('.input-box')) {
		        me.killSuggestions();
		        me.disableKillerFn();
		      }
		    } .bindAsEventListener(this);
      }, 
      enableKillerFn: function() {
		    Event.observe(document.body, 'click', this.killerFn);
	  },
	  disableKillerFn: function() {
			Event.stopObserving(document.body, 'click', this.killerFn);
	  },
	  killSuggestions: function() {
			this.stopKillSuggestions();
			if($('search-popup') != null)
			{
				this.intervalId = window.setInterval(function() { $('search-popup').hide(); this.stopKillSuggestions(); } .bind(this), 1);
			}
		},
	  stopKillSuggestions: function() {
		    window.clearInterval(this.intervalId);
	  },
      load: function(me) {
    	  	me = this;
    	  if(me.element.value != "")
		  {
				var searchUrl = 'http://dev.nailkare.com/sb.php?q=' + me.element.value;
				new Ajax.JSONRequest(searchUrl, {
					callbackParamName : "json.wrf",
					onComplete : function(response) {
						var json = response.responseJSON;
						var docs = json.response.docs;
						var keywords = json.keywordssuggestions;
						if(keywords.length > 0)
						{
							if(document.getElementById('search-popup'))
							{
								$('search-popup').remove();
							}
							var parentElement = me.element.up(1);
							var divPopup = new Element('div', {id:'search-popup'}).addClassName('search-selector-popup');
							divPopup.update('');
							var ulElement = new Element('ul').addClassName('list-unstyled');
							var suggestions = [];
							keywords.each(function(keyword){
								var liElement = new Element('li').update(keyword);
								Event.observe(liElement, 'click', function(){
									me.element.value = this.innerHTML;
									//me.element.focus(); 
									me.element.setAttribute('value', this.innerHTML);
									$('search-popup').remove();
								});
								ulElement.appendChild(liElement);
							});
							/*
							docs.each(function(doc){
								var liElement = new Element('li').update(doc.address_varchar);
								ulElement.appendChild(liElement);
							});*/
							divPopup.appendChild(ulElement);
							parentElement.appendChild(divPopup);
							Event.observe(me.element, 'blur', me.enableKillerFn.bind(me));
						}
					}
				});
		  }
    	  else
    	  {
    		  if(document.getElementById('search-popup'))
				{
					$('search-popup').remove();
				}
    	   }
      },
}
