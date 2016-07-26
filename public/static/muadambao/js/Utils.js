var Utils = {
	signless:function(str){
		return str.toLowerCase()
			.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a")
			.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e")
			.replace(/ì|í|ị|ỉ|ĩ/g,"i")
			.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o")
			.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u")
			.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y")
			.replace(/đ/g,"d");
	},
	// require jquery.numberformatter-1.2.4, jquery number for mat require jshashset
	formatNumberVND:function (e) {
	    e.parseNumber({
	        format: "#,##0",
	        locale: "us"
	    });
	    e.formatNumber({
	        format: "#,##0",
	        locale: "us"
	    });
	},

	// update index of table
	updateTableIndex:function(tableE,rowSlt){
		var elms = null;
		if(rowSlt){
			elms = tableE.find(rowSlt);
		}else{
			elms = tableE.find('tr td:first-child');
		}
		elms.each(function(index){
			$(this).html(index+1);
		});
	}
}