$(document).ready(function() {
	var productElement = $("[data-product-id]");
	var productElementCount = productElement.length;
	var productIdList = Array();
	if(productElementCount > 0) {
		$.each(productElement, function(k, v) {
			productIdList.push($(v).attr('data-product-id'));
		});

		$.get("http://tracking.api.thitruongsi.com:8002/tracking", {"act":"impr", "obj_type":"product", "obj_ids":JSON.stringify(productIdList)}, function(resp) {
			
		}, 'json');
	}
});