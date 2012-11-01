//function ajaxHandler(result, url, alertError)
function ajaxHandler(result) {
	var result = result.split('|');
	var title  = result[0].trim();
	var info   = result[1];
	switch (title) {
		case 'success':
			url = info;
			//??
			if(url) window.location.href = url;
			return true;
		case 'alert':
			alert(info);
			return false;
		default:
			return false;
	}
}
///*
// * url: string to header
// */
//function fun(url) {
//	$.post(url, {} ,function(result) {
//      	if(ajaxHandler(result))    return;
//    });
