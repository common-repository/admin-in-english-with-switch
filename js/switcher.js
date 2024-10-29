function jsEnableEng(flag) {
	jQuery.post(ajaxurl, 
			   {
			      'action': 'enable_eng',
			      'data': flag
			   }, 
			   function(response){
			       location.reload();
			   }
	);
}
